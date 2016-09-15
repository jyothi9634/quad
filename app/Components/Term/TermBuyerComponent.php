<?php
namespace App\Components\Term;

use App\Components\CommonComponent;
use App\Models\TermBuyerQuote;
use App\Models\TermBuyerQuoteItem;
use App\Models\TermBuyerQuoteSelectedSeller;
use App\Models\TermBuyerQuoteSlab;
use App\Models\TermBuyerBidDate;
use App\Models\TermBuyerQuoteBidTermsFile;
use App\Models\TermContract;
use App\Models\TermBuyerQuoteSellersQuotesPrice;
use App\Models\TermContractsIndentQuantitie;
use App\Models\RelocationtermBuyerPostIndentParticular;
use App\Components\MessagesComponent;
use DB;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\BuyerComponent;
use App\Components\Matching\BuyerMatchingComponent;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use ZipArchive;

class TermBuyerComponent {
	
	/*Create post creation save function
	 * 
	 */
	public static function TermBuyerCreateQuote($serviceId, $allRequestdata, $postType) {
		
		
		try {
		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];		
		
		$created_year = date('Y');
		$serviceId = Session::get('service_id');
		$ordid  =   CommonComponent::getTermPostID();		
	
		if ($serviceId == ROAD_FTL) {		
			$trans_randid = 'FTL_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);	
			$servicename = 'FTL TERM';
		} elseif($serviceId==ROAD_PTL){
			$trans_randid = 'PTL_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'LTL TERM';
		} elseif($serviceId==RAIL){
			$trans_randid = 'RAIL_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'RAIL TERM';
		} elseif($serviceId==AIR_DOMESTIC){
			$trans_randid = 'AIR_DOMESTIC_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'AIR DOMESTIC TERM';
		} elseif($serviceId==AIR_INTERNATIONAL){
			$trans_randid = 'AIR_INTERNATIONAL_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'AIR INTERNATIONAL TERM';
		}
		elseif($serviceId==COURIER){
			$trans_randid = 'COURIER_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'COURIER TERM';
		}elseif($serviceId==OCEAN){
			$trans_randid = 'OCEAN_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
			$servicename = 'OCEAN TERM';
		}

		
		
		if (isset($_REQUEST['quoteaccess_id']) && !empty($_REQUEST['quoteaccess_id']) ) {
			$is_private = $_REQUEST['quoteaccess_id'];
			if (isset($is_private) == '2' && !empty($is_private)) {				
				if ($allRequestdata['term_seller_list'] != "") {
					$seller_list = explode(",", $allRequestdata['term_seller_list']);
					$seller_list_count = count($seller_list);
				}
			} else {
				$is_private = 1;
			}
		}
		//echo "<pre>"; print_r($allRequestdata); die;
		//echo $allRequestdata['term_courier_types'][0]; 
		//exit;
		$base_dir = 'uploads/buyer/'.Auth::id().'/Terms/' ;
		if (!is_dir ( $base_dir )) {
			mkdir ( $base_dir, 0777, true );
		}
		//check post type confirm or draft
		if (!empty($allRequestdata['confirm_but']) && isset($allRequestdata['confirm_but'])) {
			$postStatus= OPEN;
		} else {
			$postStatus= SAVEDASDRAFT;
		}			
		
		/*
		 * Term Quote insert main quotes table data
		 */
		$buyerQuote = new TermBuyerQuote();
		$buyerQuote->lkp_service_id = $serviceId;
		$buyerQuote->lkp_lead_type_id = $postType;
		$buyerQuote->lkp_quote_access_id = $is_private;
		$buyerQuote->transaction_id = $trans_randid;
		$buyerQuote->from_date = CommonComponent::convertDateForDatabase($allRequestdata['dispatch_date'][0]);
		$buyerQuote->to_date = CommonComponent::convertDateForDatabase($allRequestdata['delivery_date'][0]);
		$buyerQuote->buyer_notes = $allRequestdata['buyer_notes'];
		$buyerQuote->lkp_bid_type_id = $allRequestdata['bid_type'];
			
		if ($serviceId == COURIER) {
			$buyerQuote->lkp_courier_delivery_type_id = $allRequestdata['term_post_delivery_type'][0];
			$buyerQuote->lkp_courier_type_id = $allRequestdata['term_courier_types'][0];
			$buyerQuote->max_weight_accepted = $allRequestdata['max_weight_accepted_text'];
			$buyerQuote->lkp_ict_weight_uom_id = $allRequestdata['units_max_weight'];
			$buyerQuote->is_incremental = $allRequestdata['check_max_weight_assign'];
			$buyerQuote->increment_weight = $allRequestdata['incremental_weight_text'];
		}	
		$buyerQuote->lkp_post_status_id = $postStatus;
		$buyerQuote->buyer_id = Auth::id();
		$buyerQuote->created_by = Auth::id();
		$buyerQuote->created_at = $created_at;
		$buyerQuote->created_ip = $createdIp;	
		
		if ($buyerQuote->save()) {
			$transactionID = $buyerQuote->transaction_id;
			
		//Targer directory for store files in particular folder
		
			
		//Save bid Data dates 
		$fromcities = array();
		$buyerBidDate = new TermBuyerBidDate();
		$buyerBidDate->term_buyer_quote_id = $buyerQuote->id;
		$buyerBidDate->bid_end_date = CommonComponent::convertDateForDatabase($allRequestdata['last_bid_date']);
		$buyerBidDate->bid_end_time = $allRequestdata['bid_close_time'];
		$buyerBidDate->is_active = 1;
		$buyerBidDate->lkp_service_id = $serviceId;
		$buyerBidDate->created_by = Auth::id();
		$buyerBidDate->created_at = $created_at;
		$buyerBidDate->created_ip = $createdIp;
		$buyerBidDate->save();		
		if(count($_FILES)>0){
			//Files save data in uploads docs
			$target_dir = 'uploads/buyer/'.Auth::id().'/Terms/'.$buyerQuote->id."/" ;
			if (!is_dir ( $target_dir )) {
				mkdir ( $target_dir, 0777, true );
			}
			$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_defualt"]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$_FILES["terms_condtion_types_term_defualt"]["size"];
			$_FILES["terms_condtion_types_term_defualt"]["name"];
			move_uploaded_file($_FILES["terms_condtion_types_term_defualt"]["tmp_name"], $target_file);
			
			$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
			$buyerBidTermFiles->term_buyer_quote_id = $buyerQuote->id;
			$buyerBidTermFiles->file_name = $_FILES["terms_condtion_types_term_defualt"]["name"];
			$buyerBidTermFiles->file_type = $imageFileType;
			$buyerBidTermFiles->file_size = $_FILES["terms_condtion_types_term_defualt"]["size"];
			$buyerBidTermFiles->file_path = $target_file;
			$buyerBidTermFiles->lkp_service_id = $serviceId;
			$buyerBidTermFiles->created_by = Auth::id();
			$buyerBidTermFiles->created_at = $created_at;
			$buyerBidTermFiles->created_ip = $createdIp;
			$buyerBidTermFiles->save();

		}
		//Docuements uploads	
		$j =1;	
		if(count($_FILES)>0){
			for($j=1;$j<=$allRequestdata['term_next_terms_count_search'];$j++){	
				if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] == '') {
					$j++;
				}
				if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] != '') {
				$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_$j"]["name"]);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				move_uploaded_file($_FILES["terms_condtion_types_term_$j"]["tmp_name"], $target_file);
				$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
				$buyerBidTermFiles->term_buyer_quote_id = $buyerQuote->id;
				$buyerBidTermFiles->file_name = $_FILES["terms_condtion_types_term_$j"]["name"];
				$buyerBidTermFiles->file_type = $imageFileType;
				$buyerBidTermFiles->file_size = $_FILES["terms_condtion_types_term_$j"]["size"];
				$buyerBidTermFiles->file_path = $target_file;
				$buyerBidTermFiles->lkp_service_id = $serviceId;
				$buyerBidTermFiles->created_by = Auth::id();
				$buyerBidTermFiles->created_at = $created_at;
				$buyerBidTermFiles->created_ip = $createdIp;
				$buyerBidTermFiles->save();
				}	
			}
		}
		if (!empty($allRequestdata['load_type'])) {
		//echo "<pre>"; print_r($allRequestdata); exit;
        $multi_data_count = count($allRequestdata['load_type']);
        for ($i = 0; $i < $multi_data_count; $i++) {
        /******Multiple insert in quote items******** */
        $Quote_Lineitems = new TermBuyerQuoteItem();
		$Quote_Lineitems->term_buyer_quote_id 	 = $buyerQuote->id;		
        $Quote_Lineitems->from_location_id = $allRequestdata['from_location'][$i];
        $Quote_Lineitems->to_location_id = $allRequestdata['to_location'][$i];
        $Quote_Lineitems->lkp_load_type_id = $allRequestdata['load_type'][$i];        
        if ($serviceId == ROAD_FTL) {
	        $Quote_Lineitems->number_loads = $allRequestdata['no_of_loads'][$i];
	        $Quote_Lineitems->units = $allRequestdata['capacity'][$i];
	        $Quote_Lineitems->lkp_vehicle_type_id = $allRequestdata['vehicle_type'][$i];
	        $Quote_Lineitems->quantity = $allRequestdata['quantity'][$i];
        }      
        if ($serviceId == COURIER) {
        	$Quote_Lineitems->number_packages = $allRequestdata['no_of_loads'][$i];
        	$Quote_Lineitems->volume = $allRequestdata['term_volume'][$i];
        }
        if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == OCEAN || $serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC) {
        	$Quote_Lineitems->lkp_packaging_type_id = $allRequestdata['package_type'][$i];
        	$Quote_Lineitems->volume = $allRequestdata['term_volume'][$i];
        	$Quote_Lineitems->number_packages = $allRequestdata['no_of_loads'][$i];
        	
        	$Quote_Lineitems->ie_code = $allRequestdata['term_iecode'][$i];
        	$Quote_Lineitems->product_made = $allRequestdata['term_product_mode'][$i];
        	$Quote_Lineitems->lkp_air_ocean_shipment_type_id = $allRequestdata['term_shipment_type'][$i];
        	$Quote_Lineitems->lkp_air_ocean_sender_identity_id = $allRequestdata['term_sender_identify'][$i];
        }        
        if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC ) {
                $Quote_Lineitems->is_door_pickup = $allRequestdata['term_door_pickup'][$i];
                $Quote_Lineitems->is_door_delivery = $allRequestdata['term_door_delivery'][$i];
        }
        $Quote_Lineitems->lkp_service_id = $serviceId;
        $Quote_Lineitems->created_by = Auth::id();
        $Quote_Lineitems->created_at = $created_at;
        $Quote_Lineitems->created_ip = $createdIp;
        $fromcities[] = $allRequestdata['from_location'][$i];
        $Quote_Lineitems->save();
         //Maintaining a log of data for buyer new quote multiple items creation
        // CommonComponent::auditLog($Quote_Lineitems->id, 'buyer_quote_items');
        }      
        
        
        //Wriiten by Ravi for slab insertion on 05-10-2016 start
        if(Session::get('service_id') == COURIER){
        	$buyerpost_lineitem_slab = new TermBuyerQuoteSlab ();
        	$buyerpost_lineitem_slab->buyer_quote_id = $buyerQuote->id;
        	$buyerpost_lineitem_slab->buyer_id = Auth::id ();
        	$buyerpost_lineitem_slab->slab_min_rate = $_POST['low_price'];
        	$buyerpost_lineitem_slab->slab_max_rate = $_POST['high_price'];
        	$created_at = date ( 'Y-m-d H:i:s' );
        	$createdIp = $_SERVER ['REMOTE_ADDR'];
        	$buyerpost_lineitem_slab->created_by = Auth::id ();
        	$buyerpost_lineitem_slab->created_at = $created_at;
        	$buyerpost_lineitem_slab->created_ip = $createdIp;
        	$buyerpost_lineitem_slab->save ();
        
        
        $low_price=1;
        $high_price=1;
       // $actual_price=1;
        for($i=1;$i<=$_POST['price_slap_hidden_value'];$i++){
        		
        	if(Session::get('service_id') == COURIER){
        		$buyerpost_lineitem_slab = new TermBuyerQuoteSlab ();
        	}
        		
        	if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] != '') {
        		$buyerpost_lineitem_slab->slab_min_rate = $_POST['low_weight_salb_'.$i];
        		$low_price++;
        	}
        	if (isset ( $_POST['low_weight_salb_'.$i] ) && $_POST['low_weight_salb_'.$i] == '') {
        		$low_price++;
        	}
        		
        	if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] != '') {
        		$buyerpost_lineitem_slab->slab_max_rate = $_POST['high_weight_slab_'.$i];
        		$high_price++;
        	}
        	if (isset ( $_POST['high_weight_slab_'.$i] ) && $_POST['high_weight_slab_'.$i] == '') {
        		$high_price++;
        	}
        	$buyerpost_lineitem_slab->buyer_quote_id = $buyerQuote->id;
        	$buyerpost_lineitem_slab->buyer_id = Auth::id ();
        	$created_at = date ( 'Y-m-d H:i:s' );
        	$createdIp = $_SERVER ['REMOTE_ADDR'];
        	$buyerpost_lineitem_slab->created_by = Auth::id ();
        	$buyerpost_lineitem_slab->created_at = $created_at;
        	$buyerpost_lineitem_slab->created_ip = $createdIp;
        	$buyerpost_lineitem_slab->save ();
        		
        }
		}
        //Wriiten by Ravi for slab insertion on 05-10-2016 start
        
       		 if ($is_private == '2') {
       		 	if ($allRequestdata['term_seller_list'] != "") {
       		 		if ($seller_list_count != 0) {
       		 			//echo $seller_list_count; exit;
       		 			for ($i = 0; $i < $seller_list_count; $i ++) {
       		 				$Quote_seller_list = new TermBuyerQuoteSelectedSeller();
       		 				$Quote_seller_list->term_buyer_quote_id = $buyerQuote->id;
       		 				$Quote_seller_list->seller_id = $seller_list[$i];
       		 				$Quote_seller_list->lkp_service_id = $serviceId;
       		 				$Quote_seller_list->created_by = Auth::id();
       		 				$Quote_seller_list->created_at = $created_at;
       		 				$Quote_seller_list->created_ip = $createdIp;
       		 				$Quote_seller_list->save();       		 
       		 				//below code  for sent mails to selelcted sellers in private post
       		 				$buyers_selected_sellers_email = DB::table('users')->where('id', $seller_list[$i])->get();
       		 				$buyers_selected_sellers_email[0]->randnumber = $trans_randid;
       		 				$buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
       		 				CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);       		 
       		 				//Maintaining a log of data for buyer new seller data multiple  creation
       		 				CommonComponent::auditLog($Quote_seller_list->id, 'buyer_quote_selected_sellers');
       		 				
       		 				if($postStatus == OPEN){
       		 				//*******Send Sms to the private Sellers***********************//
       		 				$msg_params = array(
       		 						'randnumber' => $trans_randid,
       		 						'buyername' => Auth::User()->username,
       		 						'servicename' => $servicename
       		 				);
       		 				$getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
       		 				if($getMobileNumber)
       		 				CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_TERM_SMS,$msg_params);
       		 				//*******Send Sms to the private Sellers***********************//
       		 				}
       		 			}
       		 		}
       		 	}
       		 }else{			
       		 		if($postStatus == OPEN){
			                	switch ($serviceId) {
			                		case ROAD_FTL :
			                			$servicename = 'FTL TERM';
			                			break;
			                		case ROAD_PTL :
			                			$servicename = 'PTL TERM';
			                			break;
			                		case RAIL :
			                			$servicename = 'RAIL TERM';
			                			break;
			                		case AIR_DOMESTIC :
			                			$servicename = 'AIR DOMESTIC TERM';
			                			break;
			                		case AIR_INTERNATIONAL :
			                			$servicename = 'AIR INTERNATIONAL TERM';
			                			break;
			                		case OCEAN :
			                			$servicename = 'OCEAN TERM';
			                			break;
			                		case COURIER :
			                			$servicename = 'COURIER TERM';
			                			break;
			                		default :
			                			$servicename = 'LTL TERM';
			                			break;
			                	
			                	}
                        		//*******Send Sms to the private Sellers***********************//
                        		$msg_params = array(
                        				'randnumber' => $trans_randid,
                        				'buyername' => Auth::User()->username,
                        				'servicename' => $servicename
                        		);
                        		//echo "<pre>";print_r($fromcities);exit;
                        		if($serviceId == ROAD_FTL){
                        			$getSellerIds  =   CommonComponent::getTermSellerList($fromcities);
                        		}else{
                        			$getSellerIds  =   CommonComponent::getAllSellerList($fromcities);
                        		}
                        		//echo "<pre>";print_r($getSellerIds);exit;
                        		for($i=0;$i<count($getSellerIds);$i++){	
                        			$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
                        			if($getMobileNumber)
                        			CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                        		}
                        		//*******Send Sms to the private Sellers***********************//
                        	                     
       		 }
                        }      
          }
		//making zip file start
		$documents = DB::table('term_buyer_quote_bid_terms_files')
			->where('term_buyer_quote_id', $buyerQuote->id)
			->select('file_name', 'file_path')
			->get();
		
		
		if($documents[0]->file_name!=''){
			$files = array();
			foreach($documents as $document){
				$files[] = $document->file_path;
			}
			$zippath = (isset($files['0'])) ? explode("/",$files['0']) : array();
			array_pop($zippath);
			$zippath = implode("/",$zippath);
			if(file_exists($zippath)) {
				$zipname = $zippath . '/biddocuments.zip';
				$zip = new ZipArchive;
				$zip->open($zipname, ZipArchive::CREATE);
				foreach ($files as $file) {
					$zip->addFile($file);
				}
			}
		}
		//making zip file end
          return $transactionID;
		}	
	 
	} catch (Exception $e) {
		echo 'Caught exception: ', $e->getMessage(), "\n";
	}
 }	
/* Term Buyer 
	  post lists
	 */
	public static function  getTermBuyerPostlists($serviceId,$rel_int=null){
		// Filters values to populate in the page
		//echo "ghfg";exit;
		$from_locations = array ("" => "From Location");
		if ($serviceId == COURIER) {
			$ptlCourierTypes = array ("" => "Courier Type");
			if(Session::get('delivery_type') == 2){
				$to_locations = array ("" => "To Country");
			}else{
				$to_locations = array ("" => "To Location");
			}
		}else{
			$to_locations = array ("" => "To Location");
		}
		
		$from_date = '';
		$to_date = '';
		// query to retrieve buyer posts list and bind it to the grid
		$Query = DB::table ( 'term_buyer_quotes as bqi' );
		$Query->leftjoin ( 'term_buyer_quote_items as bqit', 'bqi.id', '=', 'bqit.term_buyer_quote_id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'bqi.lkp_post_status_id' );
		if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY) {
			
		$Query->leftjoin ( 'lkp_cities as cf', 'bqit.from_location_id', '=', 'cf.id' );
		$Query->leftjoin ( 'lkp_cities as ct', 'bqit.to_location_id', '=', 'ct.id' );
		} elseif ($serviceId == ROAD_PTL || $serviceId == RAIL ||$serviceId == AIR_DOMESTIC ) {
		$Query->leftjoin ( 'lkp_ptl_pincodes as cf', 'bqit.from_location_id', '=', 'cf.id' );
		$Query->leftjoin ( 'lkp_ptl_pincodes as ct', 'bqit.to_location_id', '=', 'ct.id' );
		} elseif ($serviceId == AIR_INTERNATIONAL) {
		$Query->leftJoin('lkp_airports as cf', 'cf.id', '=', 'bqit.from_location_id');
		$Query->leftJoin('lkp_airports as ct', 'ct.id', '=', 'bqit.to_location_id');
		} elseif ($serviceId == COURIER) {
			$Query->join('lkp_ptl_pincodes as lppf', 'bqit.from_location_id', '=', 'lppf.id');
			$Query->leftjoin('lkp_ptl_pincodes as lppt', function($join)
			{
				$join->on('bqit.to_location_id', '=', 'lppt.id');
				$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(1));
			
			});
			$Query->leftjoin('lkp_countries as lppt1', function($join)
			{
				$join->on('bqit.to_location_id', '=', 'lppt1.id');
				$join->on(DB::raw('bqi.lkp_courier_delivery_type_id'),'=',DB::raw(2));
			
			});
		}
		elseif ($serviceId == OCEAN) {
		$Query->leftJoin('lkp_seaports as cf', 'cf.id', '=', 'bqit.from_location_id');
		$Query->leftJoin('lkp_seaports as ct', 'ct.id', '=', 'bqit.to_location_id');
		}		
		$Query->join ( 'term_buyer_bid_dates as tbdt', 'tbdt.term_buyer_quote_id', '=', 'bqi.id' );
		$Query->join ( 'lkp_quote_accesses as lqa', 'bqi.lkp_quote_access_id', '=', 'lqa.id' );
		$Query->groupBy('bqit.term_buyer_quote_id');
		$Query->where( 'bqi.created_by', Auth::User ()->id );
		$Query->where('bqi.lkp_post_status_id','!=',6);
		$Query->where('bqi.lkp_post_status_id','!=',7);
		$Query->where('bqi.lkp_post_status_id','!=',8);
		if ($serviceId == COURIER) {
			if (Session::has('delivery_type')) {
				$Query->where ( 'bqi.lkp_courier_delivery_type_id', '=', Session::get('delivery_type') );
			}
		}
		if ($serviceId == RELOCATION_INTERNATIONAL){
			$Query->where ( 'bqi.lkp_lead_type_id', '=', $rel_int );
		}
		if ($serviceId == ROAD_FTL) {
		$Query->where('bqi.lkp_service_id','=',ROAD_FTL);
		} elseif ($serviceId == ROAD_PTL) {
		$Query->where('bqi.lkp_service_id','=',ROAD_PTL);
		} elseif ($serviceId == RAIL) {
		$Query->where('bqi.lkp_service_id','=',RAIL);
		} elseif ($serviceId == AIR_DOMESTIC) {
		$Query->where('bqi.lkp_service_id','=',AIR_DOMESTIC);
		} elseif ($serviceId == AIR_INTERNATIONAL) {
		$Query->where('bqi.lkp_service_id','=',AIR_INTERNATIONAL);
		} elseif ($serviceId == OCEAN) {
		$Query->where('bqi.lkp_service_id','=',OCEAN);
		} elseif ($serviceId == COURIER) {
		$Query->where('bqi.lkp_service_id','=',COURIER);
		} elseif ($serviceId == RELOCATION_DOMESTIC) {
		$Query->where('bqi.lkp_service_id','=',RELOCATION_DOMESTIC);
		} elseif ($serviceId == RELOCATION_INTERNATIONAL) {
		$Query->where('bqi.lkp_service_id','=',RELOCATION_INTERNATIONAL);
		} elseif ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
		$Query->where('bqi.lkp_service_id','=',RELOCATION_GLOBAL_MOBILITY);
		}
		
		if (isset ( $_REQUEST ['status_id'] ) && $_REQUEST ['status_id'] != '') {	
                        if($_REQUEST ['status_id'] == 0) {
                            $Query->whereIn ( 'bqi.lkp_post_status_id', array(1,2,3,4,5));
                        } else {
                            $Query->where ( 'bqi.lkp_post_status_id', '=', $_REQUEST ['status_id'] );
                        }			
		}
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'bqi.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'bqi.from_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			
		}
		if (isset ( $_GET['bid_end_date'] ) && $_GET['bid_end_date'] != '') {
			$commonBidEndDate = CommonComponent::convertDateForDatabase($_GET ['bid_end_date']);
			$Query->where ( 'tbdt.bid_end_date', '=', $commonBidEndDate );
			
		}
		//Functionality to handle filters based on the selection starts all services
		if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$postResults = $Query->select ( 'bqi.*','bqit.from_location_id','bqit.to_location_id', 'ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity', 'tbdt.bid_end_date','tbdt.created_at','lqa.quote_access' )->get ();
			foreach ( $postResults as $post ) {
				$buyer_quotes = DB::table ( 'term_buyer_quote_items' )->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*' )->get ();
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->from_location_id )->pluck ( 'city_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $quotes->to_location_id )->pluck ( 'city_name' );
					}			
				}
			}
		} elseif ($serviceId == ROAD_PTL || $serviceId == RAIL  || $serviceId == AIR_DOMESTIC) {
			$postResults = $Query->select ( 'bqi.*','bqit.from_location_id','bqit.to_location_id', 'ps.post_status', 'ct.postoffice_name as toCity', 'cf.postoffice_name as fromCity', 'tbdt.bid_end_date','tbdt.created_at','lqa.quote_access'  )->get ();
			
			foreach ( $postResults as $post ) {
				$buyer_quotes = DB::table ( 'term_buyer_quote_items' )->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*' )->get ();
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->from_location_id )->pluck ( 'pincode' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->to_location_id )->pluck ( 'pincode' );
					}
				}
			}
		}elseif ($serviceId == COURIER) {
			$postResults = $Query->select ( 'bqi.*','bqit.from_location_id','bqit.to_location_id', 'ps.post_status', DB::raw("(case when `bqi`.`lkp_courier_delivery_type_id` = 1 then lppt.id  when `bqi`.`lkp_courier_delivery_type_id` = 2 then lppt1.country_name end) as toCity"), 'lppf.id as fromCity', 'tbdt.bid_end_date','tbdt.created_at','lqa.quote_access'  )->get ();
			//echo "<pre>"; print_r($postResults); die;
			foreach ( $postResults as $post ) {
				$buyer_quotes = DB::table ( 'term_buyer_quote_items' )->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*' )->get ();
				$document_filter = DB::table ( 'term_buyer_quotes' )->where ( 'id', $post->id )->select ( 'term_buyer_quotes.*' )->get ();
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->from_location_id )->pluck ( 'pincode' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						if(Session::get('delivery_type') == 1){
							$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_ptl_pincodes' )->where ( 'id', $quotes->to_location_id )->pluck ( 'pincode' );
						}else{
							$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_countries' )->where ( 'id', $quotes->to_location_id )->pluck ( 'country_name' );
						}

					}
				}
				
				foreach ( $document_filter as $document_filters ) {
				if (!isset( $ptlCourierTypes [$document_filters->lkp_courier_type_id] )) {
                            $ptlCourierTypes [$document_filters->lkp_courier_type_id] = DB::table ( 'lkp_courier_types' )->where ( 'id', $document_filters->lkp_courier_type_id )->pluck ( 'courier_type' );
                        }
				}
			}
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$postResults = $Query->select ( 'bqi.*','bqit.from_location_id','bqit.to_location_id', 'ps.post_status', 'ct.airport_name as toCity', 'cf.airport_name as fromCity', 'tbdt.bid_end_date','tbdt.created_at','lqa.quote_access'  )->get ();
			foreach ( $postResults as $post ) {
				$buyer_quotes = DB::table ( 'term_buyer_quote_items' )->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*' )->get ();
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $quotes->from_location_id )->pluck ( 'airport_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_airports' )->where ( 'id', $quotes->to_location_id )->pluck ( 'airport_name' );
					}
				}
			}
		}	elseif ($serviceId == OCEAN) {
			$postResults = $Query->select ( 'bqi.*','bqit.from_location_id','bqit.to_location_id', 'ps.post_status', 'ct.seaport_name as toCity', 'cf.seaport_name as fromCity', 'tbdt.bid_end_date','tbdt.created_at','lqa.quote_access'  )->get ();
			foreach ( $postResults as $post ) {
				$buyer_quotes = DB::table ( 'term_buyer_quote_items' )->where ( 'term_buyer_quote_id', $post->id )->select ( 'term_buyer_quote_items.*' )->get ();
				foreach ( $buyer_quotes as $quotes ) {
					if (! isset ( $from_locations [$quotes->from_location_id] )) {
						$from_locations [$quotes->from_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $quotes->from_location_id )->pluck ( 'seaport_name' );
					}
					if (! isset ( $to_locations [$quotes->to_location_id] )) {
						$to_locations [$quotes->to_location_id] = DB::table ( 'lkp_seaports' )->where ( 'id', $quotes->to_location_id )->pluck ( 'seaport_name' );
					}
				}
			}
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		
		
		$grid = DataGrid::source ( $Query );		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
          if ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
              $grid->add ( 'fromCity', 'Location', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));  
          } else {
              $grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
          }
		if ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
             $grid->add ( 'toCity', '', false )->style ( "display:none" );
          } else {
             $grid->add ( 'toCity', 'To', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
          }
		
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'quote_access', 'Posted For', false )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'bid_end_date', 'Bid End Date ', 'bid_end_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'post_status', 'dummycolumn', 'post_status' )->style ( "display:none" );
		$grid->edit ( 'dummy', 'Status', 'post_status' )->attributes(array("class" => "col-md-1 padding-none"));
		
		$grid->add ( 'buyer_quote_id', 'Buyer id', 'buyer_quote_id' )->style ( "display:none" );
		$grid->add ( 'lkp_post_status_id', 'Post status id', 'lkp_post_status_id' )->style ( "display:none" );
		$grid->add ( 'from_city_id', 'from city id', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_price_type_id', 'Price Type', true )->style ( "display:none" );
		
		$grid->orderBy ( 'bqi.id', 'desc' );
		$grid->paginate ( 5 );   
                  
		$grid->row ( function ($row) {
		$buyer_quote_id = $row->cells [0]->value;
		$row->cells [0]->style ( 'display:none' );
		$fromcity = $row->cells [1]->value;
		$dispatchDate = $row->cells [3]->value;
		$deliveryDate = $row->cells [4]->value;
		$bidDate = CommonComponent::getBidDateTimeByQuoteId($buyer_quote_id,Session::get ( 'service_id' ));
		
		$serviceId = Session::get('service_id');
		if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC) {
			$row->cells [1]->value = '<span></span>'.$fromcity;
		} elseif ($serviceId == ROAD_PTL) {
			$row->cells [1]->value = '<span></span>'.$fromcity;
		}

		$row->cells [3]->value = CommonComponent::checkAndGetDate($dispatchDate);
		$row->cells [4]->value = CommonComponent::checkAndGetDate($deliveryDate);
		$row->cells [6]->value = CommonComponent::checkAndGetDate($bidDate);

		
		$row->cells [8]->style ( 'width:100%' );
		$buyer_id = $row->cells [9]->style ( 'display:none' );
		$serviceId = Session::get('service_id');
		$buyerCountIdTot = count (TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId( $buyer_quote_id,$serviceId) );
		$post_status_id = $row->cells [10]->style ( 'display:none' );
                
                if($buyerCountIdTot > 0) {
                    $buyerCountId = $buyerCountIdTot;
                } else {
                    $buyerCountId = '';
                }
                    
		$arraySellerIds = BuyerComponent::getSellerIds($row->cells[11]->style ( 'display:none' ));
		$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($row->cells[11]->style ( 'display:none' ), $arraySellerIds);
		$priceType = $row->cells [12]->style ( 'display:none' );
		$termViewCount = CommonComponent::termDisplayViewCount($buyer_quote_id,Session::get ( 'service_id' ));
		if ($post_status_id == '1' && $post_status_id!="") {
			$editLink = url()."/termdraftedit/$buyer_quote_id";
		} else {
			$editLink = url()."/gettermbuyercounteroffer/$buyer_quote_id";
		}
		
		$from = TermBuyerComponent::checkMulti($serviceId,$buyer_quote_id,"from_location_id");
		$to = 	TermBuyerComponent::checkMulti($serviceId,$buyer_quote_id,"to_location_id");
		
		if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL ) {
			if($from == "multi"){
				$row->cells [1]->value ='<span></span>'. "Many";
			}
		} elseif ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
		if($from == "multi"){
				$row->cells [1]->value ='<span></span>'. "Many";
			}
		} elseif ($serviceId == COURIER) {
			if($from == "multi"){
					$row->cells [1]->value ='<span></span>'. "Many";
				}else{
					$from_location_name = CommonComponent::getPinNameFromId($fromcity);
					$row->cells [1]->value ='<span></span>'. $from_location_name;
				}
		} elseif ($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN) {
		if($from == "multi"){
			$row->cells [1]->value ='<span></span>'. "Many";
			}
		}
          if ($serviceId != RELOCATION_GLOBAL_MOBILITY) {
             if($to == "multi"){
			$row->cells [2]->value = "Many";
              }   
          }		
          
		if ($serviceId == COURIER) {
			if(Session::get('delivery_type') == 1){
				if($to == "multi"){
					$row->cells [2]->value = "Many";
				}else{
					$to_location_name = CommonComponent::getPinNameFromId($row->cells [2]->value);
					$row->cells [2]->value = $to_location_name;
				}
			}else{
				if($to == "multi"){
					$row->cells [2]->value = "Many";
				}
			}
		}
		$postedFor = $row->cells [8]->value;
					
		$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none html_link", "data_link"=>$editLink));
          if ($serviceId != RELOCATION_GLOBAL_MOBILITY) {
            $row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none html_link", "data_link"=>$editLink));
          }
		$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none html_link", "data_link"=>$editLink));
		$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none hidden-xs html_link", "data_link"=>$editLink));
		$row->cells [5]->attributes(array("class" => "col-md-1 padding-left-none html_link", "data_link"=>$editLink));
		$row->cells [6]->attributes(array("class" => "col-md-2 padding-left-none html_link", "data_link"=>$editLink));
		$row->cells [7]->attributes(array("class" => "col-md-1 padding-left-none html_link", "data_link"=>$editLink));

		
		$leadscount = count(BuyerMatchingComponent::getMatchedResults(ROAD_FTL,$buyer_quote_id));	
                $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyer_quote_id,1);
		
		if ($post_status_id == '2' || $post_status_id == '1') {
			$row->cells [8]->value .= "
			<div class='col-md-1 padding-left-none text-right pull-right'>
			
			</div></a>";
		}	
			
		$row->cells [8]->value .= "<div class='clearfix'></div><div class='pull-left'>
		<div class='info-links'>
		<a href='/gettermbuyercounteroffer/".$buyer_quote_id."?type=messages'><i class='fa fa-envelope-o'></i> 
		Messages<span class='badge'>".count($msg_count['result'])."</span></a>
		<a href='/gettermbuyercounteroffer/".$buyer_quote_id."?type=quotes'>
		<i class='fa fa-file-text-o'></i> 
		Quotes<span class='badge'>$buyerCountId</span></a>
		<a href='#'><i class='fa fa-thumbs-o-up'></i> Leads</a>
		<a href='#'><i class='fa fa-line-chart'></i> Market Analytics</a>
		<a href='#'><i class='fa fa-file-text-o'></i> Documentation</a>
		</div>
		</div>
                <div class='pull-right text-right'>
		<div class='info-links'>";
                if ($post_status_id == '2' || $post_status_id == '1') {
                  $row->cells [8]->value .= " <a href='#' data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid(".$buyer_quote_id.")' ><i class='fa fa-trash buyerpostdelete' title='Delete'></i></a>";
                }		
                
		$row->cells [8]->value .= " <a><span class='views red'><i class='fa fa-eye' title='Views'></i> $termViewCount </span></a>
		</div>
		</div>
		<div class='margin-top text-right pull-right'>
		$buyer_id
		</div>";
		

	} );
		
		// Functionality to build filters in the page starts
		$filter = DataFilter::source ( $Query );
		$filter->add ( 'bqi.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		$filter->add ( 'bqi.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		if ($serviceId == COURIER) {
		$filter->add ( 'bqi.lkp_courier_type_id', 'Courier Type', 'select' )->options ( $ptlCourierTypes )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
		}
		$filter->submit ( 'search' );
		$filter->reset ( 'reset' );
		$filter->build ();
		// Functionality to build filters in the page ends
		
		$result = array ();
		$result ['grid'] = $grid;
		$result ['filter'] = $filter;
		return $result;
	}
	
	
	/**
	 * Get Post Buyer Counter Offer Page
	 * Get details of buyer counter offer
	 * @param int $buyerQuoteItemId
	 * @return type
	 */
	public static function getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId,$comparisonType=null,$priceVal=null,$checkIds=null)
	{
		
		
		try {
			Log::info('Get posted buyer counter offer for ftl: '.Auth::id(),array('c'=>'2'));
			if($roleId == BUYER){
				CommonComponent::activityLog("BUYER_FETCHED_SELLER_POST",
						BUYER_FETCHED_SELLER_POST,0,
						HTTP_REFERRER,CURRENT_URL);
			}
			
			$arrayBuyerCounterOffer = BuyerComponent::getTermBuyerQuoteDetailsFromId($buyerQuoteItemId,$serviceId);
			$countview = CommonComponent::termDisplayViewCount($buyerQuoteItemId,$serviceId);
			$quotesCount=count (TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId( $buyerQuoteItemId,$serviceId) );
			
			if(!empty($arrayBuyerCounterOffer)) {
				$arrayBuyerQuoteSellersQuotesPrices = BuyerComponent::getTermBuyerQuoteDetailsFromId($buyerQuoteItemId,$serviceId, $comparisonType,$priceVal,$checkIds);
				$arraySellerIds = BuyerComponent::getSellerIds($arrayBuyerCounterOffer[0]->term_buyer_quote_id);
				$arrayBuyerLeads = BuyerComponent::getLeadsForBuyer($arrayBuyerCounterOffer[0]->from_location_id, $arraySellerIds);
				$countBuyerLeads = count($arrayBuyerLeads);
				if(!empty($arrayBuyerQuoteSellersQuotesPrices)) {
					$countCartItems = BuyerComponent::getCountOfCartItems($arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id,$buyerQuoteItemId,true);
				} else {
					$countCartItems = 0;
				}
				$bidEndDates = TermBuyerComponent::getBidDatesData($serviceId,$arrayBuyerCounterOffer[0]->term_buyer_quote_id);
				if($serviceId==ROAD_FTL || $serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_INTERNATIONAL){
				$fromLocation = BuyerComponent::getCityNameFromId($arrayBuyerCounterOffer[0]->from_location_id);
				$toLocation = BuyerComponent::getCityNameFromId($arrayBuyerCounterOffer[0]->to_location_id);
				}elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    $fromLocation = BuyerComponent::getCityNameFromId($arrayBuyerCounterOffer[0]->from_location_id);  
                    $toLocation = '';
                    }
                    elseif($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC){
				$fromLocation = CommonComponent::getPinName($arrayBuyerCounterOffer[0]->from_location_id);
				$toLocation = CommonComponent::getPinName($arrayBuyerCounterOffer[0]->to_location_id);
				}elseif($serviceId== COURIER){
				$fromLocation = CommonComponent::getPinName($arrayBuyerCounterOffer[0]->from_location_id);
				if($arrayBuyerCounterOffer[0]->courier_delivery_id == 1){
				$toLocation = CommonComponent::getPinName($arrayBuyerCounterOffer[0]->to_location_id);
				}else{
				$toLocation = CommonComponent::getCountry($arrayBuyerCounterOffer[0]->to_location_id);
				}
				}elseif($serviceId==AIR_INTERNATIONAL){
				$fromLocation = CommonComponent::getAirportName($arrayBuyerCounterOffer[0]->from_location_id);
				$toLocation = CommonComponent::getAirportName($arrayBuyerCounterOffer[0]->to_location_id);
				}elseif($serviceId==OCEAN){
				$fromLocation = CommonComponent::getSeaportName($arrayBuyerCounterOffer[0]->from_location_id);
				$toLocation = CommonComponent::getSeaportName($arrayBuyerCounterOffer[0]->to_location_id);
				}
				$dispatchDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->from_date);
				
				$deliveryDate = CommonComponent::checkAndGetDate($arrayBuyerCounterOffer[0]->to_date);
	
				$sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
				$destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
				$packagingType = BuyerComponent::getPackagingType('Destination');
				$buyerPostCounterOfferComparisonTypes = config::get('constants.BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES');
				$privateSellerNames = BuyerComponent::getTermAllPrivateSellerNames($buyerQuoteItemId,$serviceId);
				//echo "<pre>";print_r($privateSellerNames);exit;
				return [
						'arrayBuyerCounterOffer' => $arrayBuyerCounterOffer,
						'fromLocation' => $fromLocation,
						'toLocation' => $toLocation,
						'deliveryDate' => $deliveryDate,
						'dispatchDate' => $dispatchDate,
						'arrayBuyerQuoteSellersQuotesPrices' => $arrayBuyerQuoteSellersQuotesPrices,
						'countBuyerLeads' => $countBuyerLeads,
						'sourceLocation' => $sourceLocationType,
						'destinationLocation' => $destinationLocationType,
						'packagingType' => $packagingType,
						'countCartItems' => $countCartItems,
						'countview' => $countview,
						'bidEndDates'=>$bidEndDates,
						'quotesCount' => $quotesCount,
                        'privateSellerNames' => $privateSellerNames,
						
				];
			}
		} catch (Exception $e) {
	
		}
	}

	public static function getBuyerQuotesTermdata($serviceId,$quoteId)
	{
		try {
		if ($serviceId == ROAD_FTL)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
                    ->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
                    ->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
                    ->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
                    ->leftjoin('lkp_vehicle_types as vt', 'term_buyer_quote_items.lkp_vehicle_type_id', '=', 'vt.id')
                    ->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'term_buyer_quote_items.lkp_load_type_id' )
                    ->where('term_buyer_quotes.id', $quoteId)
                    ->where('term_buyer_quotes.lkp_service_id', $serviceId)
                    ->select('term_buyer_quote_items.*', 'vt.vehicle_type as vehicle_type','lt.load_type as load_type', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity', 'term_buyer_quotes.transaction_id', 'term_buyer_quotes.from_date', 'term_buyer_quotes.to_date')
                    ->get(); 	

		
		}else if ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
					->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
					->leftjoin('lkp_ptl_pincodes as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
					->leftjoin('lkp_ptl_pincodes as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
					->leftjoin('lkp_packaging_types as pt', 'term_buyer_quote_items.lkp_packaging_type_id', '=', 'pt.id')
					->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'term_buyer_quote_items.lkp_load_type_id' )
					->where('term_buyer_quotes.id', $quoteId)
					->select('term_buyer_quote_items.*', 'pt.packaging_type_name as packaging_type','lt.load_type as load_type', 'c1.pincode as from_pincode', 'c2.pincode as to_pincode','c1.postoffice_name as from_postofficename', 'c2.postoffice_name as to_postofficename', 'term_buyer_quotes.transaction_id','term_buyer_quotes.from_date', 'term_buyer_quotes.to_date', 'c1.postoffice_name as from_locationcity', 'c2.postoffice_name as to_locationcity')
					->get();
		}else if ($serviceId == COURIER)	{
				$getBuyerTermQuotes = DB::table('term_buyer_quotes');
				$getBuyerTermQuotes->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id');
				$getBuyerTermQuotes->leftjoin('lkp_ptl_pincodes as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id');
				
				$getBuyerTermQuotes->leftjoin('lkp_ptl_pincodes as c2', function($join)
 				{
 					$join->on('term_buyer_quote_items.to_location_id', '=', 'c2.id');
 					$join->on(DB::raw('term_buyer_quotes.lkp_courier_delivery_type_id'),'=',DB::raw(1));
						
				});
				$getBuyerTermQuotes->leftjoin('lkp_countries as lppt', function($join)
				{
 					$join->on('term_buyer_quote_items.to_location_id', '=', 'lppt.id');
 					$join->on(DB::raw('term_buyer_quotes.lkp_courier_delivery_type_id'),'=',DB::raw(2));
						
 				});
				
				$getBuyerTermQuotes->where('term_buyer_quotes.id', $quoteId);
				$getBuyerTermQuotes->select('term_buyer_quote_items.*', 
						'c1.pincode as from_pincode', 
						'c1.postoffice_name as from_postofficename', 
						'term_buyer_quotes.transaction_id',
						'term_buyer_quotes.from_date',
						'term_buyer_quotes.to_date', 
						'c1.postoffice_name as from_locationcity', 
						DB::raw("(case when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 1 then c2.pincode  when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 2 then lppt.country_name end) as to_pincode"),
						DB::raw("(case when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 1 then c2.postoffice_name  when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 2 then lppt.country_name end) as to_postofficename"),
						DB::raw("(case when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 1 then c2.postoffice_name  when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 2 then lppt.country_name end) as to_locationcity"));
				
				$getBuyerTermQuotesdata =$getBuyerTermQuotes->get();
				
		}else if ($serviceId == OCEAN)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
					->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
					->leftjoin('lkp_seaports as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
					->leftjoin('lkp_seaports as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
					->leftjoin('lkp_packaging_types as pt', 'term_buyer_quote_items.lkp_packaging_type_id', '=', 'pt.id')
					->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'term_buyer_quote_items.lkp_load_type_id' )
					->where('term_buyer_quotes.id', $quoteId)
					->select('term_buyer_quote_items.*', 'pt.packaging_type_name as packaging_type','lt.load_type as load_type', 'c1.seaport_name as from_postofficename', 'c2.seaport_name as to_postofficename', 'term_buyer_quotes.transaction_id','term_buyer_quotes.from_date', 'term_buyer_quotes.to_date', 'c1.seaport_name as from_locationcity', 'c2.seaport_name as to_locationcity')
					->get();			
		}else if ($serviceId == AIR_INTERNATIONAL)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
					->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
					->leftjoin('lkp_airports as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
					->leftjoin('lkp_airports as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
					->leftjoin('lkp_packaging_types as pt', 'term_buyer_quote_items.lkp_packaging_type_id', '=', 'pt.id')
					->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'term_buyer_quote_items.lkp_load_type_id' )
					->where('term_buyer_quotes.id', $quoteId)
					->select('term_buyer_quote_items.*', 'pt.packaging_type_name as packaging_type','lt.load_type as load_type', 'c1.airport_name as from_postofficename', 'c2.airport_name as to_postofficename', 'term_buyer_quotes.transaction_id','term_buyer_quotes.from_date', 'term_buyer_quotes.to_date', 'c1.airport_name as from_locationcity', 'c2.airport_name as to_locationcity')
					->get();			
		}else if ($serviceId == RELOCATION_DOMESTIC)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
				->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
				->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
				->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
				->leftjoin('lkp_vehicle_types as vt', 'term_buyer_quote_items.lkp_vehicle_type_id', '=', 'vt.id')
				->leftjoin( 'lkp_load_types as lt', 'lt.id', '=', 'term_buyer_quote_items.lkp_load_type_id' )
				->where('term_buyer_quotes.id', $quoteId)
				->where('term_buyer_quotes.lkp_service_id', $serviceId)
				->select('term_buyer_quote_items.*', 'vt.vehicle_type as vehicle_type','lt.load_type as load_type', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity', 'term_buyer_quotes.transaction_id', 'term_buyer_quotes.from_date', 'term_buyer_quotes.to_date')
				->get();
		}else if ($serviceId == RELOCATION_INTERNATIONAL)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
				->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
				->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
				->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
				->where('term_buyer_quotes.id', $quoteId)
				->where('term_buyer_quotes.lkp_service_id', $serviceId)
				->select('term_buyer_quote_items.*', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity', 'term_buyer_quotes.transaction_id', 'term_buyer_quotes.from_date', 'term_buyer_quotes.to_date', 'term_buyer_quotes.lkp_lead_type_id')
				->get();
		}else if ($serviceId == RELOCATION_GLOBAL_MOBILITY)	{
			$getBuyerTermQuotesdata = DB::table('term_buyer_quotes')
				->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.term_buyer_quote_id', '=', 'term_buyer_quotes.id')
				->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
				//->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
				->leftjoin('lkp_relocationgm_services as rgms', 'term_buyer_quote_items.lkp_gm_service_id', '=', 'rgms.id')
				->where('term_buyer_quotes.id', $quoteId)
				->where('term_buyer_quotes.lkp_service_id', $serviceId)
				->select('term_buyer_quote_items.*', 'c1.city_name as from_locationcity', 'term_buyer_quotes.transaction_id', 'term_buyer_quotes.from_date', 'term_buyer_quotes.to_date', 'term_buyer_quotes.lkp_lead_type_id','rgms.service_type')
				->get();
		}
		return $getBuyerTermQuotesdata;
	} catch (Exception $e) {
	
		}
	}

	public static function checkMulti($serviceId,$quoteId,$field)
	{
		$getBuyerTermQuotesdata = array();
		try {
		$getBuyerTermQuotesdata = DB::table('term_buyer_quote_items')
					->where('term_buyer_quote_items.term_buyer_quote_id', $quoteId)
					//->groupBy("term_buyer_quote_items.$field")
					->select("term_buyer_quote_items.$field")
					->get();			
		} catch (Exception $e) {

		}
		if(count($getBuyerTermQuotesdata) > 1){
			return "multi";
		}
		return "";
		
	}
	
	public static function getTermQuotes($serviceId,$quoteId)
	{
		try {			
				$getTermquotes = DB::table('term_buyer_quotes')				
				->where('term_buyer_quotes.id', $quoteId)
				->leftjoin('lkp_bid_types as bt', 'term_buyer_quotes.lkp_bid_type_id', '=', 'bt.id')
				->select('term_buyer_quotes.*', 'bt.bid_type as bid_type')
				->first();				
				return $getTermquotes;			
		} catch (Exception $e) {
	
		}
	}
	//Getting all bid dates data
	public static function getBidDatesData($serviceId,$quoteId)
	{
		try {			
				$getBiddates = DB::table('term_buyer_bid_dates')
				->where('term_buyer_bid_dates.term_buyer_quote_id', $quoteId)
				->select('term_buyer_bid_dates.id','term_buyer_bid_dates.bid_end_date', 'term_buyer_bid_dates.bid_end_time')
				->orderBy('id', 'DESC')
				->get();
				return $getBiddates;	
		} catch (Exception $e) {
	
		}
	}	
	//Getting all bid dates data
	public static function getLastUpdatedBidDatesData($serviceId,$quoteId)
	{
		try {
			$getLastbidates = DB::table('term_buyer_bid_dates')
			->where('term_buyer_bid_dates.term_buyer_quote_id', $quoteId)
			->select('term_buyer_bid_dates.id','term_buyer_bid_dates.term_buyer_quote_id','term_buyer_bid_dates.bid_end_date', 'term_buyer_bid_dates.bid_end_time')
			->first();
			return $getLastbidates;	
		} catch (Exception $e) {
	
		}
	}
	
	public static function updateDatesData($allRequestdata)
	{
		try {	
				if(Session::get ( 'service_id' ) != ''){
					$serviceId = Session::get ( 'service_id' );
				}						
				//Save bid Data dates 
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyerBidDate = new TermBuyerBidDate();
				$buyerBidDate->term_buyer_quote_id = $allRequestdata['quoteid'];
				$buyerBidDate->bid_end_date = CommonComponent::convertDateForDatabase($allRequestdata['last_bid_date']);
				$buyerBidDate->bid_end_time = $allRequestdata['bid_close_time'];
				$buyerBidDate->is_active = 1;
				$buyerBidDate->updated_by = Auth::id();
				$buyerBidDate->updated_at = $created_at;
				$buyerBidDate->updated_ip = $createdIp;
				$buyerBidDate->lkp_service_id = $serviceId;
				$buyerBidDate->save();	
				//Sending mail to users after bid edit
				$buyers_selected_sellers_email = DB::table('users')->where('id', Auth::id())->get();
				$buyers_selected_sellers_email[0]->bid_end_date = CommonComponent::convertDateForDatabase($allRequestdata['last_bid_date']);
				$buyers_selected_sellers_email[0]->bid_end_time = $allRequestdata['bid_close_time'];
				CommonComponent::send_email(BUYER_UPDATED_BIDCLOSE_DATE, $buyers_selected_sellers_email);
                                
                                //send mail for private seller sellected
                                $privateSellerNames = BuyerComponent::getTermAllPrivateSellerNames($allRequestdata['quoteid'],$serviceId);
                                //echo "<pre>"; print_r($privateSellerNames); die;
                                foreach($privateSellerNames as $sellerPrivateId) {                                    
                                    $buyers_selected_sellers_email_bid = DB::table('users')->where('id', $sellerPrivateId->id)->get();
                                    $buyers_selected_sellers_email_bid[0]->bid_end_date = CommonComponent::convertDateForDatabase($allRequestdata['last_bid_date']);
                                    $buyers_selected_sellers_email_bid[0]->bid_end_time = $allRequestdata['bid_close_time'];
                                    CommonComponent::send_email(BUYER_UPDATED_BIDCLOSE_DATE, $buyers_selected_sellers_email_bid);   		 
                                }
                                
                                
				return 1;					
		} catch (Exception $e) {
	
		}
	}
	
	
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve seller lists
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteId, $serviceId=null,$comparisonType = null,$priceVal = null,$checkIds = null) {
		try {
			
			
			Log::info('Get seller lists for the buyer: ' . Auth::id(), array('c' => '2'));
			
			$arrayBuyerQuoteSellersNotQuotesPrices="";
			$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('term_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
			if (!empty($buyerQuoteId)) {
				$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
			}
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
			$getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptance` = "0")');
			$getBuyerQuoteSellersQuotesPricesQuery->whereRaw('bqsqp.initial_quote_price is not NULL');
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.is_submitted', 1);
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
			$getBuyerQuoteSellersQuotesPricesQuery->groupBy('bqsqp.seller_id');
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.lkp_service_id', 'bqsqp.initial_quote_price', 'bqsqp.final_quote_price','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft', 
					'bqsqp.final_rate_per_kg','bqsqp.final_kg_per_cft','bqsqp.rate_per_cft','bqsqp.transit_days','u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id', 'bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',  'lvt.vehicle_type', 'bqi.lkp_post_status_id');
			$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
			
			return $arrayBuyerQuoteSellersQuotesPrices;
			
			
			
		} catch (Exception $exc) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve seller lists
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getTermBuyerQuoteSellersQuotesPriceitemsFromId($buyerQuoteId,$seller_id,$serviceId, $comparisonType = null,$priceVal = null,$checkIds = null) {
		try {
				
				
			Log::info('Get seller lists for the buyer: ' . Auth::id(), array('c' => '2'));
				
			$arrayBuyerQuoteSellersNotQuotesPrices="";
			$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
			if ($serviceId != COURIER) {
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('term_buyer_quote_items as bqi', 'bqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
			}else{
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('term_buyer_quote_items as bqi', 'bqi.term_buyer_quote_id', '=', 'bqsqp.term_buyer_quote_item_id');
			}
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_vehicle_types as lvt', 'lvt.id', '=', 'bqi.lkp_vehicle_type_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id');
			$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'bqi.lkp_load_type_id');
			if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == ROAD_FTL || $serviceId == RELOCATION_INTERNATIONAL) {
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_cities as cf', 'bqi.from_location_id', '=', 'cf.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_cities as ct', 'bqi.to_location_id', '=', 'ct.id' );
				
			} elseif ($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_ptl_pincodes as cf', 'bqi.from_location_id', '=', 'cf.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_ptl_pincodes as ct', 'bqi.to_location_id', '=', 'ct.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
				
			} elseif ($serviceId == COURIER) {
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_ptl_pincodes as cf', 'bqi.from_location_id', '=', 'cf.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_ptl_pincodes as ct', function($join)
				{
					$join->on('bqi.to_location_id', '=', 'ct.id');
					$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
						
				});
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin('lkp_countries as lppt1', function($join)
				{
					$join->on('bqi.to_location_id', '=', 'lppt1.id');
					$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
						
				});
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
			} elseif ($serviceId == AIR_INTERNATIONAL) {
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_airports as cf', 'bqi.from_location_id', '=', 'cf.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_airports as ct', 'bqi.to_location_id', '=', 'ct.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
			} elseif ($serviceId == OCEAN) {
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_seaports as cf', 'bqi.from_location_id', '=', 'cf.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_seaports as ct', 'bqi.to_location_id', '=', 'ct.id' );
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_packaging_types as lp', 'bqi.lkp_packaging_type_id', '=', 'lp.id' );
			} elseif ($serviceId == RELOCATION_GLOBAL_MOBILITY) {                    
				$getBuyerQuoteSellersQuotesPricesQuery->leftjoin ( 'lkp_relocationgm_services as lkrgs', 'bqi.lkp_gm_service_id', '=', 'lkrgs.id' );
			} 
			
			if (!empty($buyerQuoteId)) {
				$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
			}
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
			$getBuyerQuoteSellersQuotesPricesQuery->whereRaw('(`bqsqp`.`initial_quote_price` is not NULL and `bqsqp`.`is_submitted` =1 and `bqsqp`.`initial_quote_price` != "" or `bqsqp`.`seller_acceptance` = "0")');
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.buyer_id', Auth::User()->id);
			$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.seller_id', $seller_id);
			if ($serviceId == ROAD_FTL) {			
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_quote_price', 'bqsqp.final_quote_price', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id', 
					  'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.quantity','cf.city_name as fromcity','ct.city_name as tocity','bq.lkp_service_id');
			} elseif ($serviceId == ROAD_PTL  || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) {
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_rate_per_kg', 'bqsqp.final_rate_per_kg','bqsqp.initial_kg_per_cft','bqsqp.final_kg_per_cft', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
					'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.quantity','cf.postoffice_name as fromcity','ct.postoffice_name as tocity','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id');
			}elseif ($serviceId ==COURIER) {
				$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_rate_per_kg', 'bqsqp.final_rate_per_kg','bqsqp.initial_kg_per_cft','bqsqp.final_kg_per_cft', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqi.id as term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
						'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.quantity','cf.postoffice_name as fromcity',DB::raw("(case when `bq`.`lkp_courier_delivery_type_id` = 1 then ct.postoffice_name  when `bq`.`lkp_courier_delivery_type_id` = 2 then lppt1.country_name end) as tocity"),'lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id');
					
			} elseif ($serviceId == AIR_INTERNATIONAL) {
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_rate_per_kg', 'bqsqp.final_rate_per_kg','bqsqp.initial_kg_per_cft','bqsqp.final_kg_per_cft', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
					'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.quantity','cf.airport_name as fromcity','ct.airport_name as tocity','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id');
			} elseif ($serviceId == OCEAN) {
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_rate_per_kg', 'bqsqp.final_rate_per_kg','bqsqp.initial_kg_per_cft','bqsqp.final_kg_per_cft', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
					'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.quantity','cf.seaport_name as fromcity','ct.seaport_name as tocity','lp.packaging_type_name','bqi.number_packages','bqi.volume','bq.lkp_service_id');
			} elseif ($serviceId == RELOCATION_DOMESTIC) {
			$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.initial_quote_price', 'bqsqp.final_quote_price','bqsqp.rate_per_cft','bqsqp.transport_charges','bqsqp.odcharges','bqsqp.transit_days','bqsqp.crating_charges','bqsqp.storage_charges','bqsqp.escort_charges','bqsqp.property_charges','bqsqp.handyman_charges','bqsqp.brokerage_charge', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id', 
					  'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.volume','bqi.number_packages','cf.city_name as fromcity','ct.city_name as tocity','bq.lkp_service_id');
			} 
			elseif ($serviceId == RELOCATION_INTERNATIONAL) {
				$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id', 'bqsqp.fright_five_hundred', 'bqsqp.final_quote_price','bqsqp.fright_hundred','bqsqp.fright_three_hundred','bqsqp.odcharges','bqsqp.transit_days','bqsqp.crating_charges','bqsqp.storage_charges','bqsqp.escort_charges','bqsqp.property_charges','bqsqp.handyman_charges','bqsqp.brokerage_charge', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
						'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.volume','bqi.number_packages','cf.city_name as fromcity','ct.city_name as tocity','bq.lkp_service_id','bqsqp.odlcl_charges','bqsqp.odtwentyft_charges','bqsqp.odfortyft_charges','bqsqp.frieghtlcl_charges','bqsqp.frieghttwentft_charges','bqsqp.frieghtfortyft_charges');
			} elseif ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
				$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id','bqsqp.initial_quote_price', 'bqsqp.term_buyer_quote_id', 'bqsqp.fright_five_hundred', 'bqsqp.final_quote_price','bqsqp.fright_hundred','bqsqp.fright_three_hundred','bqsqp.odcharges','bqsqp.transit_days','bqsqp.crating_charges','bqsqp.storage_charges','bqsqp.escort_charges','bqsqp.property_charges','bqsqp.handyman_charges','bqsqp.brokerage_charge', 'u.username', 'ldt.load_type', 'bqsqp.seller_id', 'bqsqp.term_buyer_quote_item_id','bqsqp.seller_acceptance', 'bq.lkp_quote_access_id',
						'lvt.vehicle_type', 'bqi.lkp_post_status_id','bqi.volume','bqi.number_packages','bq.lkp_service_id','bqsqp.odlcl_charges','bqsqp.odtwentyft_charges','bqsqp.odfortyft_charges','bqsqp.frieghtlcl_charges','bqsqp.frieghttwentft_charges','bqsqp.frieghtfortyft_charges','lkrgs.service_type as serviceType','bqi.measurement','bqi.measurement_units');
			}
			$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
			//echo "<pre>";print_r($arrayBuyerQuoteSellersQuotesPrices);exit;
			
			return $arrayBuyerQuoteSellersQuotesPrices;
			
				
		} catch (Exception $exc) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve seller slabs
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getTermBuyerQuoteSlabs($buyerQuoteId,$seller_id,$serviceId) {
	
		try
		{
			$getsellersalbs= DB::table('term_buyer_quote_sellers_quotes_price_slabs')
			->where('term_buyer_quote_id', $buyerQuoteId)
			->where('seller_id', $seller_id)
			->select('term_buyer_quote_sellers_quotes_price_slabs.*')
			->get();
	
			if(count($getsellersalbs)==0)
				return $getsellersalbs=array();
			else
				return $getsellersalbs;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	public static function getTermBuyerQuoteSlabsInd($buyerQuoteId) {
	
		try
		{
			$getsellersalb_for_buyer= DB::table('term_buyer_quote_slabs')
			->where('buyer_quote_id', $buyerQuoteId)
			->select('term_buyer_quote_slabs.*')
			->get();
	
			if(count($getsellersalb_for_buyer)==0)
				return $getsellersalb_for_buyer=array();
			else
				return $getsellersalb_for_buyer;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve getMaxWeightAccepted
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getMaxWeightAccepted($buyerQuoteId,$service_id) {
	
		try
		{
			$getMaxWeightAccepted= DB::table('term_buyer_quotes')
			->where('id', $buyerQuoteId)
			->where('lkp_service_id', $service_id)
			->select('term_buyer_quotes.max_weight_accepted')
			->get();

			return $getMaxWeightAccepted[0]->max_weight_accepted;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve getMaxWeightAcceptedUnits
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getMaxWeightAcceptedUnits($buyerQuoteId,$service_id) {
	
		try
		{
			$getMaxWeightAccepted= DB::table('term_buyer_quotes')
			->where('id', $buyerQuoteId)
			->where('lkp_service_id', $service_id)
			->select('term_buyer_quotes.lkp_ict_weight_uom_id')
			->get();
			if($getMaxWeightAccepted[0]->lkp_ict_weight_uom_id == 1){
				$getMaxWeightAcceptedUnit = 'Kgs';
			}else if($getMaxWeightAccepted[0]->lkp_ict_weight_uom_id == 2) {
				$getMaxWeightAcceptedUnit = 'Gms';
			}else{
				$getMaxWeightAcceptedUnit = 'Mts';
			}
			return $getMaxWeightAcceptedUnit;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve getMaxWeightAccepted
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getMaxWeightIncWeight($buyerQuoteId,$service_id) {
	
		try
		{
			$getMaxWeightIncWeight= DB::table('term_buyer_quote_sellers_quotes_prices')
			->where('term_buyer_quote_id', $buyerQuoteId)
			->where('lkp_service_id', $service_id)
			->select('term_buyer_quote_sellers_quotes_prices.incremental_weight','term_buyer_quote_sellers_quotes_prices.incremental_weight_price')
			->get();
			return $getMaxWeightIncWeight;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	/**
	 * Term Buyer counter offer Page
	 * Method to retrieve getMaxWeightAccepted
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function getMaxWeightIncWeightInd($buyerQuoteId,$service_id) {
	
		try
		{
			$getMaxWeightIncWeight= DB::table('term_buyer_quotes')
			->where('id', $buyerQuoteId)
			->where('lkp_service_id', $service_id)
			->select('term_buyer_quotes.increment_weight')
			->get();
			return $getMaxWeightIncWeight;
		}
		catch(Exception $e)
		{
			//echo $e;die();
		}
	
	}
	
	/**
	 * Term Buyer Generate Contract
	 * Method
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function generateSellerContract($allRequestdata,$serviceId){
		
		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];
		
		$path = '';
		
		
		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.seller_id', $allRequestdata['seller']);
		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_id', $allRequestdata['buyer_quote_id']);
		
		
		if ($serviceId == ROAD_FTL) {
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.term_buyer_quote_item_id','bqsqp.seller_id','bqsqp.initial_quote_price');
		}elseif ($serviceId == RELOCATION_DOMESTIC) {
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.term_buyer_quote_item_id','bqsqp.seller_id','bqsqp.rate_per_cft','bqsqp.transport_charges','bqsqp.odcharges');
		}elseif ($serviceId == RELOCATION_INTERNATIONAL) {
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.term_buyer_quote_item_id','bqsqp.seller_id','bqsqp.fright_hundred','bqsqp.fright_three_hundred','bqsqp.fright_five_hundred',
				'bqsqp.odcharges','bqsqp.transit_days', 'bqsqp.odlcl_charges', 'bqsqp.odtwentyft_charges', 'bqsqp.odfortyft_charges', 'bqsqp.frieghtlcl_charges', 'bqsqp.frieghttwentft_charges', 'bqsqp.frieghtfortyft_charges');
		}elseif ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.term_buyer_quote_item_id','bqsqp.seller_id','bqsqp.fright_hundred','bqsqp.fright_three_hundred','bqsqp.fright_five_hundred',
				'bqsqp.odcharges','bqsqp.transit_days', 'bqsqp.odlcl_charges', 'bqsqp.odtwentyft_charges', 'bqsqp.odfortyft_charges', 'bqsqp.frieghtlcl_charges', 'bqsqp.frieghttwentft_charges', 'bqsqp.frieghtfortyft_charges', 'bqsqp.initial_quote_price');
		}
		else{
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id', 'bqsqp.term_buyer_quote_id','bqsqp.term_buyer_quote_item_id','bqsqp.seller_id','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft');
	    }
	    $arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();

	    $created_year = date('Y');
	    $invid  =   CommonComponent::getTermContractID();
		for($i=0;$i<count($arrayBuyerQuoteSellersQuotesPrices);$i++)
		{
                    	//echo $i;
			
			$getBuyerQuoteSellersQuotes = DB::table('term_buyer_quotes as tbq');
			$getBuyerQuoteSellersQuotes->where('tbq.id', $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id);
			$getBuyerQuoteSellersQuotes->select('tbq.lkp_post_ratecard_type','tbq.lkp_lead_type_id');
			$arrayBuyerQuotes = $getBuyerQuoteSellersQuotes->get();
			
			$getBuyerQuoteSellersItems = DB::table('term_buyer_quote_items as tbqi');
			$getBuyerQuoteSellersItems->where('tbqi.id', $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id);
			$getBuyerQuoteSellersItems->select('tbqi.*');
			$arrayBuyerItems = $getBuyerQuoteSellersItems->get();
				
			
		if(isset($allRequestdata[$arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id."_".$arrayBuyerQuoteSellersQuotesPrices[$i]->id])){	
		$seller_check=$allRequestdata[$arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id."_".$arrayBuyerQuoteSellersQuotesPrices[$i]->id];
		
		
		if($seller_check=='on'){
			
			
			if($serviceId==RELOCATION_DOMESTIC){
				
				$randString = 'RELOCATION/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				if($arrayBuyerQuotes[0]->lkp_post_ratecard_type==1){
				$termcontract = new TermContract();
				$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
				$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id;
				$termcontract->contract_no = $randString;
				$termcontract->contract_price = $arrayBuyerQuoteSellersQuotesPrices[$i]->rate_per_cft;
				$termcontract->contract_quantity = $arrayBuyerItems[0]->volume;
				$termcontract->seller_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id;
				$termcontract->lkp_service_id = $serviceId;
				$termcontract->created_by = Auth::id();
				$termcontract->created_at = $created_at;
				$termcontract->created_ip = $createdIp;
				$termcontract->save();
				}else{
				$termcontract = new TermContract();
				$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
				$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id;
				$termcontract->contract_no = $randString;
				$termcontract->contract_price = $arrayBuyerQuoteSellersQuotesPrices[$i]->transport_charges + $arrayBuyerQuoteSellersQuotesPrices[$i]->odcharges;
				$termcontract->contract_transport_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->transport_charges;
				$termcontract->contract_od_charges  = $arrayBuyerQuoteSellersQuotesPrices[$i]->odcharges;
				$termcontract->contract_quantity = $arrayBuyerItems[0]->volume;
				$termcontract->seller_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id;
				$termcontract->lkp_service_id = $serviceId;
				$termcontract->created_by = Auth::id();
				$termcontract->created_at = $created_at;
				$termcontract->created_ip = $createdIp;
				$termcontract->save();
				}
				
				
			}
			
			if($serviceId==RELOCATION_INTERNATIONAL){
			
                        if(isset($arrayBuyerQuotes[0]->lkp_lead_type_id) && $arrayBuyerQuotes[0]->lkp_lead_type_id==1) {
                            $randString = 'RELOCATION_INTERNATIONALAIR_TERM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                        } else {
                            $randString = 'RELOCATION_INTERNATIONALOCEAN_TERM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                        }                                
				
					$termcontract = new TermContract();
					$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
					$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id;
					$termcontract->contract_no = $randString;					
                         $termcontract->contract_od_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->odcharges;
					$termcontract->fright_hundred = $arrayBuyerQuoteSellersQuotesPrices[$i]->fright_hundred;
					$termcontract->fright_three_hundred = $arrayBuyerQuoteSellersQuotesPrices[$i]->fright_three_hundred;
					$termcontract->fright_five_hundred = $arrayBuyerQuoteSellersQuotesPrices[$i]->fright_five_hundred;                                        
                         $termcontract->odlcl_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->odlcl_charges;
                         $termcontract->odtwentyft_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->odtwentyft_charges;
                         $termcontract->odfortyft_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->odfortyft_charges;
                         $termcontract->frieghtlcl_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->frieghtlcl_charges;
                         $termcontract->frieghttwentft_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->frieghttwentft_charges;
                         $termcontract->frieghtfortyft_charges = $arrayBuyerQuoteSellersQuotesPrices[$i]->frieghtfortyft_charges;
                         $termcontract->lkp_lead_type_id = $arrayBuyerQuotes[0]->lkp_lead_type_id; 
					$termcontract->contract_transit_days = $arrayBuyerQuoteSellersQuotesPrices[$i]->transit_days;
					$termcontract->seller_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id;
					$termcontract->lkp_service_id = $serviceId;
					$termcontract->created_by = Auth::id();
					$termcontract->created_at = $created_at;
					$termcontract->created_ip = $createdIp;
					$termcontract->save();			
			
			}
               
               if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    $randString = 'RELOCATIONGM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                    $termcontract = new TermContract();
					$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
					$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id;
					$termcontract->contract_price = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price;
                    $termcontract->contract_no = $randString;
                    $termcontract->seller_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id;
					$termcontract->lkp_service_id = $serviceId;
					$termcontract->created_by = Auth::id();
					$termcontract->created_at = $created_at;
					$termcontract->created_ip = $createdIp;
					$termcontract->save();
               }
			
			if(isset($allRequestdata['contractquote_'.$arrayBuyerQuoteSellersQuotesPrices[$i]->id])){
			$buyer_quote_contract=$allRequestdata['contractquote_'.$arrayBuyerQuoteSellersQuotesPrices[$i]->id];
			
			$termcontract = new TermContract();
			$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
			$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id;
			
			$termcontract->contract_quantity = $buyer_quote_contract;
			
                   if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC) {
                       if($serviceId==ROAD_FTL){
                       $randString = 'FTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                       $termcontract->contract_no = $randString;
                       $termcontract->contract_price = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price;
                      }
                    }elseif($serviceId == RELOCATION_INTERNATIONAL){
                       $randString = 'RELOCATIONGM/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                    }elseif($serviceId == RELOCATION_GLOBAL_MOBILITY){
                       $randString = 'RELOCATIONGLOBALMOBILITY/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
                    }else{
				if($serviceId==ROAD_PTL){
                        $randString = 'LTL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}
				elseif($serviceId==COURIER){
					$randString = 'COURIER/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}
				elseif($serviceId==RAIL){
                        $randString = 'RAIL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}elseif($serviceId==AIR_DOMESTIC){
                        $randString = 'AIR_DOMESTIC/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}elseif($serviceId==AIR_INTERNATIONAL){
                        $randString = 'AIR_INTERNATIONAL/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}
				elseif($serviceId==OCEAN){
                        $randString = 'OCEAN/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
				}
				$termcontract->contract_no = $randString;
				$termcontract->contract_rate_per_kg = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_rate_per_kg;
				$termcontract->contract_kg_per_cft = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_kg_per_cft;
			}
			$termcontract->seller_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id;
			$termcontract->lkp_service_id = $serviceId;
			$termcontract->created_by = Auth::id();
			$termcontract->created_at = $created_at;
			$termcontract->created_ip = $createdIp;
			$termcontract->save();
			
			if ($serviceId == ROAD_FTL) {

				TermBuyerQuoteSellersQuotesPrice::where([
						"term_buyer_quote_item_id" => $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id,
						"seller_id" => $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id
				])
				->update(
						array('final_quote_price' => $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price,
								'updated_at' => $created_at,
								'updated_ip' => $createdIp,
								'updated_by' => Auth::id()
						)
				);
				
			}else{
				TermBuyerQuoteSellersQuotesPrice::where([
						"term_buyer_quote_item_id" => $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_item_id,
						"seller_id" => $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id
				])
				->update(
						array('final_rate_per_kg' => $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_rate_per_kg,
								'final_kg_per_cft' => $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_kg_per_cft,
								'updated_at' => $created_at,
								'updated_ip' => $createdIp,
								'updated_by' => Auth::id()
						)
				);
				
			}
			
			
			
	    }

	    
	    }
	    
	    $termContractsQueryData = DB::table('term_contracts');
	    $termContractsQueryData->leftjoin('term_buyer_quotes', 'term_buyer_quotes.id', '=', 'term_contracts.term_buyer_quote_id');
	    $termContractsQueryData->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.id', '=', 'term_contracts.term_buyer_quote_item_id');
	    if ($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL) {
	    	$termContractsQueryData->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id');
	    	$termContractsQueryData->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id');
	    }elseif($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC){
	    	$termContractsQueryData->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'term_buyer_quote_items.from_location_id');
	    	$termContractsQueryData->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'term_buyer_quote_items.to_location_id');
	    }elseif($serviceId == AIR_INTERNATIONAL){
	    	$termContractsQueryData->leftjoin ( 'lkp_airports as cf', 'term_buyer_quote_items.from_location_id', '=', 'cf.id' );
	    	$termContractsQueryData->leftjoin ( 'lkp_airports as ct', 'term_buyer_quote_items.to_location_id', '=', 'ct.id' );
	    
	    }elseif($serviceId == OCEAN){
	    	$termContractsQueryData->leftjoin ( 'lkp_seaports as cf', 'term_buyer_quote_items.from_location_id', '=', 'cf.id' );
	    	$termContractsQueryData->leftjoin ( 'lkp_seaports as ct', 'term_buyer_quote_items.to_location_id', '=', 'ct.id' );
	    	 
	    }elseif($serviceId == RELOCATION_GLOBAL_MOBILITY){
          $termContractsQueryData->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id');     
          $termContractsQueryData->leftjoin('lkp_relocationgm_services as rgms', 'term_buyer_quote_items.lkp_gm_service_id', '=', 'rgms.id');
         }
	    $termContractsQueryData->leftjoin('users as u','u.id', '=','term_contracts.created_by');
	    $termContractsQueryData->leftjoin('users as su','su.id', '=','term_contracts.seller_id');
	    $termContractsQueryData->where('term_contracts.seller_id', $arrayBuyerQuoteSellersQuotesPrices[0]->seller_id);
	    $termContractsQueryData->where('term_contracts.term_buyer_quote_id', $arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id);
	    $termContractsQueryData->where('term_contracts.lkp_service_id',$serviceId);
	    if ($serviceId == ROAD_FTL) {
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo');
	    }elseif($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date', 'lp.postoffice_name as from_locationcity', 'lcityp.postoffice_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
	    			,'term_buyer_quote_items.number_packages','term_buyer_quote_items.volume');
	    }elseif($serviceId == AIR_INTERNATIONAL){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date', 'cf.airport_name as from_locationcity', 'ct.airport_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
	    			,'term_buyer_quote_items.number_packages','term_buyer_quote_items.volume');
	    	
	    }
	    elseif($serviceId == OCEAN){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date', 'cf.seaport_name as from_locationcity', 'ct.seaport_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
	    			,'term_buyer_quote_items.number_packages','term_buyer_quote_items.volume');
	    
	    }elseif($serviceId == RELOCATION_DOMESTIC){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date','term_buyer_quotes.lkp_post_ratecard_type', 'c1.city_name as from_locationcity', 'c2.city_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
	    			,'term_buyer_quote_items.number_packages','term_buyer_quote_items.volume','term_buyer_quote_items.lkp_vehicle_category_id','term_buyer_quote_items.lkp_vehicle_category_type_id','term_buyer_quote_items.vehicle_model','term_buyer_quote_items.no_of_vehicles');
	    
	    }elseif($serviceId == RELOCATION_INTERNATIONAL){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date','c1.city_name as from_locationcity', 'c2.city_name as to_locationcity','u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
	    			,'term_buyer_quote_items.number_loads','term_buyer_quote_items.avg_kg_per_move','term_buyer_quote_items.lkp_vehicle_category_id','term_buyer_quote_items.lkp_vehicle_category_type_id','term_buyer_quote_items.vehicle_model','term_buyer_quote_items.no_of_vehicles');
	    
	    }elseif($serviceId == RELOCATION_GLOBAL_MOBILITY){
	    	$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date','c1.city_name as from_locationcity', 'u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id',
                  'su.id as seller_id','u.logo','rgms.service_type','term_buyer_quote_items.measurement','term_buyer_quote_items.measurement_units'
	    			);
	    
	    }
	    $termContractsData=$termContractsQueryData->get();
	     
	    
	    $html = view('pdf.term_contract')->with(['termContractsData' => $termContractsData])->render();
	     
	    $sellerDirectory = 'uploads/buyer/contract/'.Auth::id();
	     
	    if (!is_dir ( $sellerDirectory )) {
	    	 
	    	mkdir ( $sellerDirectory, 0777, true );
	    }
	    $uniqueFileName = time() ."GeneratedContract.pdf";
	    $data=array();
	    $pdf = PDF::loadHTML($html, $data);
	    $pdf->save($sellerDirectory.'/'.$uniqueFileName);
	     
	    //exit;
	    $path = $sellerDirectory.'/'.$uniqueFileName;
	    //CommonComponent::send_email(FTL_ORDER_INVOICE, $users, '1', $path,true);
	     
	    TermContract::where ( "term_buyer_quote_id", $arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id)
	    					->where("seller_id",$arrayBuyerQuoteSellersQuotesPrices[0]->seller_id)
	    					->update( array ('file_path_one' => $path));
	    
	    
	    }
		
		$users = DB::table('users')->where('id', $arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id)->get();
		 
		CommonComponent::send_email(FTL_BUYER_GENERATE_CONTRACT, $users,'1', $path,true);
		
		$users_buyer = DB::table('users')->where('id', Auth::id())->get();
			
		CommonComponent::send_email(FTL_BUYER_GENERATE_CONTRACT, $users_buyer,'1', $path,true);


			//*******Send Sms to Seller***********************//
			$msg_params = array(
				'buyerpostid' => DB::table('term_buyer_quotes')->where('id','=',$arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id)->pluck('transaction_id'),
				'buyername' => Auth::User()->username,
				'servicename' => CommonComponent::getServiceName($serviceId)
			);
			$getMobileNumber  =   CommonComponent::getMobleNumber($arrayBuyerQuoteSellersQuotesPrices[$i]->seller_id);
			CommonComponent::sendSMS($getMobileNumber,CONTRACT_ISSUANCE,$msg_params);
			//*******Send Sms to Seller***********************//
	   }

		return $randString;
		
	}
	
	
	
	
	/**
	 * Term Buyer Generate Contract
	 * Method
	 *
	 * @param int $buyerQuoteItemId
	 * @return array
	 */
	public static function generateSellerContractCourier($allRequestdata,$serviceId){
	
		$created_at = date('Y-m-d H:i:s');
		$createdIp = $_SERVER ['REMOTE_ADDR'];
	
		$path = '';
	
		//echo '<pre>';print_r($allRequestdata);exit;
		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_items as bqsqp');
		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_id', $allRequestdata['buyer_quote_id']);
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.*');
		
		$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
		//echo '<pre>';print_r($allRequestdata);exit;
		$created_year = date('Y');
		$invid  =   CommonComponent::getTermContractID();
		for($i=0;$i<count($arrayBuyerQuoteSellersQuotesPrices);$i++)
		{			
		$getBuyerQuoteSellersQuotes = DB::table('term_buyer_quotes as tbq');
		$getBuyerQuoteSellersQuotes->where('tbq.id', $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id);
		$getBuyerQuoteSellersQuotes->select('tbq.lkp_post_ratecard_type');
				$arrayBuyerQuotes = $getBuyerQuoteSellersQuotes->get();
					
				$getBuyerQuoteSellersItems = DB::table('term_buyer_quote_items as tbqi');
						$getBuyerQuoteSellersItems->where('tbqi.id', $arrayBuyerQuoteSellersQuotesPrices[$i]->id);
						$getBuyerQuoteSellersItems->select('tbqi.volume');
						$arrayBuyerItems = $getBuyerQuoteSellersItems->get();
	
							
						if(isset($allRequestdata[$allRequestdata['seller']."_".$arrayBuyerQuoteSellersQuotesPrices[$i]->id])){
						$seller_check=$allRequestdata[$allRequestdata['seller']."_".$arrayBuyerQuoteSellersQuotesPrices[$i]->id];
	
						if($seller_check=='on'){
							
							if(isset($allRequestdata['buyer_quote_id'])){
								$termcontract = new TermContract();
								$termcontract->term_buyer_quote_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->term_buyer_quote_id;
								$termcontract->term_buyer_quote_item_id = $arrayBuyerQuoteSellersQuotesPrices[$i]->id;
								$randString = 'COURIER/' .$created_year .'/'. str_pad($invid, 6, "0", STR_PAD_LEFT);
								$termcontract->contract_no = $randString;
								$termcontract->contract_quantity = 1;
								$termcontract->seller_id = $allRequestdata['seller'];
								$termcontract->lkp_service_id = COURIER;
								$termcontract->created_by = Auth::id();
								$termcontract->created_at = $created_at;
								$termcontract->created_ip = $createdIp;
								$termcontract->save();	
								}			 
		    			}
		     
		    
	
			}
		
			
			//Contract Pdf Generation
			$termContractsQueryData = DB::table('term_contracts');
			$termContractsQueryData->leftjoin('term_buyer_quotes', 'term_buyer_quotes.id', '=', 'term_contracts.term_buyer_quote_id');
			$termContractsQueryData->leftjoin('term_buyer_quote_items', 'term_buyer_quote_items.id', '=', 'term_contracts.term_buyer_quote_item_id');
			$termContractsQueryData->leftjoin ( 'lkp_ptl_pincodes as cf', 'term_buyer_quote_items.from_location_id', '=', 'cf.id' );
			$termContractsQueryData->leftjoin('lkp_ptl_pincodes as ct', function($join)
			{
				$join->on('term_buyer_quote_items.to_location_id', '=', 'ct.id');
				$join->on(DB::raw('term_buyer_quotes.lkp_courier_delivery_type_id'),'=',DB::raw(1));
					
			});
			$termContractsQueryData->leftjoin('lkp_countries as ct1', function($join)
			{
				$join->on('term_buyer_quote_items.to_location_id', '=', 'ct1.id');
				$join->on(DB::raw('term_buyer_quotes.lkp_courier_delivery_type_id'),'=',DB::raw(2));
					
			});
			
			
			$termContractsQueryData->leftjoin('users as u','u.id', '=','term_contracts.created_by');
			$termContractsQueryData->leftjoin('users as su','su.id', '=','term_contracts.seller_id');
			$termContractsQueryData->where('term_contracts.seller_id', $allRequestdata['seller']);
			$termContractsQueryData->where('term_contracts.term_buyer_quote_id', $arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id);
			$termContractsQueryData->where('term_contracts.lkp_service_id',$serviceId);
			$termContractsQueryData->select('term_contracts.*','term_buyer_quotes.from_date','term_buyer_quotes.to_date', 'cf.postoffice_name as from_locationcity',
					DB::raw("(case when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 1 then ct.postoffice_name  when `term_buyer_quotes`.`lkp_courier_delivery_type_id` = 2 then ct1.country_name end) as to_locationcity"),
					'u.username as buyername','su.username as sellername','su.is_business as sellerbusy','u.is_business as buyerbusy','u.id as buyer_id','su.id as seller_id','u.logo'
					,'term_buyer_quote_items.number_packages','term_buyer_quote_items.volume','term_buyer_quotes.lkp_courier_type_id','term_buyer_quotes.lkp_courier_delivery_type_id','term_buyer_quotes.id as termbuyerquoteid');
			$termContractsData=$termContractsQueryData->get();
			
			$html = view('pdf.term_contract')->with(['termContractsData' => $termContractsData])->render();
			
			$sellerDirectory = 'uploads/buyer/contract/'.Auth::id();
			
			if (!is_dir ( $sellerDirectory )) {
					
				mkdir ( $sellerDirectory, 0777, true );
			}
			$uniqueFileName = time() ."GeneratedContract.pdf";
			$data=array();
			$pdf = PDF::loadHTML($html, $data);
			$pdf->save($sellerDirectory.'/'.$uniqueFileName);
			
			//exit;
			$path = $sellerDirectory.'/'.$uniqueFileName;
			//CommonComponent::send_email(FTL_ORDER_INVOICE, $users, '1', $path,true);
			
			TermContract::where ( "term_buyer_quote_id", $arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id)
			->where("seller_id",$allRequestdata['seller'])
			->update( array ('file_path_one' => $path));
			
		}
			
			$users = DB::table('users')->where('id', $allRequestdata['seller'])->get();
				
			CommonComponent::send_email(FTL_BUYER_GENERATE_CONTRACT, $users,'1', $path,true);
			
			$users_buyer = DB::table('users')->where('id', Auth::id())->get();
				
			CommonComponent::send_email(FTL_BUYER_GENERATE_CONTRACT, $users_buyer,'1', $path,true);
			
			
			//*******Send Sms to Seller***********************//
			$msg_params = array(
					'buyerpostid' => DB::table('term_buyer_quotes')->where('id','=',$arrayBuyerQuoteSellersQuotesPrices[0]->term_buyer_quote_id)->pluck('transaction_id'),
					'buyername' => Auth::User()->username,
					'servicename' => CommonComponent::getServiceName($serviceId)
			);
			$getMobileNumber  =   CommonComponent::getMobleNumber($allRequestdata['seller']);
			CommonComponent::sendSMS($getMobileNumber,CONTRACT_ISSUANCE,$msg_params);
			//*******Send Sms to Seller***********************//
			
		
			
			
		return $randString;
	

	}
	
/*
 * Getting buyer order contracts list
 */
	public static function getTermBuyerContractList($order_type, $post, $data){
           
           if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY){
                 $from_locations = array("" => "From Location");
           } else {
                 $from_locations = array("" => "Location");
           }
		
		$to_locations = array("" => "To Location");
		$buyers = array("" => "Seller");
		if(isset($post['order_int_type']) && $post ['order_int_type']==2){
		$order_lead=2;	
		}else{
		$order_lead=1;
		}
		// query to retrieve seller posts list and bind it to the grid
		$query = DB::table('term_contracts');
		$query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'term_contracts.term_buyer_quote_id');
		$query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'term_contracts.term_buyer_quote_item_id');
                 $serviceId = Session::get('service_id');
                switch ($serviceId) {
                    case ROAD_FTL :
                    case RELOCATION_DOMESTIC :
                    case RELOCATION_INTERNATIONAL :
                    case RELOCATION_GLOBAL_MOBILITY :
                    $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
                    $query->leftJoin('users as u', 'u.id', '=', 'term_contracts.seller_id');                    
                    break;
                    case ROAD_PTL: 
                    case RAIL:
                    case AIR_DOMESTIC:
                    $query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
                    $query->leftJoin('users as u', 'u.id', '=', 'term_contracts.seller_id');
                    break;
                    
                    case COURIER:
                    $query->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'tbqi.from_location_id');
                    
                    $query->leftjoin('lkp_ptl_pincodes as lcityp', function($join)
                    {
                    	$join->on('tbqi.to_location_id', '=', 'lcityp.id');
                    	$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                    
                    });
                    $query->leftjoin('lkp_countries as lppt1', function($join)
                    {
                    	$join->on('tbqi.to_location_id', '=', 'lppt1.id');
                    	$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                    
                    });
                    
                    $query->leftJoin('users as u', 'u.id', '=', 'term_contracts.seller_id');
                    break;
                    	
                    case AIR_INTERNATIONAL:
                    $query->leftJoin('lkp_airports as lp', 'lp.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_airports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
                    $query->leftJoin('users as u', 'u.id', '=', 'term_contracts.seller_id');
                    break;
                    case OCEAN:
                    $query->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
                    $query->leftJoin('users as u', 'u.id', '=', 'term_contracts.seller_id');
                    break;
                    case ROAD_INTRACITY :
                    $query->leftJoin('lkp_ict_locations as lc', 'lc.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_ict_locations as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
                    break;
                    default :
                    $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'tbqi.from_location_id');
                    $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
                    break;
                }
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'term_contracts.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'term_contracts.contract_status');
		$query->groupBy('term_contracts.contract_no');
		$query->where('term_contracts.created_by', '=', Auth::user()->id);
		if ($serviceId == ROAD_FTL) {
			$query->where('term_contracts.lkp_service_id','=',ROAD_FTL);
		} elseif ($serviceId == ROAD_PTL) {
			$query->where('term_contracts.lkp_service_id','=',ROAD_PTL);
		} elseif ($serviceId == RAIL) {
			$query->where('term_contracts.lkp_service_id','=',RAIL);
		} elseif ($serviceId == COURIER) {
			$query->where('term_contracts.lkp_service_id','=',COURIER);
		} elseif ($serviceId == AIR_DOMESTIC) {
			$query->where('term_contracts.lkp_service_id','=',AIR_DOMESTIC);
		} elseif ($serviceId == AIR_INTERNATIONAL) {
			$query->where('term_contracts.lkp_service_id','=',AIR_INTERNATIONAL);
		} elseif ($serviceId == OCEAN) {
			$query->where('term_contracts.lkp_service_id','=',OCEAN);
		} elseif ($serviceId == RELOCATION_DOMESTIC) {
			$query->where('term_contracts.lkp_service_id','=',RELOCATION_DOMESTIC);
		} elseif ($serviceId == RELOCATION_INTERNATIONAL) {
			$query->where('term_contracts.lkp_service_id','=',RELOCATION_INTERNATIONAL);
			$query->where('term_contracts.lkp_lead_type_id','=',$order_lead);
			
		} elseif ($serviceId == RELOCATION_GLOBAL_MOBILITY) {
			$query->where('term_contracts.lkp_service_id','=',RELOCATION_GLOBAL_MOBILITY);
		}
		
                
		//conditions to make search
		
		if(Session::get ( 'service_id' )  == COURIER){
			$query->where('tbq.lkp_courier_delivery_type_id', '=', Session::get('delivery_type'));
		}
		
		if (isset($post['service_id']) && $data['service_id'] != '') {
			$query->where('term_contracts.lkp_service_id', $post['service_id']);
		}
		if (isset($post['status_id']) && $data['status_id'] != '') {
			$query->where('term_contracts.contract_status', $post['status_id']);
		}
		
		if (isset ( $post ['start_dispatch_date'] ) && $post ['start_dispatch_date'] != '') {
			$query->where ( 'tbq.from_date', '>=', CommonComponent::convertDateForDatabase($post ['start_dispatch_date']) );
				
		}
		if (isset ( $post ['end_dispatch_date'] ) && $post ['end_dispatch_date'] != '') {
			$query->where ( 'tbq.from_date', '<=', CommonComponent::convertDateForDatabase($post ['end_dispatch_date']) );
				
		}
		
		switch ($serviceId) {
                    case ROAD_FTL :
                    case RELOCATION_DOMESTIC :	
                    case RELOCATION_INTERNATIONAL :
                    case RELOCATION_GLOBAL_MOBILITY :
                    $orderresults = $query->select('term_contracts.*','tbq.from_date','tbq.to_date', 'os.order_status as order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city','u.username')
		->get();
                        break;
                    case ROAD_PTL: 
                    case RAIL: 
                    case AIR_DOMESTIC:
                    $orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city','u.username')
		->get();
					break;
					case COURIER:
					$orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.postoffice_name as from_city',
					 DB::raw("(case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcityp.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lppt1.country_name end) as to_city"),
					 'u.username')
					->get();
                    break;
                    
                    case AIR_INTERNATIONAL:
                    $orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.airport_name as from_city', 'lcityp.airport_name as to_city','u.username')
		->get();
                        break;
                    case OCEAN:
                    $orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.seaport_name as from_city', 'lcityp.seaport_name as to_city','u.username')
		->get();
                        break;
                    case ROAD_INTRACITY :
                    $orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city','u.username')
		->get();
                        break;
                    default :
                    $orderresults = $query->select('term_contracts.*', 'tbq.from_date','tbq.to_date', 'os.order_status as order_status',  'lp.postoffice_name as from_city', 'lcityp.postoffice_name as to_city','u.username')
		->get();
                    break;
                }      

                
                if(Session::get ( 'service_id' )  == COURIER){
					$order_items = DB::table('term_buyer_quote_items as bqi')
                	->leftjoin('term_contracts as tc','tc.term_buyer_quote_item_id','=','bqi.id')
                	->leftjoin('term_buyer_quotes as bq','bqi.term_buyer_quote_id','=','bq.id')
                	->leftjoin('users as u','u.id','=','tc.seller_id')
                	->where ( 'bqi.created_by', Auth::User ()->id )
                	->where ( 'tc.lkp_service_id', $serviceId)
                	->where ( 'bq.lkp_courier_delivery_type_id', Session::get('delivery_type'))
                	->select('bqi.*','u.username')
                	->get();
                }else{
                	$order_items = DB::table('term_buyer_quote_items as bqi')
                	->leftjoin('term_contracts as tc','tc.term_buyer_quote_item_id','=','bqi.id')
                	->leftjoin('users as u','u.id','=','tc.seller_id')
                	->where ( 'bqi.created_by', Auth::User ()->id )
                	//->where ( 'tc.lkp_lead_type_id', $order_lead )
                	->where ( 'tc.lkp_service_id', $serviceId)
                	->select('bqi.*','u.username')
                	->get();
                }
               // echo "<pre>"; print_r($order_items); die;
			foreach ($order_items as $order_item) {
                if (!isset($from_locations[$order_item->from_location_id])) {
					switch ($serviceId) {
                    case ROAD_FTL :
                    case RELOCATION_DOMESTIC :
                    case RELOCATION_INTERNATIONAL :
                    case RELOCATION_GLOBAL_MOBILITY :
                    $from_locations[$order_item->from_location_id] = DB::table('lkp_cities')->where('id', $order_item->from_location_id)->pluck('city_name');
                        break;
                    case ROAD_PTL :
                    case RAIL :
                    case AIR_DOMESTIC:
                    $from_locations[$order_item->from_location_id] = DB::table('lkp_ptl_pincodes')->where('id', $order_item->from_location_id)->pluck('postoffice_name');
                        break;
                        
                     case COURIER:
                        $from_locations[$order_item->from_location_id] = DB::table('lkp_ptl_pincodes')->where('id', $order_item->from_location_id)->pluck('postoffice_name');
                        break;
                    case AIR_INTERNATIONAL:
                    $from_locations[$order_item->from_location_id] = DB::table('lkp_airports')->where('id', $order_item->from_location_id)->pluck('airport_name');
                        break;
                    case OCEAN:
                    $from_locations[$order_item->from_location_id] = DB::table('lkp_seaports')->where('id', $order_item->from_location_id)->pluck('seaport_name');
                        break;
                    case ROAD_INTRACITY :
                        $from_locations[$order_item->from_location_id] = DB::table('lkp_ict_locations')->where('id', $order_item->from_location_id)->pluck('ict_location_name');
                        break;
                    default :
                   $from_locations[$order_item->from_location_id] = DB::table('lkp_cities')->where('id', $order_item->from_location_id)->pluck('city_name');
                    break;
                }
				}
				if (!isset($to_locations[$order_item->to_location_id])) {
                    switch ($serviceId) {
                    case ROAD_FTL :
                    case RELOCATION_DOMESTIC :
                    case RELOCATION_INTERNATIONAL :
                    $to_locations[$order_item->to_location_id] = DB::table('lkp_cities')->where('id', $order_item->to_location_id)->pluck('city_name');
                        break;
                    case ROAD_PTL:
                    case RAIL :
                    case AIR_DOMESTIC:
                    $to_locations[$order_item->to_location_id] = DB::table('lkp_ptl_pincodes')->where('id', $order_item->to_location_id)->pluck('postoffice_name');
                        break;
                        
                    case COURIER :
                    	if(Session::get('delivery_type') == 1){
                    	$to_locations[$order_item->to_location_id] = DB::table('lkp_ptl_pincodes')->where('id', $order_item->to_location_id)->pluck('postoffice_name');
                    	}else{
                    		$to_locations[$order_item->to_location_id] = DB::table('lkp_countries')->where('id', $order_item->to_location_id)->pluck('country_name');
                    	}
                    	break;
                        
                    case AIR_INTERNATIONAL:
                    $to_locations[$order_item->to_location_id] = DB::table('lkp_airports')->where('id', $order_item->to_location_id)->pluck('airport_name');
                        break;
                    case OCEAN:
                    $to_locations[$order_item->to_location_id] = DB::table('lkp_seaports')->where('id', $order_item->to_location_id)->pluck('seaport_name');
                        break;
                    case ROAD_INTRACITY :
                    $to_locations[$order_item->to_location_id] = DB::table('lkp_ict_locations')->where('id', $order_item->to_location_id)->pluck('ict_location_name');
                        break;
                    default :
                   $to_locations[$order_item->to_location_id] = DB::table('lkp_cities')->where('id', $order_item->to_location_id)->pluck('city_name');
                    break;
                }
				}
                    if (! isset ( $buyers [$order_item->username] )) {
					$buyers [$order_item->username] = $order_item->username;
				}
			}
		//}
		//Functionality to handle filters based on the selection ends		
		$grid = DataGrid::source($query);		
		$grid->attributes(array("class" => "table table-striped"));		
		$grid->add('id', 'ID', false)->style('display:none');
		$grid->add ('contract_no', 'Contract No', 'contract_no')->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add('username', 'Vendor Name', 'username')->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'bid_end_date', '', false)->style ( "display:none" );
		$grid->add ('contract_status', 'Status', true)->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ('placeindent','','')->style ( "display:none" );
		$grid->add ('a','','')->style ( "display:none" );
		$grid->add ('term_buyer_quote_id', 'term_buyer_quote_id', 'term_buyer_quote_id')->style ( "display:none" );
		$grid->add ( 'lkp_quote_access_id', 'Buyer access_id', 'lkp_quote_access_id' )->style ( "display:none" );
		$grid->add ( 'term_buyer_quote_id', 'Buyer id', 'buyer_quote_id' )->style ( "display:none" );
		$grid->add ( 'lkp_post_status_id', 'Post status id', 'lkp_post_status_id' )->style ( "display:none" );
		$grid->add ( 'from_city_id', 'from city id', 'from_city_id' )->style ( "display:none" );
		$grid->add ( 'lkp_quote_price_type_id', 'Price Type', true )->style ( "display:none" );
		$grid->add ( 'seller_id', 'seller_id', 'seller_id' )->style ( "display:none" );
		$grid->add ( 'from_city', 'from_city', false )->style ( "display:none" );
		$grid->add ( 'to_city', 'to_city', false )->style ( "display:none" );
		
		$grid->orderBy('id', 'desc');
		$grid->paginate(5);

		$grid->row(function ($row) {
            $buyerContractId = $row->cells [0]->value;
            $seller_id = $row->cells [14]->value;
            $row->cells [14]->style('display:none');
            $row->cells [15]->style('display:none');
           $row->cells [16]->style('display:none');
            $row->cells [9]->style('display:none');
            $buyerQuoteId = $row->cells [9]->value;
            $row->cells[0]->value = '<a href=/contract/buyerdetails/'.$buyerContractId.'>';
			$row->cells [0]->style('display:none');
			$contractNo = $row->cells [1]->value;
			$row->cells [1]->value = "<div class='col-md-2 padding-left-none'>".$contractNo."
										<div class='red'>
										<i class='fa fa-star'></i>
										<i class='fa fa-star'></i>
										<i class='fa fa-star'></i>
										</div></div>";									
			$SellerName = $row->cells [2]->value;
			$row->cells [2]->value = "<div class='col-md-2 padding-left-none'>".$SellerName."</div>";
			$validFrom = ''.CommonComponent::checkAndGetDate($row->cells [3]->value).'';
			$row->cells [3]->value = "<div class='col-md-2 padding-left-none'>".$validFrom."</div>"; 			
			$validTo =  ''.CommonComponent::checkAndGetDate($row->cells [4]->value).'';
			$row->cells [4]->value = "<div class='col-md-2 padding-left-none'>".$validTo."</div>";
			$validFrom = str_replace("/","-",$validFrom);
			$validTo = str_replace("/","-",$validTo);
			$validFrom = date('Y-m-d', strtotime($validFrom));
			$validTo = date('Y-m-d', strtotime($validTo));
			$status = $row->cells [6]->value;
			if ($status == PENDING_ACCEPTANCE) {
				$updatedStatus = 'Pending Acceptance';
				$placeindentButton = '<a id="cancel_buyer_term" data-id='.$buyerContractId.' class="btn red-btn pull-right cancel_buyer_term">Cancel Contract</a>';
			} elseif ($status == CONTRACT_ACCEPTED) {
				$updatedStatus = 'Contract Accepted';
				$placeindentButton = '<button class="btn add-btn pull-right show-data-cust">Place Indent</button>';
				//Check the condtion for place idnent button checking currentdate conditions				
				/*if ($currentDateTerm >=$validFrom && $currentDateTerm <=$validTo) {
				$placeindentButton = '<button class="btn post-btn pull-right sm-cust-btn show-data-cust">Place Indent</button>';
				} else {
				$placeindentButton = '';
				}*/
			} elseif ($status == CONTRACT_CANCELLED) {
				$updatedStatus = 'Cancel Contract';
				$placeindentButton = '';
			} elseif ($status == ORDER_CANCELLED) {
				$updatedStatus = 'Cancelled';
				$placeindentButton = '';
			}
			$row->cells[6]->value = '<div class="col-md-2 padding-left-none">'.$updatedStatus.'</div>';
			$row->cells[7]->value = '<div class="col-md-2 padding-none pull-right">
											'.$placeindentButton.'
										</div>';	
			$buyer_quote_id= $row->cells[9]->value;
			$serviceId = Session::get('service_id');
            $msg_cnt = MessagesComponent::getPerticularMessageDetailsCount(null,$buyerContractId,1);
            $details_contract  =    TermBuyerComponent::getTermContractList($row->data->term_buyer_quote_id,$serviceId);
            $row->cells [8]->value = "</a><div class='clearfix'></div>
									<div class='pull-left'>
										<div class='info-links'>
											<a href='/getmessagedetails/0/".$buyerContractId."/1'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".$msg_cnt."</span></a>
											<a href='#'><i class='fa fa-file-text-o'></i> Status<span class='badge'></span></a>
											<a href='#'><i class='fa fa-file-text-o'></i> Documents<span class='badge'></span></a>
										</div>
									</div>
									<div class='col-md-2 padding-none text-right pull-right'>
											<div class='info-links'>
												<a id='".$seller_id."' class='show-data-link'><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>
												<a href='#' class='new_message' data-userid='".$seller_id."' data-term='1' data-contractid='".$buyerContractId."'><i class='fa fa-envelope-o'></i></a>
											</div>
									</div>
									<div class='col-md-12 show-data-div spot_transaction_details_view_list' id='spot_transaction_details_view_'".$buyerContractId."'>
										<div class='col-md-12 tab-modal-head padding-none margin-bottom'>
										<h3><span class='close-icon'>x</span></h3><br>";
            
            
                  if(Session::get ( 'service_id' )  == RELOCATION_GLOBAL_MOBILITY){
                       
            
            		$row->cells [8]->value .="<div class='table-row'>
            			<h4 class='from-to-locations'><i class='fa fa-map-marker'></i> ".$details_contract[0]->from."</h4>
										</div>";
                    
                  } else {
                        foreach($details_contract as $details){
            
            		$row->cells [8]->value .="<div class='table-row'>
            			<h4 class='from-to-locations'><i class='fa fa-map-marker'></i> ".$details->from." to ".$details->to."</h4>
										</div>";
                     }
                  }
                    
            		$row->cells [8]->value .="</div>
										<div class='col-md-12 padding-none'>
											<div class='col-md-3 padding-left-none data-fld'>
												<span class='data-head'>From Date</span>
												<span class='data-value'>".CommonComponent::checkAndGetDate($validFrom)."</span>
											</div>
											<div class='col-md-3 padding-left-none data-fld'>
												<span class='data-head'>To Date</span>
												<span class='data-value'>".CommonComponent::checkAndGetDate($validTo)."</span>
											</div>	
											<div class='col-md-4 padding-left-none  padding-right-none link-text pull-right padding-top-8'>
											<a class='pull-right' href='/getcontractdownload/$buyerContractId'>Download Contract</a></div>										
											<div class='clearfix'></div>
										</div>										
										</div>";
                        $row->attributes(array("class" => ""));
				});
		
		//Functionality to build filters in the page starts
		$filter = DataFilter::source($query);
		$filter->add('tbqi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
		$filter->add('tbqi.to_location_id', 'To Location', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
		$filter->add('u.username', 'Seller', 'select')->options($buyers)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
		$filter->add('term_contracts.contract_no', 'Seller', 'text')->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
		$filter->submit('search');
		$filter->reset('reset');
		$filter->build();		
		$result = array();
		$result['grid'] = $grid;
		$result['filter'] = $filter;
		return $result;		
	}
	
	/**
	 * get buyer counter offer page
	 * Cancel term
	 * @param integer $buyerQuoteItemId
	 * @return type
	 */
	public static function cancelBuyerTermQuote($buyerContractId, $serviceId)
	{
		Log::info('Cancel the quote enquiry for ftl term: '.Auth::id(),array('c'=>'2'));
		try{
			$roleId = Auth::User()->lkp_role_id;
			if($roleId == BUYER){
				CommonComponent::activityLog("BUYER_CANCELED_TERM_ORDER",
				BUYER_CANCELED_TERM_ORDER,0,
				HTTP_REFERRER,CURRENT_URL);
			}
			//Save data into txnprojectinviteerequests
			$updatedAt = date ('Y-m-d H:i:s');
			$updatedIp = $_SERVER['REMOTE_ADDR'];
			$updatedBy = Auth::User()->user_id;
			//update order contract status.
			TermContract::where(["id" => $buyerContractId])->where(["lkp_service_id" => $serviceId])
			->update(
			array(
			'contract_status' => CONTRACT_CANCELLED,
			'updated_at' => $updatedAt,
			'updated_ip' => $updatedIp,
			'updated_by' => $updatedBy
			)
			);			
			return ['cancelsuccessmessage' => 'Term cancelled successfully.'];
			//Save data into txnprojectinviteerequests
		} catch (Exception $e) {
	
		}
	}

	
	public static function getContractQuantity($seller_id,$quote_item_id){
				
		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_contracts as bqsqp');
		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.seller_id', $seller_id);
		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_item_id', $quote_item_id);
		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.contract_quantity');
		$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
		//echo 
		if(count($arrayBuyerQuoteSellersQuotesPrices)>0){
		  $sellerquantity=$arrayBuyerQuoteSellersQuotesPrices[0]->contract_quantity;	
		}else{
		  $sellerquantity='';
		}
	
		return $sellerquantity;		
	}
 
	/**
	 * Get Indents history using term Id
	 * Param id
	 */

	public static function getIndentsByContractId($contractId,$serviceId){
		if(!empty($contractId)){
			return DB::table('term_contracts_indent_quantities')->where('term_contract_id', $contractId)->where('lkp_service_id', $serviceId)->get();
		}
		return false;
	}
	

	/***** Post cancel function in term
	 *Added switch cases for all services for term.
	 */
	public static function getTermPostCancel($serviceId, $post_type, $postIds ) {
		Log::info('Cancel the buyer post term: '.Auth::id(),array('c'=>'2'));
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ["REMOTE_ADDR"];
	try {
				//check condition for post status open or not.
				
				$checkInTerms = DB::table('term_contracts as tc')
				->where('tc.term_buyer_quote_id', $postIds)
				->where('tc.lkp_service_id', $serviceId)
				->select('tc.term_buyer_quote_id')
				->get();				
				if(isset($checkInTerms) && !empty($checkInTerms)){					
						return "Your post exists in contract";
						return 0;
					}else{
						$checkstatus	=DB::table('term_buyer_quotes as bq')
						->where('bq.id', $postIds)
						->where('bq.lkp_service_id', $serviceId)
						->select('bq.lkp_post_status_id')
						->get();						
						foreach ($checkstatus as $query) {
							$results[] = $query->lkp_post_status_id;
						}					
						if (!in_array("5", $results) && !in_array("3", $results) && !in_array("4", $results) && !in_array("7", $results))
						{
							TermBuyerQuote::where ( "id", $postIds )->update ( array (
							'lkp_post_status_id' => CANCELLED,
							'updated_at' => $updatedAt,
						 	'updated_by' => Auth::User ()->id,
							'updated_ip' => $updatedIp
							));
							return "Buyer posts successfully deleted";
						} else {
							return "Please select open posts only";
							return 0;
						}
					}		
		 
	} catch ( Exception $ex ) {
		 
		return 0;
	}
}
	/**
	 * Compare quotes in excel
	 */
	
	public static function getTermCompareQuotePrices($buyerQuoteId,$serviceId){
		
		$lowestrowsfull = array();
		$sellerwiserates = array();
		
		switch($serviceId){
			case ROAD_FTL     :
			
				$getBuyerQuoteItems=DB::table('term_buyer_quote_items as tbqi');
				$getBuyerQuoteItems->where('term_buyer_quote_id',$buyerQuoteId);
				$getBuyerQuoteItems->where('lkp_service_id',$serviceId);
				$getBuyerQuoteItems->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
				$getBuyerQuoteItems->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
				$getBuyerQuoteItems->select('tbqi.id','tbqi.quantity','cf.city_name as fromcity','ct.city_name as tocity');
				$arrayBuyerQuoteItems = $getBuyerQuoteItems->get();
				
				
				
				foreach($arrayBuyerQuoteItems as $buyeritem) {
					$lowestrow = array();
					$lowestrow[] = $buyeritem->fromcity;
					$lowestrow[] = $buyeritem->tocity;
					$lowestrow[] = $buyeritem->quantity;
				    
					$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
					$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_item_id', $buyeritem->id);
					$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
					$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id','bqsqp.initial_quote_price');
					$getBuyerQuoteSellersQuotesPricesQuery->orderBy('bqsqp.initial_quote_price', 'asc');
					$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
					
					for($i=0;$i<=2;$i++){
						if(isset($arrayBuyerQuoteSellersQuotesPrices[$i])){
							$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price;
							//$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price * $buyeritem->quantity;
						}else{
							$lowestrow[] = "-";
							//$lowestrow[] = "-";
						}
					}
					for($i=0;$i<=2;$i++){
						if(isset($arrayBuyerQuoteSellersQuotesPrices[$i])){
							$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_quote_price * $buyeritem->quantity;
						}else{
							$lowestrow[] = "-";
						}
					}
					
					$lowestrowsfull[] = $lowestrow;
					Session::put('lowestquotes', $lowestrowsfull);
				//echo "<pre>";
				//print_r($lowestrowsfull);
				//exit;
					
			}
			
			$getBuyerQuoteSellersQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
			$getBuyerQuoteSellersQuery->leftJoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
			$getBuyerQuoteSellersQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
			$getBuyerQuoteSellersQuery->groupBy('bqsqp.seller_id');
			$getBuyerQuoteSellersQuery->where('bqsqp.lkp_service_id', $serviceId);
			$getBuyerQuoteSellersQuery->select('bqsqp.seller_id','u.username');
			$arrayBuyerQuoteSellers = $getBuyerQuoteSellersQuery->get();
			
			
			//$i=0;
			$lowestquoterows = Session::get ( 'lowestquotes' );
			foreach($arrayBuyerQuoteSellers as $seller) {
				
				
				
				$sellerOne = array(
						array(''),
		 				array($seller->username),
		 				array('From','To','Quantity','Quote','Value'),
		 				);
					
				
			$getBuyerQuoteSellersPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
			$getBuyerQuoteSellersPricesQuery->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
			$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
			$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
			$getBuyerQuoteSellersPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
			$getBuyerQuoteSellersPricesQuery->where('bqsqp.seller_id', $seller->seller_id);
			$getBuyerQuoteSellersPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
			$getBuyerQuoteSellersPricesQuery->select('bqsqp.id','bqsqp.initial_quote_price','tbqi.quantity','cf.city_name as fromcity','ct.city_name as tocity');
			$arrayBuyerQuoteSellersPrices = $getBuyerQuoteSellersPricesQuery->get();
			
			$subtotal = 0;
			foreach($arrayBuyerQuoteSellersPrices as $arrayBuyerQuoteSellersPrice){
				$singlesellerprice = array();
				$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->fromcity;
				$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->tocity;
				$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->quantity;
				$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->initial_quote_price;
				$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->initial_quote_price * $arrayBuyerQuoteSellersPrice->quantity;
				foreach ($lowestquoterows as $lowestrow) {
					if($arrayBuyerQuoteSellersPrice->initial_quote_price * $arrayBuyerQuoteSellersPrice->quantity==$lowestrow[6]){
						$singlesellerprice[] = 'L1';
					}
					if($arrayBuyerQuoteSellersPrice->initial_quote_price * $arrayBuyerQuoteSellersPrice->quantity==$lowestrow[7]){
						$singlesellerprice[] = 'L2';
					}
					if($arrayBuyerQuoteSellersPrice->initial_quote_price * $arrayBuyerQuoteSellersPrice->quantity==$lowestrow[8]){
						$singlesellerprice[] = 'L3';
					}
				}
				unset($singlesellerprice[6]);unset($singlesellerprice[7]);
				$sellerOne[] = $singlesellerprice;
				$subtotal+=$arrayBuyerQuoteSellersPrice->initial_quote_price * $arrayBuyerQuoteSellersPrice->quantity;
			}
			$sellerOne[] = array("","","","Sub Total",  $subtotal );
			
			$sellerwiserates[] = $sellerOne;
			
			
			
			}
			
			Session::put('sellerquotes', $sellerwiserates);
				
				
			BuyerComponent::geneartaeQCSFTL($sellerwiserates);
		
		 break;
		 
		    case ROAD_PTL   :
			case RAIL       :
			case AIR_DOMESTIC:
			case AIR_INTERNATIONAL:
			case OCEAN:
		 	
		 	$getBuyerQuoteItems=DB::table('term_buyer_quote_items as tbqi');
		 	$getBuyerQuoteItems->where('term_buyer_quote_id',$buyerQuoteId);
		 	$getBuyerQuoteItems->where('lkp_service_id',$serviceId);
		 	$getBuyerQuoteItems->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'tbqi.lkp_load_type_id');
		 	$getBuyerQuoteItems->leftjoin ( 'lkp_packaging_types as lpt', 'tbqi.lkp_packaging_type_id', '=', 'lpt.id' );
		 	switch ($serviceId) {
		 	case ROAD_PTL:
		 	case RAIL:
		 	case AIR_DOMESTIC:
		 		$getBuyerQuoteItems->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteItems->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		break;
		 	case AIR_INTERNATIONAL:
		 		$getBuyerQuoteItems->leftJoin('lkp_airports as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteItems->leftJoin('lkp_airports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		
		 		break;
		 	case OCEAN:
		 		$getBuyerQuoteItems->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteItems->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		break;
		 	}
		 	switch ($serviceId) {
		 		case ROAD_PTL:
		 		case RAIL:
		 		case AIR_DOMESTIC:
		 			$getBuyerQuoteItems->select('tbqi.id','tbqi.volume','tbqi.units','tbqi.number_packages','lp.postoffice_name as fromcity','lcityp.postoffice_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 	    break;
		 	    case AIR_INTERNATIONAL:
		 	    	$getBuyerQuoteItems->select('tbqi.id','tbqi.volume','tbqi.units','tbqi.number_packages','lp.airport_name as fromcity', 'lcityp.airport_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 	    break;
		 	    case OCEAN:
		 	    	$getBuyerQuoteItems->select('tbqi.id','tbqi.volume','tbqi.units','tbqi.number_packages','lp.seaport_name as fromcity', 'lcityp.seaport_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 	    break;
		 	 }
		 	$arrayBuyerQuoteItems = $getBuyerQuoteItems->get();
		 	
		 	
		 	foreach($arrayBuyerQuoteItems as $buyeritem) {
		 		$lowestrow = array();
		 		$lowestrow[] = $buyeritem->fromcity;
		 		$lowestrow[] = $buyeritem->tocity;
		 		$lowestrow[] = $buyeritem->load_type;
		 		$lowestrow[] = $buyeritem->packaging_type_name;
		 		$lowestrow[] = $buyeritem->volume;
		 		$lowestrow[] = $buyeritem->number_packages;
		 		
		 	
		 		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_item_id', $buyeritem->id);
		 		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		 		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft'
		 				,DB::raw('bqsqp.initial_rate_per_kg * bqsqp.initial_kg_per_cft as tot_price'));
		 		$getBuyerQuoteSellersQuotesPricesQuery->orderBy('tot_price', 'asc');
		 		$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
		 			
		 		for($i=0;$i<=3;$i++){
		 			if(isset($arrayBuyerQuoteSellersQuotesPrices[$i])){
		 				$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->initial_rate_per_kg*$arrayBuyerQuoteSellersQuotesPrices[$i]->initial_kg_per_cft*$buyeritem->volume;
		 				
		 			}else{
		 				$lowestrow[] = "-";
		 				
		 			}
		 		}
		 		
		 		$lowestrowsfull[] = $lowestrow;
		 		Session::put('lowestquotesother', $lowestrowsfull);
		 		
		 		
		 		
		 			
		 	}
		 	
		 	$getBuyerQuoteSellersQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 	$getBuyerQuoteSellersQuery->leftJoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
		 	$getBuyerQuoteSellersQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		 	$getBuyerQuoteSellersQuery->groupBy('bqsqp.seller_id');
		 	$getBuyerQuoteSellersQuery->where('bqsqp.lkp_service_id', $serviceId);
		 	$getBuyerQuoteSellersQuery->select('bqsqp.seller_id','u.username');
		 	$arrayBuyerQuoteSellers = $getBuyerQuoteSellersQuery->get();
		 	
		 	$lowestquoterows = Session::get ( 'lowestquotesother' );
		 	foreach($arrayBuyerQuoteSellers as $seller) {
		 	
		 	$sellerOne = array(
		 				array(''),
		 				array($seller->username),
		 				array('From Location','To Location','Load Type','Package Type','Volume','No of packages','Rate per KG','Conversion KG per CFT','Value','Ranking'),
		 				);
		 			
		 	
		 		$getBuyerQuoteSellersPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
		 		$getBuyerQuoteSellersPricesQuery->leftjoin('lkp_load_types as ldt', 'ldt.id', '=', 'tbqi.lkp_load_type_id');
		 		$getBuyerQuoteSellersPricesQuery->leftjoin ( 'lkp_packaging_types as lpt', 'tbqi.lkp_packaging_type_id', '=', 'lpt.id' );
		 	switch ($serviceId) {
		 	case ROAD_PTL:
		 	case RAIL:
		 	case AIR_DOMESTIC:
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_ptl_pincodes as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_ptl_pincodes as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		break;
		 	case AIR_INTERNATIONAL:
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_airports as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_airports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		
		 		break;
		 	case OCEAN:
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_seaports as lp', 'lp.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_seaports as lcityp', 'lcityp.id', '=', 'tbqi.to_location_id');
		 		break;
		 	}
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.seller_id', $seller->seller_id);
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		 		switch ($serviceId) {
		 			case ROAD_PTL:
		 			case RAIL:
		 			case AIR_DOMESTIC:
		 				$getBuyerQuoteSellersPricesQuery->select('bqsqp.id','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft','tbqi.volume','tbqi.units','tbqi.number_packages','lp.postoffice_name as fromcity','lcityp.postoffice_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 				break;
		 			case AIR_INTERNATIONAL:
		 				$getBuyerQuoteSellersPricesQuery->select('bqsqp.id','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft','tbqi.volume','tbqi.units','tbqi.number_packages','lp.airport_name as fromcity', 'lcityp.airport_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 				break;
		 			case OCEAN:
		 				$getBuyerQuoteSellersPricesQuery->select('bqsqp.id','bqsqp.initial_rate_per_kg','bqsqp.initial_kg_per_cft','tbqi.volume','tbqi.units','tbqi.number_packages','lp.seaport_name as fromcity', 'lcityp.seaport_name as tocity','ldt.load_type','lpt.packaging_type_name');
		 				break;
		 		}
		 		
		 		$arrayBuyerQuoteSellersPrices = $getBuyerQuoteSellersPricesQuery->get();
		 		
		 		foreach($arrayBuyerQuoteSellersPrices as $arrayBuyerQuoteSellersPrice){
		 			$singlesellerprice = array();
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->fromcity;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->tocity;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->load_type;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->packaging_type_name;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->volume;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->number_packages;;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->initial_rate_per_kg;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->initial_kg_per_cft;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->initial_rate_per_kg*$arrayBuyerQuoteSellersPrice->initial_kg_per_cft*$arrayBuyerQuoteSellersPrice->volume;
		 			foreach ($lowestquoterows as $lowestrow) {
		 				if($arrayBuyerQuoteSellersPrice->initial_rate_per_kg*$arrayBuyerQuoteSellersPrice->initial_kg_per_cft*$arrayBuyerQuoteSellersPrice->volume==$lowestrow[6]){
		 					$singlesellerprice[] = 'L1';
		 				}
		 				if($arrayBuyerQuoteSellersPrice->initial_rate_per_kg*$arrayBuyerQuoteSellersPrice->initial_kg_per_cft*$arrayBuyerQuoteSellersPrice->volume==$lowestrow[7]){
		 					$singlesellerprice[] = 'L2';
		 				}
		 				if($arrayBuyerQuoteSellersPrice->initial_rate_per_kg*$arrayBuyerQuoteSellersPrice->initial_kg_per_cft*$arrayBuyerQuoteSellersPrice->volume==$lowestrow[8]){
		 					$singlesellerprice[] = 'L3';
		 				}
		 			}
					unset($singlesellerprice[10]);unset($singlesellerprice[11]);
		 			$sellerOne[] = $singlesellerprice;
		 		}
		 			
		 		$sellerwiserates[] = $sellerOne;
		 			
		 			
		 			
		 	}
		 	
		 	
		 	Session::put('sellerquotesother', $sellerwiserates);
		 	
		 	BuyerComponent::geneartaeQCSOtherServices($sellerwiserates);
		 	
		 break;	
		 
		 case RELOCATION_DOMESTIC     :
		 		
		 	
		 	$getBuyerQuotes=DB::table('term_buyer_quotes as tbq');
		 	$getBuyerQuotes->where('id',$buyerQuoteId);
		 	$getBuyerQuotes->where('lkp_service_id',$serviceId);
		 	$getBuyerQuotes->select('tbq.lkp_post_ratecard_type');
		 	$arrayBuyerQuotes = $getBuyerQuotes->get();
		 	
		    if($arrayBuyerQuotes[0]->lkp_post_ratecard_type==1){
		    	
		    	$getBuyerQuoteItems=DB::table('term_buyer_quote_items as tbqi');
		    	$getBuyerQuoteItems->where('term_buyer_quote_id',$buyerQuoteId);
		    	$getBuyerQuoteItems->where('lkp_service_id',$serviceId);
		    	$getBuyerQuoteItems->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
		    	$getBuyerQuoteItems->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
		    	$getBuyerQuoteItems->select('tbqi.id','tbqi.volume','tbqi.number_packages','cf.city_name as fromcity','ct.city_name as tocity');
		    	$arrayBuyerQuoteItems = $getBuyerQuoteItems->get();
		    	
		    	
		 	foreach($arrayBuyerQuoteItems as $buyeritem) {
		 		$lowestrow = array();
		 		$lowestrow[] = $buyeritem->fromcity;
		 		$lowestrow[] = $buyeritem->tocity;
		 		$lowestrow[] = $buyeritem->volume;
		 		$lowestrow[] = $buyeritem->number_packages;
		 		
		 		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_item_id', $buyeritem->id);
		 		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		 		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id','bqsqp.rate_per_cft');
		 		$getBuyerQuoteSellersQuotesPricesQuery->orderBy('bqsqp.rate_per_cft', 'asc');
		 		$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
		 			
		 		for($i=0;$i<=2;$i++){
		 			if(isset($arrayBuyerQuoteSellersQuotesPrices[$i])){
		 				$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->rate_per_cft;
		 				
		 			}else{
		 				$lowestrow[] = "-";
		 				
		 			}
		 		}
		 		
		 			
		 		$lowestrowsfull[] = $lowestrow;
		 		Session::put('lowestquotesrelocation', $lowestrowsfull);
		 		
		 			
		 	}
		   

		    
		 	$getBuyerQuoteSellersQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 	$getBuyerQuoteSellersQuery->leftJoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
		 	$getBuyerQuoteSellersQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		 	$getBuyerQuoteSellersQuery->groupBy('bqsqp.seller_id');
		 	$getBuyerQuoteSellersQuery->where('bqsqp.lkp_service_id', $serviceId);
		 	$getBuyerQuoteSellersQuery->select('bqsqp.seller_id','u.username');
		 	$arrayBuyerQuoteSellers = $getBuyerQuoteSellersQuery->get();
		 		
		 		
		 	//$i=0;
		 	$lowestquoterows = Session::get ( 'lowestquotesrelocation' );
		 	foreach($arrayBuyerQuoteSellers as $seller) {
		 
		 
		 
		 		$sellerOne = array(
		 				array(''),
		 				array($seller->username),
		 				array('From','To','Volume','Number of Packages','Rate per CFT','Ranking'));
		 			
		 
		 		$getBuyerQuoteSellersPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
		 		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.seller_id', $seller->seller_id);
		 		$getBuyerQuoteSellersPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		 		$getBuyerQuoteSellersPricesQuery->select('bqsqp.id','bqsqp.rate_per_cft','tbqi.volume','tbqi.number_packages','cf.city_name as fromcity','ct.city_name as tocity');
		 		$arrayBuyerQuoteSellersPrices = $getBuyerQuoteSellersPricesQuery->get();
		 		
		 		$subtotal = 0;
		 		foreach($arrayBuyerQuoteSellersPrices as $arrayBuyerQuoteSellersPrice){
		 			$singlesellerprice = array();
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->fromcity;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->tocity;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->volume;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->number_packages;
		 			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->rate_per_cft;
		 			foreach ($lowestquoterows as $lowestrow) {
		 				if($arrayBuyerQuoteSellersPrice->rate_per_cft==$lowestrow[4]){
		 					$singlesellerprice[] = 'L1';
		 				}
		 				if($arrayBuyerQuoteSellersPrice->rate_per_cft==$lowestrow[5]){
		 					$singlesellerprice[] = 'L2';
		 				}
		 				if($arrayBuyerQuoteSellersPrice->rate_per_cft==$lowestrow[6]){
		 					$singlesellerprice[] = 'L3';
		 				}
		 			}
		 			unset($singlesellerprice[6]);unset($singlesellerprice[7]);
		 			$sellerOne[] = $singlesellerprice;
		 			$subtotal+=$arrayBuyerQuoteSellersPrice->rate_per_cft;
		 		}
		 		$sellerOne[] = array("","","","Sub Total",  $subtotal );
		 			
		 		$sellerwiserates[] = $sellerOne;
		 			
		 			
		 			
		 	}
		 		
		 	Session::put('sellerquotesrelocation', $sellerwiserates);
		 
		 
		 	BuyerComponent::geneartaeQCSRELOCATION($sellerwiserates);
		 	
		    }
		    
		    if($arrayBuyerQuotes[0]->lkp_post_ratecard_type==2){
		    	
		    	$getBuyerQuoteItems=DB::table('term_buyer_quote_items as tbqi');
		    	$getBuyerQuoteItems->where('term_buyer_quote_id',$buyerQuoteId);
		    	$getBuyerQuoteItems->where('lkp_service_id',$serviceId);
		    	$getBuyerQuoteItems->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
		    	$getBuyerQuoteItems->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
		    	$getBuyerQuoteItems->select('tbqi.*','cf.city_name as fromcity','ct.city_name as tocity');
		    	$arrayBuyerQuoteItems = $getBuyerQuoteItems->get();
		    	 
		    	 
		    	foreach($arrayBuyerQuoteItems as $buyeritem) {
		    		$lowestrow = array();
		    		$lowestrow[] = $buyeritem->fromcity;
		    		$lowestrow[] = $buyeritem->tocity;
		    		$lowestrow[] = CommonComponent::getVehicleCategoryById($buyeritem->lkp_vehicle_category_id);
		    		$lowestrow[] = $buyeritem->vehicle_model;
		    		$lowestrow[] = $buyeritem->no_of_vehicles;
		    		 
		    		$getBuyerQuoteSellersQuotesPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		    		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.term_buyer_quote_item_id', $buyeritem->id);
		    		$getBuyerQuoteSellersQuotesPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		    		$getBuyerQuoteSellersQuotesPricesQuery->select('bqsqp.id','bqsqp.transport_charges','bqsqp.odcharges');
		    		$getBuyerQuoteSellersQuotesPricesQuery->select(DB::raw('(bqsqp.transport_charges+bqsqp.odcharges) AS total_charges,bqsqp.id,bqsqp.transport_charges,bqsqp.odcharges'));
		    		$getBuyerQuoteSellersQuotesPricesQuery->orderBy('total_charges');
		    		$arrayBuyerQuoteSellersQuotesPrices = $getBuyerQuoteSellersQuotesPricesQuery->get();
		    	
		    		for($i=0;$i<=2;$i++){
		    			if(isset($arrayBuyerQuoteSellersQuotesPrices[$i])){
		    				$lowestrow[] = $arrayBuyerQuoteSellersQuotesPrices[$i]->transport_charges+$arrayBuyerQuoteSellersQuotesPrices[$i]->odcharges;
		    				
		    			}else{
		    				$lowestrow[] = "-";
		    				
		    			}
		    		}
		    		 
		    	
		    		$lowestrowsfull[] = $lowestrow;
		    		Session::put('lowestquotesrelocationveh', $lowestrowsfull);
		    		
		    	
		    	}
		    	 
		    	
		    	
		    	$getBuyerQuoteSellersQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		    	$getBuyerQuoteSellersQuery->leftJoin('users as u', 'u.id', '=', 'bqsqp.seller_id');
		    	$getBuyerQuoteSellersQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		    	$getBuyerQuoteSellersQuery->groupBy('bqsqp.seller_id');
		    	$getBuyerQuoteSellersQuery->where('bqsqp.lkp_service_id', $serviceId);
		    	$getBuyerQuoteSellersQuery->select('bqsqp.seller_id','u.username');
		    	$arrayBuyerQuoteSellers = $getBuyerQuoteSellersQuery->get();
		    	 
		    	 
		    	$lowestquoterows = Session::get ( 'lowestquotesrelocationveh' );
		    	foreach($arrayBuyerQuoteSellers as $seller) {
		    			
		    			
		    			
		    		$sellerOne = array(
		    				array(''),
		    				array($seller->username),
	    				array('From','To','Vehicle Type','Vehicle Model','Number of Vehicles','Total Price','Ranking'),
		    				
		    		);
		    	
		    			
		    		$getBuyerQuoteSellersPricesQuery = DB::table('term_buyer_quote_sellers_quotes_prices as bqsqp');
		    		$getBuyerQuoteSellersPricesQuery->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'bqsqp.term_buyer_quote_item_id');
		    		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as cf', 'cf.id', '=', 'tbqi.from_location_id');
		    		$getBuyerQuoteSellersPricesQuery->leftJoin('lkp_cities as ct', 'ct.id', '=', 'tbqi.to_location_id');
		    		$getBuyerQuoteSellersPricesQuery->where('bqsqp.term_buyer_quote_id', $buyerQuoteId);
		    		$getBuyerQuoteSellersPricesQuery->where('bqsqp.seller_id', $seller->seller_id);
		    		$getBuyerQuoteSellersPricesQuery->where('bqsqp.lkp_service_id', $serviceId);
		    		$getBuyerQuoteSellersPricesQuery->select(DB::raw('(bqsqp.transport_charges+bqsqp.odcharges) AS total_charges,bqsqp.id,tbqi.lkp_vehicle_category_id,tbqi.vehicle_model,tbqi.no_of_vehicles,cf.city_name as fromcity,ct.city_name as tocity'));
		    		
		    		$arrayBuyerQuoteSellersPrices = $getBuyerQuoteSellersPricesQuery->get();
		    	
		    		$subtotal = 0;
		    		foreach($arrayBuyerQuoteSellersPrices as $arrayBuyerQuoteSellersPrice){
		    			$singlesellerprice = array();
		    			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->fromcity;
		    			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->tocity;
		    			$singlesellerprice[] = CommonComponent::getVehicleCategoryById($buyeritem->lkp_vehicle_category_id);
		    			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->vehicle_model;
		    			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->no_of_vehicles;
		    			$singlesellerprice[] = $arrayBuyerQuoteSellersPrice->total_charges;
		    			foreach ($lowestquoterows as $lowestrow) {
		    				if($arrayBuyerQuoteSellersPrice->total_charges==$lowestrow[5]){
		    					$singlesellerprice[] = 'L1';
		    				}
		    				if($arrayBuyerQuoteSellersPrice->total_charges==$lowestrow[6]){
		    					$singlesellerprice[] = 'L2';
		    				}
		    				if($arrayBuyerQuoteSellersPrice->total_charges==$lowestrow[7]){
		    					$singlesellerprice[] = 'L3';
		    				}
		    			}
		    			unset($singlesellerprice[7]);unset($singlesellerprice[8]);
		    			$sellerOne[] = $singlesellerprice;
		    			$subtotal+=$arrayBuyerQuoteSellersPrice->total_charges;
		    		}
		    		$sellerOne[] = array("","","","","Sub Total",  $subtotal );
		    	
		    		$sellerwiserates[] = $sellerOne;
		    	
		    	
		    	
		    	}
		    	 
		    	Session::put('sellerquotesrelocationveh', $sellerwiserates);
		    		
		    		
		    	BuyerComponent::geneartaeQCSRELOCATIONVEHICLE($sellerwiserates);
		    	
		    }
		}
		
		
		
        return $arrayBuyerQuoteSellersQuotesPrices;
		
	   	
	}
	
	public static function array_swap_assoc($key1, $key2, $array) {
		$newArray = array ();
		foreach ($array as $key => $value) {
			if ($key == $key1) {
				$newArray[$key2] = $array[$key2];
			} elseif ($key == $key2) {
				$newArray[$key1] = $array[$key1];
			} else {
				$newArray[$key] = $value;
			}
		}
		return $newArray;
	}

	/**
     * Buyer Term Quote Details Page
     * Retrieval of data related to buyer posts items
     */
    public static function getBuyerTermQuoteItemData($buyerQuoteItemId, $serviceId=null) {
        // query to retrieve buyer posts list and bind it to the grid
        if(empty($serviceId)) {
            $serviceId = Session::get('service_id');
        }
        
        $buyerQuoteItemData = DB::table('term_buyer_quote_items as bqi')
                ->join('term_buyer_quotes as bq', 'bq.id', '=', 'bqi.term_buyer_quote_id')
                ->where('bqi.id', $buyerQuoteItemId)
                ->where('bqi.lkp_service_id', $serviceId)
                ->select('bqi.*', 'bqi.lkp_service_id', 'bq.lkp_lead_type_id', 'bq.lkp_quote_access_id')
                ->get();          

        return $buyerQuoteItemData;
    }
    /**
     * Buyer Term Quote Details
     * Retrieval of data related to buyer posts items
     */
    public static function getTermContractDetails($quoteId,$serviceId)
	{
		
            try{
                $query_details = DB::table('term_contracts as tc');
                $query_details->leftjoin('term_buyer_quote_items as bqi', 'tc.term_buyer_quote_item_id', '=', 'bqi.id');
                $query_details->leftJoin('term_buyer_quote_sellers_quotes_prices as sp', 'sp.term_buyer_quote_item_id', '=', 'tc.term_buyer_quote_item_id');
            if($serviceId==ROAD_FTL || $serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_INTERNATIONAL){
                $query_details->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
                $query_details->leftJoin('lkp_cities as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                }
                if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC){
                $query_details->leftJoin('lkp_ptl_pincodes as lc', 'bqi.from_location_id', '=', 'lc.id');
                $query_details->leftJoin('lkp_ptl_pincodes as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                }if($serviceId==COURIER){
                	$query_details->leftjoin('term_buyer_quotes as bq', 'tc.term_buyer_quote_id', '=', 'bq.id');
                	
                	$query_details->leftjoin('lkp_ptl_pincodes as lc', 'bqi.from_location_id', '=', 'lc.id');
                	$query_details->leftjoin('lkp_ptl_pincodes as lcity', function($join)
                	{
                		$join->on('bqi.to_location_id', '=', 'lcity.id');
                		$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                			
                	});
                	$query_details->leftjoin('lkp_countries as lcity1', function($join)
                	{
                		$join->on('bqi.to_location_id', '=', 'lcity1.id');
                		$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                			
                	});
                	
                
                }if($serviceId==AIR_INTERNATIONAL){
                $query_details->leftJoin('lkp_airports as lc', 'bqi.from_location_id', '=', 'lc.id');
                $query_details->leftJoin('lkp_airports as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                }if($serviceId==OCEAN){
                $query_details->leftJoin('lkp_seaports as lc', 'bqi.from_location_id', '=', 'lc.id');
                $query_details->leftJoin('lkp_seaports as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                }
                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                	$query_details->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
                	
                }
                $query_details->leftJoin('lkp_vehicle_types as lvt', 'bqi.lkp_vehicle_type_id', '=', 'lvt.id');
                $query_details->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id');
                $query_details->where('tc.seller_id', '=', Auth::User ()->id);
                $query_details->where('tc.term_buyer_quote_id', '=', $quoteId);
                $query_details->where('tc.lkp_service_id',Session::get('service_id'));
                $query_details->groupBy ( 'bqi.id');
                if($serviceId==ROAD_FTL){
                $query_details->select('tc.contract_quantity','tc.contract_price','lkp_load_types.load_type','lvt.vehicle_type','lc.city_name as from','lcity.city_name as to','bqi.quantity','sp.initial_quote_price');
                }
                if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC){ 
                $query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.postoffice_name as from','lcity.postoffice_name as to','bqi.volume','bqi.number_packages');
                }
                if($serviceId==COURIER){
                	$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.postoffice_name as from',
                			DB::raw("(case when `bq`.`lkp_courier_delivery_type_id` = 1 then lcity.postoffice_name  when `bq`.`lkp_courier_delivery_type_id` = 2 then lcity1.country_name end) as 'to'"),
                			'bqi.volume','bqi.number_packages','bqi.term_buyer_quote_id','bqi.created_by');
                }
                if($serviceId==AIR_INTERNATIONAL){
                	
                	$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.airport_name as from','lcity.airport_name as to','bqi.volume','bqi.number_packages');
                } 
                if($serviceId==OCEAN){
                	$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.seaport_name as from','lcity.seaport_name as to','bqi.volume','bqi.number_packages');
                }
                if($serviceId==RELOCATION_DOMESTIC){
                	$query_details->select('tc.contract_quantity','tc.contract_price','tc.contract_transport_charges','tc.contract_od_charges','lc.city_name as from','lcity.city_name as to','bqi.volume','bqi.number_packages','bqi.lkp_vehicle_category_id','bqi.lkp_vehicle_category_type_id','bqi.vehicle_model','bqi.no_of_vehicles','sp.rate_per_cft');
                }
			if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                	$query_details->select('tc.contract_quantity','tc.contract_price','lc.city_name as from','bqi.lkp_gm_service_id','bqi.measurement','bqi.measurement_units');
			}if($serviceId==RELOCATION_INTERNATIONAL){
                	$query_details->select('tc.contract_quantity','tc.contract_price','lc.city_name as from','lcity.city_name as to');
				 }
               
                $details_contract  =   $query_details->get();
                
              
                return $details_contract;
		} catch ( Exception $ex ) {
		 
		return 0;
                }
	}
	
	public static function getTermFiles($serviceId,$QuoteId){
		
		$buyerQuoteFiles = DB::table('term_buyer_quote_bid_terms_files as tbqf')
		->where('tbqf.term_buyer_quote_id', $QuoteId)
		->where('tbqf.lkp_service_id', $serviceId)
		->select('tbqf.*')
		->get();
		
		return $buyerQuoteFiles;
		
	}
	
	/**
	 * Buyer edit term  Page
	 * Method to retrieve private seller name
	 * @param int $buyerQuoteId
	 * @return array
	 */
	public static function getTermPrivateSellerNames($quoteId) {
		try {
			Log::info ( 'Get private seller names: ' . Auth::id (), array (
			'c' => '2'
					));
					$getEditDarftQuery = DB::table('term_buyer_quotes as tbq');
					$getEditDarftQuery->leftjoin('term_buyer_quote_selected_sellers as tbqss', 'tbqss.term_buyer_quote_id', '=', 'tbq.id');
					$getEditDarftQuery->leftjoin('users as seller_names', 'seller_names.id', '=', 'tbqss.seller_id');                                                                           
					if (!empty($quoteId)) {
						$getEditDarftQuery->where('tbq.id', $quoteId);
					}
					$getEditDarftQuery->select ('seller_names.username','seller_names.id');
					$arrPrivateSellerDraft = $getEditDarftQuery->get ();
					return $arrPrivateSellerDraft;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}
	
	
	/**
	 * Buyer Term Quote Details
	 * Retrieval of data related to buyer posts items
	 */
	public static function getTermContractList($quoteId,$serviceId)
	{
	
		try{
			$query_details = DB::table('term_contracts as tc');
			//$query->leftJoin('term_buyer_quotes as bq', 'tc.term_buyer_quote_id', '=', 'bq.id')
			$query_details->leftjoin('term_buyer_quote_items as bqi', 'tc.term_buyer_quote_item_id', '=', 'bqi.id');
			if($serviceId==COURIER){
			$query_details->leftjoin('term_buyer_quotes as tbq', 'bqi.term_buyer_quote_id', '=', 'tbq.id');
			}
			$query_details->leftJoin('term_buyer_quote_sellers_quotes_prices as sp', 'sp.term_buyer_quote_item_id', '=', 'tc.term_buyer_quote_item_id');
			if($serviceId==ROAD_FTL || $serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_INTERNATIONAL || $serviceId==RELOCATION_GLOBAL_MOBILITY){
				$query_details->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
				$query_details->leftJoin('lkp_cities as lcity', 'bqi.to_location_id', '=', 'lcity.id');
			}
			if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC){
				$query_details->leftJoin('lkp_ptl_pincodes as lc', 'bqi.from_location_id', '=', 'lc.id');
				$query_details->leftJoin('lkp_ptl_pincodes as lcity', 'bqi.to_location_id', '=', 'lcity.id');
			}
			if($serviceId==COURIER){
				$query_details->leftJoin('lkp_ptl_pincodes as lc', 'bqi.from_location_id', '=', 'lc.id');
				
				//$query_details->leftJoin('lkp_ptl_pincodes as lcity', 'bqi.to_location_id', '=', 'lcity.id');
				$query_details->leftjoin('lkp_ptl_pincodes as lcityp', function($join)
				{
					$join->on('bqi.to_location_id', '=', 'lcityp.id');
					$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
				
				});
				$query_details->leftjoin('lkp_countries as lppt1', function($join)
				{
					$join->on('bqi.to_location_id', '=', 'lppt1.id');
					$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
				
				});
			}
			if($serviceId==AIR_INTERNATIONAL){
				$query_details->leftJoin('lkp_airports as lc', 'bqi.from_location_id', '=', 'lc.id');
				$query_details->leftJoin('lkp_airports as lcity', 'bqi.to_location_id', '=', 'lcity.id');
			}if($serviceId==OCEAN){
				$query_details->leftJoin('lkp_seaports as lc', 'bqi.from_location_id', '=', 'lc.id');
				$query_details->leftJoin('lkp_seaports as lcity', 'bqi.to_location_id', '=', 'lcity.id');
			}
			$query_details->leftJoin('lkp_vehicle_types as lvt', 'bqi.lkp_vehicle_type_id', '=', 'lvt.id');
			$query_details->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id');
			$query_details->where('tc.term_buyer_quote_id', '=', $quoteId);
			$query_details->where('tc.lkp_service_id',Session::get('service_id'));
			$query_details->groupBy ( 'bqi.id');
			if($serviceId==ROAD_FTL){
				$query_details->select('tc.contract_quantity','tc.contract_price','lkp_load_types.load_type','lvt.vehicle_type','lc.city_name as from','lcity.city_name as to','bqi.quantity','sp.initial_quote_price');
			}
			if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC){
				$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.postoffice_name as from','lcity.postoffice_name as to','bqi.volume','bqi.number_packages');
			}
			if($serviceId==COURIER){
				$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type',
						'lc.postoffice_name as from',
						DB::raw("(case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcityp.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lppt1.country_name end) as 'to'"),
						'bqi.volume','bqi.number_packages');
			}
			if($serviceId==AIR_INTERNATIONAL){
				 
				$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.airport_name as from','lcity.airport_name as to','bqi.volume','bqi.number_packages');
			}
			if($serviceId==OCEAN){
				$query_details->select('tc.contract_quantity','tc.contract_rate_per_kg','tc.contract_kg_per_cft','lkp_load_types.load_type','lvt.vehicle_type','lc.seaport_name as from','lcity.seaport_name as to','bqi.volume','bqi.number_packages');
			}
			if($serviceId==RELOCATION_DOMESTIC){
				$query_details->select('tc.contract_quantity','tc.contract_price','lc.city_name as from','lcity.city_name as to','bqi.volume','bqi.number_packages','sp.rate_per_cft');
			}
               if($serviceId==RELOCATION_INTERNATIONAL){
                  $query_details->select('tc.contract_quantity','tc.contract_price','lc.city_name as from','lcity.city_name as to');
			}
               if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                  $query_details->select('tc.contract_quantity','tc.contract_price','lc.city_name as from');
			}
			$details_contract  =   $query_details->get();
	
	
			return $details_contract;
		} catch ( Exception $ex ) {
				
			return 0;
		}
	}
	
	public static function saveIndentDetails($serviceId){
		
		try{
			
			$indentData=Session::get('indentdata');
			
			
			if($serviceId==ROAD_FTL){
			$cont_id=$indentData['valid_id'];
			$created_at = date('Y-m-d H:i:s');
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			$buyercontract = new TermContractsIndentQuantitie();
			$buyercontract->term_contract_id = $indentData['valid_id'];
			$buyercontract->indent_quantity = $indentData['current_indenet_quantity_'.$cont_id];
			$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
			$buyercontract->lkp_service_id = $serviceId;
			$buyercontract->created_by = Auth::id();
			$buyercontract->created_at = $created_at;
			$buyercontract->created_ip = $createdIp;
			$buyercontract->updated_at = $created_at;

			$buyercontract->save();

			}elseif($serviceId==RELOCATION_DOMESTIC){
				//dd($indentData);exit;
				$cont_id=$indentData['contract_id'];
				if(isset($indentData['property_type_'.$cont_id]) && $indentData['property_type_'.$cont_id]!=""){
				$load_Types = array('1'=>'Full Load','2'=>'Part Load');
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyercontract = new TermContractsIndentQuantitie();
				$buyercontract->term_contract_id = $indentData['contract_id'];
				$buyercontract->volume = $indentData['total_hidden_volume_'.$cont_id];
				$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
				$buyercontract->lkp_property_type_id = $indentData['property_type_'.$cont_id];
				$buyercontract->lkp_service_id = $serviceId;
				$buyercontract->domestic_load = $load_Types[$indentData['load_type_'.$cont_id]];
				$buyercontract->created_by = Auth::id();
				$buyercontract->created_at = $created_at;
				$buyercontract->created_ip = $createdIp;
				$buyercontract->updated_at = $created_at;
				$buyercontract->lkp_ratecard_type =1;
				$buyercontract->save();
				}else{

					$created_at = date('Y-m-d H:i:s');
					$createdIp = $_SERVER ['REMOTE_ADDR'];
					$buyercontract = new TermContractsIndentQuantitie();
					$buyercontract->term_contract_id = $indentData['contract_id'];
					$buyercontract->indent_quantity = $indentData['term_numberofveh_'.$cont_id];
					$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
					$buyercontract->lkp_service_id = $serviceId;
					$buyercontract->created_by = Auth::id();
					$buyercontract->created_at = $created_at;
					$buyercontract->created_ip = $createdIp;
					$buyercontract->updated_at = $created_at;
					$buyercontract->lkp_ratecard_type =2;
					
					$buyercontract->save();
				}
				if(Session::has('masterBedRoom')){
				
					$particulars=CommonComponent::getParticularsByRoomId(1);
					$created_at = date ( 'Y-m-d H:i:s' );
					$createdIp = $_SERVER ['REMOTE_ADDR'];
				
				
					$masterbedroom=array();
					$masterbedroom=Session::get('masterBedRoom');
				
					foreach($particulars as $particular){
						
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$masterbedroom['number_items_'.$particular->id];
						$particularcrating=$masterbedroom['crating_'.$particular->id];
						//$particulardata=Session::get($particulardata);
						//$particularcrating=Session::get($particularcrating);
						//echo $particulardata;
						if($particulardata!=""){
								
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$masterbedroom1['number_items_'.$particular->id];
						$particularcrating=$masterbedroom1['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$masterbedroom2['number_items_'.$particular->id];
						$particularcrating=$masterbedroom2['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$masterbedroom3['number_items_'.$particular->id];
						$particularcrating=$masterbedroom3['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$lobby['number_items_'.$particular->id];
						$particularcrating=$lobby['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$kitchen['number_items_'.$particular->id];
						$particularcrating=$kitchen['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$bathroom['number_items_'.$particular->id];
						$particularcrating=$bathroom['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
						$buyerpost_inventory = new RelocationtermBuyerPostIndentParticular();
						$particulardata=$living['number_items_'.$particular->id];
						$particularcrating=$living['crating_'.$particular->id];
						if($particulardata!=""){
							$buyerpost_inventory->lkp_service_id=RELOCATION_DOMESTIC;
							$buyerpost_inventory->term_contract_id=$indentData['contract_id'];
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
				if(isset($_POST['elevator_origin_'.$indentData['contract_id']])){
				$buyerpost_inventory->origin_elevator = $_POST['elevator_origin_'.$indentData['contract_id']];
				}
				if(isset($_POST['elevator_destination_'.$indentData['contract_id']])){
				$buyerpost_inventory->destination_elevator = $_POST['elevator_destination_'.$indentData['contract_id']];
				}
				if(isset($_POST['origin_handy_serivce_'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_handyman = 1;
				}
				if(isset($_POST['destination_handy_serivce_'.$indentData['contract_id']])){
					$buyerpost_inventory->destination_handyman = 1;
				}
				if(isset($_POST['origin_storage_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_storage = 1;
				}
				if(isset($_POST['destination_storage_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->destination_storage = 1;
				}
				if(isset($_POST['insurance_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_insurance = 1;
				}
				if(isset($_POST['escort_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_escort = 1;
				}
				if(isset($_POST['mobilty_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_mobility = 1;
				}
				if(isset($_POST['property_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_property = 1;
				}
				if(isset($_POST['setting_serivce'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_service = 1;
				}
				if(isset($_POST['insurance_domestic'.$indentData['contract_id']])){
					$buyerpost_inventory->origin_domestic = 1;
				}
				
				
				
				
			}
			elseif($serviceId==RELOCATION_INTERNATIONAL){
				
				$cont_id=$indentData['contract_id'];
				//dd($indentData);exit;
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyercontract = new TermContractsIndentQuantitie();
				$buyercontract->term_contract_id = $cont_id;
				//$buyercontract->indent_quantity = $indentData['current_indenet_quantity_'.$cont_id];
				$buyercontract->lkp_service_id = $serviceId;
				$buyercontract->created_by = Auth::id();
				$buyercontract->created_at = $created_at;
				$buyercontract->created_ip = $createdIp;
				$buyercontract->updated_at = $created_at;
				if(isset($indentData['cartons_1'])){
				$buyercontract->cartons_one = $indentData['cartons_1'];
				$buyercontract->cartons_two = $indentData['cartons_2'];
				$buyercontract->cartons_three = $indentData['cartons_3'];
				$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
				$buyercontract->avergaekgmove = $indentData['total_hidden_kgs_'.$cont_id];
			    }else{
			    $buyercontract->volume = $indentData['total_hidden_kgs_'.$cont_id];
			    $buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
			    $buyercontract->lkp_property_type_id = $indentData['property_type_'.$cont_id];
			    }
			    
				$buyercontract->save();
				
			}
			elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
				$cont_id=$indentData['contract_id'];
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyercontract = new TermContractsIndentQuantitie();
				$buyercontract->term_contract_id = $cont_id;
				$buyercontract->indent_quantity = $indentData['total_hidden_days_'.$cont_id];
				$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
				$buyercontract->lkp_service_id = $serviceId;
				$buyercontract->created_by = Auth::id();
				$buyercontract->created_at = $created_at;
				$buyercontract->created_ip = $createdIp;
				$buyercontract->updated_at = $created_at;
			
				$buyercontract->save();
			
			}
			elseif($serviceId==COURIER){
				//dd($indentData);exit;
				$cont_id=$indentData['contract_id'];
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyercontract = new TermContractsIndentQuantitie();
				$buyercontract->term_contract_id = $cont_id;
				//$buyercontract->indent_quantity = $indentData['current_indenet_quantity_'.$cont_id];
				$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
				$buyercontract->length = $indentData['courier_term_length_'.$cont_id];
				$buyercontract->breadth = $indentData['courier_term_width_'.$cont_id];
				$buyercontract->height = $indentData['courier_term_height_'.$cont_id];
				$buyercontract->lkp_ict_weight_type_id = $indentData['courier_CheckWeightUnit_'.$cont_id];
				$buyercontract->lkp_ptl_length_uom_id = $indentData['term_weighttype_'.$cont_id];
				$buyercontract->noofpackages = $indentData['term_noofpackages_'.$cont_id];
				$buyercontract->unitweight = $indentData['ptlUnitsWeight_'.$cont_id];
				$buyercontract->volumetricweight = '';
				$buyercontract->package_value = $indentData['package_value'.$cont_id];
				$buyercontract->lkp_service_id = $serviceId;
				$buyercontract->created_by = Auth::id();
				$buyercontract->created_at = $created_at;
				$buyercontract->created_ip = $createdIp;
				$buyercontract->updated_at = $created_at;
			
				$buyercontract->save();
			}
			else{
				$cont_id=$indentData['contract_id'];
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$buyercontract = new TermContractsIndentQuantitie();
				$buyercontract->term_contract_id = $cont_id;
				//$buyercontract->indent_quantity = $indentData['current_indenet_quantity_'.$cont_id];
				$buyercontract->contract_price = $indentData['total_hidden_amnt_'.$cont_id];
				$buyercontract->length = $indentData['term_length_'.$cont_id];
				$buyercontract->breadth = $indentData['term_width_'.$cont_id];
				$buyercontract->height = $indentData['term_height_'.$cont_id];
				$buyercontract->lkp_ict_weight_type_id = $indentData['ptlCheckUnitWeight_'.$cont_id];
				$buyercontract->lkp_ptl_length_uom_id = $indentData['term_weighttype_'.$cont_id];
				$buyercontract->noofpackages = $indentData['term_noofpackages_'.$cont_id];
				$buyercontract->unitweight = $indentData['ptlUnitsWeight_'.$cont_id];
				$buyercontract->volumetricweight = $indentData['hiddenvolumetricWeight_'.$cont_id];
                $buyercontract->volume = $indentData['volume_hidden_ltl_'.$cont_id];
				$buyercontract->lkp_service_id = $serviceId;
				$buyercontract->created_by = Auth::id();
				$buyercontract->created_at = $created_at;
				$buyercontract->created_ip = $createdIp;
				$buyercontract->updated_at = $created_at;

				$buyercontract->save();
			}

			
			
			
		}catch ( Exception $ex ) {
				
			return 0;
		}
			
		
	}
	
	public static function  getTermBuyerContractDetails($quoteId,$sellerId,$serviceId){
		
		$query_contract = DB::table('term_contracts as tc');
		$query_contract->where('tc.term_buyer_quote_id', '=', $quoteId);
		$query_contract->where('tc.seller_id',$sellerId);
		$query_contract->where('tc.lkp_service_id',$serviceId);
		$query_contract->select('tc.id');
		
		$details_contract_count  =   $query_contract->get();
		
		return count($details_contract_count);
		
	}
	
	public static function RelocationTermBuyerCreateQuote($serviceId, $allRequestdata, $postType) {
		
		
		try {
               // echo "<pre>"; print_r($allRequestdata); die;			
			if($serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_INTERNATIONAL || $serviceId==RELOCATION_GLOBAL_MOBILITY){
				
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				
				$created_year = date('Y');
				$serviceId = Session::get('service_id');
				$ordid  =   CommonComponent::getTermPostID();
				
				if($serviceId==RELOCATION_DOMESTIC){
				$trans_randid = 'RELOCATION_DOMESTIC_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
				$servicename = 'RELOCATION_DOMESTIC TERM';
				}
				if($serviceId==RELOCATION_INTERNATIONAL){
                                        if(isset($allRequestdata['post_type_term']) && $allRequestdata['post_type_term']==1) {
                                            $trans_randid = 'RELOCATION_INTERNATIONALAIR_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
                                        } else {
                                            $trans_randid = 'RELOCATION_INTERNATIONALOCEAN_TERM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
                                        }
					
					$servicename = 'RELOCATION_INTERNATIONAL TERM';
				}

				if($serviceId==RELOCATION_GLOBAL_MOBILITY){
					$trans_randid = 'RELOCATIONGM/' .$created_year .'/'. str_pad($ordid, 6, "0", STR_PAD_LEFT);
					$servicename = 'RELOCATION_GLOBAL_MOBILITY TERM';
				}
				
				$fromcities = array();
				$fromcities[] = $allRequestdata['from_location'];				
				
				if (isset($_REQUEST['quoteaccess_id']) && !empty($_REQUEST['quoteaccess_id']) ) {
					$is_private = $_REQUEST['quoteaccess_id'];
					if (isset($is_private) == '2' && !empty($is_private)) {
						if ($allRequestdata['term_seller_list'] != "") {
							$seller_list = explode(",", $allRequestdata['term_seller_list']);
							$seller_list_count = count($seller_list);
						}
					} else {
						$is_private = 1;
					}
				}				
				$base_dir = 'uploads/buyer/'.Auth::id().'/Terms/' ;
				if (!is_dir ( $base_dir )) {
					mkdir ( $base_dir, 0777, true );
				}				
				if (!empty($allRequestdata['confirm_but']) && isset($allRequestdata['confirm_but'])) {
					$postStatus= OPEN;
				} else {
					$postStatus= SAVEDASDRAFT;
				}
				
				/*
				 * Term Quote insert main quotes table data
				 */
				$buyerQuote = new TermBuyerQuote();
				$buyerQuote->lkp_service_id = $serviceId;
				if($serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_GLOBAL_MOBILITY){
				$buyerQuote->lkp_lead_type_id = $postType;
				}
				if($serviceId==RELOCATION_INTERNATIONAL){
				$buyerQuote->lkp_lead_type_id = $allRequestdata['post_type_term'];
				}
				$buyerQuote->lkp_quote_access_id = $is_private;
				$buyerQuote->transaction_id = $trans_randid;
				if($serviceId==RELOCATION_GLOBAL_MOBILITY){
					$buyerQuote->from_date = CommonComponent::convertDateForDatabase($allRequestdata['term_dispatch_date']);
					$buyerQuote->to_date = CommonComponent::convertDateForDatabase($allRequestdata['term_delivery_date']);
				}else{
					$buyerQuote->from_date = CommonComponent::convertDateForDatabase($allRequestdata['dispatch_date'][0]);
					$buyerQuote->to_date = CommonComponent::convertDateForDatabase($allRequestdata['delivery_date'][0]);
				}
				$buyerQuote->buyer_notes = $allRequestdata['buyer_notes'];					
				$buyerQuote->lkp_post_status_id = $postStatus;
				if($serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_GLOBAL_MOBILITY){
				$buyerQuote->lkp_post_ratecard_type = $allRequestdata['term_post_rate_card_type'];
				}
				if($serviceId==RELOCATION_INTERNATIONAL){
				if(isset($_POST['source_storage'])){					
				$buyerQuote->source_storage = 1;
				}
				if(isset($_POST['destination_storage'])){
				$buyerQuote->destination_storage = 1;
				}
				if($allRequestdata['post_type_term']==2){
				if(isset($_POST['source_handyman'])){					
				$buyerQuote->source_handyman = 1;
				}
				if(isset($_POST['destination_handyman'])){
				$buyerQuote->destination_handyman = 1;
				}
				}
				
				}
				
				$buyerQuote->buyer_id = Auth::id();
				$buyerQuote->created_by = Auth::id();
				$buyerQuote->created_at = $created_at;
				$buyerQuote->created_ip = $createdIp;
				if ($buyerQuote->save()) {
					$transactionID = $buyerQuote->transaction_id;
					//Save bid Data dates
					$buyerBidDate = new TermBuyerBidDate();
					$buyerBidDate->term_buyer_quote_id = $buyerQuote->id;
					$buyerBidDate->bid_end_date = CommonComponent::convertDateForDatabase($allRequestdata['last_bid_date']);
					$buyerBidDate->bid_end_time = $allRequestdata['bid_close_time'];
					$buyerBidDate->is_active = 1;
					$buyerBidDate->lkp_service_id = $serviceId;
					$buyerBidDate->created_by = Auth::id();
					$buyerBidDate->created_at = $created_at;
					$buyerBidDate->created_ip = $createdIp;
					$buyerBidDate->save();
					
					if(count($_FILES)>0){
						//Files save data in uploads docs
						$target_dir = 'uploads/buyer/'.Auth::id().'/Terms/'.$buyerQuote->id."/" ;
						if (!is_dir ( $target_dir )) {
							mkdir ( $target_dir, 0777, true );
						}
						$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_defualt"]["name"]);
						$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
						$_FILES["terms_condtion_types_term_defualt"]["size"];
						$_FILES["terms_condtion_types_term_defualt"]["name"];
						move_uploaded_file($_FILES["terms_condtion_types_term_defualt"]["tmp_name"], $target_file);
							
						$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
						$buyerBidTermFiles->term_buyer_quote_id = $buyerQuote->id;
						$buyerBidTermFiles->file_name = $_FILES["terms_condtion_types_term_defualt"]["name"];
						$buyerBidTermFiles->file_type = $imageFileType;
						$buyerBidTermFiles->file_size = $_FILES["terms_condtion_types_term_defualt"]["size"];
						$buyerBidTermFiles->file_path = $target_file;
						$buyerBidTermFiles->lkp_service_id = $serviceId;
						$buyerBidTermFiles->created_by = Auth::id();
						$buyerBidTermFiles->created_at = $created_at;
						$buyerBidTermFiles->created_ip = $createdIp;
						$buyerBidTermFiles->save();
					
					}
					//Docuements uploads
					$j =1;
					if(count($_FILES)>0){
						for($j=1;$j<=$allRequestdata['term_next_terms_count_search'];$j++){
							if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] == '') {
								$j++;
							}
							if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] != '') {
								$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_$j"]["name"]);
								$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
								move_uploaded_file($_FILES["terms_condtion_types_term_$j"]["tmp_name"], $target_file);
								$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
								$buyerBidTermFiles->term_buyer_quote_id = $buyerQuote->id;
								$buyerBidTermFiles->file_name = $_FILES["terms_condtion_types_term_$j"]["name"];
								$buyerBidTermFiles->file_type = $imageFileType;
								$buyerBidTermFiles->file_size = $_FILES["terms_condtion_types_term_$j"]["size"];
								$buyerBidTermFiles->file_path = $target_file;
								$buyerBidTermFiles->lkp_service_id = $serviceId;
								$buyerBidTermFiles->created_by = Auth::id();
								$buyerBidTermFiles->created_at = $created_at;
								$buyerBidTermFiles->created_ip = $createdIp;
								$buyerBidTermFiles->save();
							}
						}
					}
					if (!empty($allRequestdata['from_location'])) {
						if($serviceId==RELOCATION_GLOBAL_MOBILITY){
							$from_location = $allRequestdata['from_location_id'];
							$service_count = $allRequestdata['term_service_slab_hidden_value'];
							for($s=0;$s<$service_count;$s++){
								$Quote_Lineitems = new TermBuyerQuoteItem();
								$Quote_Lineitems->term_buyer_quote_id 	 = $buyerQuote->id;
								$Quote_Lineitems->from_location_id = $from_location;

								$Quote_Lineitems->lkp_gm_service_id = $allRequestdata['term_service_ids'][$s];
								$Quote_Lineitems->measurement = $allRequestdata['term_measurements'][$s];
								$Quote_Lineitems->measurement_units = $allRequestdata['term_measurement_units'][$s];

								$Quote_Lineitems->lkp_service_id = $serviceId;
								$Quote_Lineitems->created_by = Auth::id();
								$Quote_Lineitems->created_at = $created_at;
								$Quote_Lineitems->created_ip = $createdIp;
								$Quote_Lineitems->save();
							}
						}else{
							$multi_data_count = count($allRequestdata['from_location']);
							for ($i = 0; $i < $multi_data_count; $i++) {
								/******Multiple insert in quote items******** */
								$Quote_Lineitems = new TermBuyerQuoteItem();
								$Quote_Lineitems->term_buyer_quote_id 	 = $buyerQuote->id;
								$Quote_Lineitems->from_location_id = $allRequestdata['from_location'][$i];
								$Quote_Lineitems->to_location_id = $allRequestdata['to_location'][$i];
								if($serviceId==RELOCATION_DOMESTIC){
								if($allRequestdata['term_post_rate_card_type']==1){
								$Quote_Lineitems->volume = $allRequestdata['relocation_term_volume'][$i];
								$Quote_Lineitems->number_packages = $allRequestdata['relocation_term_noofshipments'][$i];
								}else{
								$Quote_Lineitems->lkp_vehicle_category_id = $allRequestdata['relocation_term_vehicle_cat'][$i];
								
								if($allRequestdata['relocation_term_vehicle_cat'][$i]==1){								
								$Quote_Lineitems->lkp_vehicle_category_type_id = $allRequestdata['relocation_term_vehicle_cat_type'][$i];
								}
								
								$Quote_Lineitems-> 	no_of_vehicles = $allRequestdata['relocation_term_nooftrips'][$i];
								$Quote_Lineitems->vehicle_model = $allRequestdata['relocation_term_vehicle_model'][$i];
									
								}
								}
								if($serviceId==RELOCATION_INTERNATIONAL){
								$Quote_Lineitems->number_loads = $allRequestdata['relocation_term_noofmoves'][$i];
								$Quote_Lineitems->avg_kg_per_move = $allRequestdata['relocation_term_kgmove'][$i];
								}
								$Quote_Lineitems->lkp_service_id = $serviceId;
								$Quote_Lineitems->created_by = Auth::id();
								$Quote_Lineitems->created_at = $created_at;
								$Quote_Lineitems->created_ip = $createdIp;
								$Quote_Lineitems->save();
								
							}
						}	
						if ($is_private == '2') {
							if ($allRequestdata['term_seller_list'] != "") {
								if ($seller_list_count != 0) {
									//echo $seller_list_count; exit;
									for ($i = 0; $i < $seller_list_count; $i ++) {
										$Quote_seller_list = new TermBuyerQuoteSelectedSeller();
										$Quote_seller_list->term_buyer_quote_id = $buyerQuote->id;
										$Quote_seller_list->seller_id = $seller_list[$i];
										$Quote_seller_list->lkp_service_id = $serviceId;
										$Quote_seller_list->created_by = Auth::id();
										$Quote_seller_list->created_at = $created_at;
										$Quote_seller_list->created_ip = $createdIp;
										$Quote_seller_list->save();
										//below code  for sent mails to selelcted sellers in private post
										$buyers_selected_sellers_email = DB::table('users')->where('id', $seller_list[$i])->get();
										$buyers_selected_sellers_email[0]->randnumber = $trans_randid;
										$buyers_selected_sellers_email[0]->buyername = Auth::User()->username;
										CommonComponent::send_email(BUYER_CREATED_POST_FOR_SELLERS, $buyers_selected_sellers_email);
										//Maintaining a log of data for buyer new seller data multiple  creation
										CommonComponent::auditLog($Quote_seller_list->id, 'buyer_quote_selected_sellers');
										
										
					
										//*******Send Sms to the private Sellers***********************//
										if($postStatus == OPEN){
										$msg_params = array(
												'randnumber' => $trans_randid,
												'buyername' => Auth::User()->username,
												'servicename' => "RELOCATION DOMESTIC TERM"
										);
										$getMobileNumber  =   CommonComponent::getMobleNumber($seller_list[$i]);
										if($getMobileNumber)
											CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_TERM_SMS,$msg_params);
										//*******Send Sms to the private Sellers***********************//
										}
									}
								}
							}
						}else{
							
							if($postStatus == OPEN){
								switch ($serviceId) {
									case RELOCATION_DOMESTIC :
										$servicename = 'RELOCATION DOMESTIC TERM';
										break;
									case RELOCATION_INTERNATIONAL :
										if($allRequestdata['post_type_term']==1){
										$servicename = 'RELOCATION AIR INTERNATIONAL TERM';
										}else{
										$servicename = 'RELOCATION OCEAN INTERNATIONAL TERM';
										}
										break;
									case RELOCATION_GLOBAL_MOBILITY :
										$servicename = 'RELOCATION GLOBAL MOBILITY TERM';
										break;
									default :
										$servicename = 'LTL TERM';
										break;
							
								}
								//*******Send Sms to the private Sellers***********************//
								$msg_params = array(
										'randnumber' => $trans_randid,
										'buyername' => Auth::User()->username,
										'servicename' => $servicename
								);
								//echo "<pre>";print_r($fromcities);exit;
									$getSellerIds  =   CommonComponent::getTermSellerList($fromcities);
								//echo "<pre>";print_r($getSellerIds);exit;
								for($i=0;$i<count($getSellerIds);$i++){
									$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
									if($getMobileNumber)
										CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
								}
								//*******Send Sms to the private Sellers***********************//
							
							}
							
						}
					}
					//making zip file start
					$documents = DB::table('term_buyer_quote_bid_terms_files')
					->where('term_buyer_quote_id', $buyerQuote->id)
					->select('file_name', 'file_path')
					->get();					
					
					if($documents[0]->file_name!=''){
						$files = array();
						foreach($documents as $document){
							$files[] = $document->file_path;
						}
						$zippath = (isset($files['0'])) ? explode("/",$files['0']) : array();
						array_pop($zippath);
						$zippath = implode("/",$zippath);
						if(file_exists($zippath)) {
							$zipname = $zippath . '/biddocuments.zip';
							$zip = new ZipArchive;
							$zip->open($zipname, ZipArchive::CREATE);
							foreach ($files as $file) {
								$zip->addFile($file);
							}
						}
					}
					//making zip file end
					return $transactionID;
				}
			}
			
		} catch ( Exception $ex ) {				
			return 0;
		}	
	}
	
	
	public static function getRateCardType($buyer_quote_id){
		
		$rateCard = DB::table('term_buyer_quotes')
		->where('id', $buyer_quote_id)
		->select('lkp_post_ratecard_type')
		->get();
		
		return $rateCard[0]->lkp_post_ratecard_type;
			
	}
	
	
	public static function getCourierTermFreightDetailsCal($input) {
		try {
			
			Log::info('Set buyer counter offer for ptl: ' . Auth::id(), array('c' => '2'));
			$tot=0;
			$serviceId = Session::get('service_id');
			$totalFreight = 0;
			
			$ptlLength=$input['length'];
			$ptlWidth=$input['width'];
			$ptlHeight= $input['height'];
			$ptlweightType=$input['lengthUnit'];
			$weightunit=$input['WeightUnit'];
			$unitweight=$input['UnitWeight'];
			$confactor=$input['conversionfactor'];
			$transit_days=$input['transit_days'];
			$fuel_charges=$input['fuel_charges'];
			$cod_charges=$input['cod_charges'];
			$freight_charges=$input['freight_charges'];
			$arc_charges=$input['arc_charges'];
			$max_value=$input['max_value'];
			$noOfPackages=$input['noOfPackage'];
			$packageValue=$input['packageValue'];
			$courier_type=$input['courier_type'];
			$incrementalweight=$input['incremental_weight'];
			$remaining_incremental_weight=$input['remaining_incremental_weight'];
			
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
			
			if($confactor>0)
            	$vweight= ($displayVolumeWeight)/$confactor;
            else 
            	$vweight = $displayVolumeWeight;
            
			if($weightunit==3)
			{
				$unitweight = $unitweight*1000;
			}elseif($weightunit==2){
				$unitweight = $unitweight*0.001;
			}else{
				$unitweight = $unitweight;
			}
            
			if($courier_type == 2){
				if($vweight>=$unitweight){
					$displayChargableweighttotal=$vweight;
				}else{
					$displayChargableweighttotal=$unitweight;
				}
             }else{
              	$displayChargableweighttotal =  $unitweight;
             }
			
			
			
			$seller_post_slab_values  = DB::table('term_buyer_quote_sellers_quotes_price_slabs')
			->where('term_buyer_quote_sellers_quotes_price_slabs.term_buyer_quote_id',$input['buyer_quote_id'])
			->where('term_buyer_quote_sellers_quotes_price_slabs.term_buyer_quote_sellers_quotes_price_id',$input['term_buyer_quote_sellers_quotes_price_id'])
			->where('term_buyer_quote_sellers_quotes_price_slabs.seller_id',$input['sellerid'])
			->select('term_buyer_quote_sellers_quotes_price_slabs.*')
			->get();
			
			
			$maxVal =0;
			$total_slab_amount = 0;
			for($m=0;$m<count($seller_post_slab_values);$m++){
				
				$minVal = $seller_post_slab_values[$m]->slab_min_rate;
				$maxVal = $seller_post_slab_values[$m]->slab_max_rate;
				$slabval = $seller_post_slab_values[$m]->slab_rate;
				$total_slab_amount = $total_slab_amount + $seller_post_slab_values[$m]->slab_rate;
				if($displayChargableweighttotal >= $minVal && $displayChargableweighttotal <= $maxVal){
					break;
				}
			
			}
		
			if($displayChargableweighttotal > $maxVal){
				$balance_weight = $displayChargableweighttotal - $maxVal;
				
				if($incrementalweight == 1){
					$weight_inc = $balance_weight/$remaining_incremental_weight;
					$additonal_rate = $weight_inc * $input['rate_per_increment'];
					$total_slab_amount = $total_slab_amount + $additonal_rate;
					
				}else{
					$weight_inc = $balance_weight/$maxVal;
					$additonal_rate = $weight_inc * $slabval;
					$total_slab_amount = $total_slab_amount + $additonal_rate;
					
				}
			}
			
			$totalChargableAmount = ($total_slab_amount*$noOfPackages);
			$fuelsurchargeCalVal = ($fuel_charges * $totalChargableAmount)/100;
			$codchargeVal = ($cod_charges * $noOfPackages * $packageValue ) /100;
			$arcchargeVal = ($arc_charges * $noOfPackages * $packageValue ) /100;
			
			$tot    +=$totalChargableAmount + $fuelsurchargeCalVal + $codchargeVal + $arcchargeVal+$freight_charges;
			$tot=round($tot,2);
			$totalChargableAmount=round($totalChargableAmount,2);
			//Save data into txnprojectinviteerequests
			return [
			'totalAmount' => $tot,
			'freight'=>$totalChargableAmount];
		} catch (Exception $e) {
	
		}
	}
	
	public static function getInternationalType($buyer_quote_id){
	
		$rateCard = DB::table('term_buyer_quotes')
		->where('id', $buyer_quote_id)
		->select('lkp_lead_type_id')
		->get();
	
		return $rateCard[0]->lkp_lead_type_id;
			
	}
	
	
	
}


