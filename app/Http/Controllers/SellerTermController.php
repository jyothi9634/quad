<?php
namespace App\Http\Controllers;

use App\Components\CommonComponent;
use App\Components\Term\TermSellerComponent;
use App\Components\Term\TermBuyerComponent;
use App\Models\TermBuyerQuoteSellersQuotesPrice;
use App\Models\TermBuyerQuoteSellersQuotesPriceSlab;
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
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;

/* Term controller switch cases.
 * 
 */
class SellerTermController extends Controller {
	

	public function sellerTermPosts() {
		
	try{
			$enquiry_type = '';			
			
			//Retrieval of post statuses
			$status = CommonComponent::getPostStatuses();

			//Retrieval of seller services
			$services = CommonComponent::getServices();

			//Retrieval of lead types
			$enquiry_types = CommonComponent::getEnquiryTypes();
			
			//Search Form logic
			$serviceId = '';
			if (!empty($_POST) ){
				if(isset($_POST['status_id']) && $_POST['status_id'] != ''){	
					$post_status = $_POST['status_id'];
					Session::put('status_search', $_POST['status_id']);
				}else{
                    $post_status='';
                }
				if(isset($_POST['service_id']) && $_POST['service_id'] != ''){
					$serviceId= $_POST['service_id'];
					//Session::put('service_id', $_POST['service_id']);
				}
				if(isset($_POST['lkp_enquiry_type_id']) && $_POST['lkp_enquiry_type_id'] != ''){
					$enquiry_type = $_POST['lkp_enquiry_type_id'];
					Session::put('enquiry_type', $_POST['lkp_enquiry_type_id']);
				}
			}else if(isset($_GET['page'])){
					$post_status = Session::get('status_search');
					$serviceId = Session::get('service_id');		
					$enquiry_type = Session::get('lkp_enquiry_type_id');
					
			}else{
				$enquiry_type = '';
				$post_status = '';
				Session::put('status_search','');
				Session::put('enquiry_type','');
			}

			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			
			//Loading respective service data grid
			switch($serviceId){
				case ROAD_FTL : //CommonComponent::activityLog("FTL_SELLER_LISTED_POSTS",
											// FTL_SELLER_LISTED_POSTS,0,
											 //HTTP_REFERRER,CURRENT_URL);
											 //echo "ggg";exit;
				                      $result = TermSellerComponent::getTermSellerPostlists($serviceId,$post_status,$enquiry_type);
									  $grid = $result ['grid'];
									  $filter = $result ['filter'];
									  //rendering the view with the data grid
									  return view ( 'term.sellers.term_seller_posts_list', [
											'grid' => $grid,
											'filter' => $filter
											 ], array (
											 'services' => $services,
											 'enquiry_types' => $enquiry_types,
											 'enquiry_type' => $enquiry_type,
											 'service_id' => $serviceId,
											 'post_status'=>$post_status,
											 'status'=>$status));
							          break;
				case ROAD_PTL : 
                              
							          break;
                                
				case ROAD_INTRACITY :
					
				                      
				                      break;
				case ROAD_TRUCK_HAUL: 
							          break;
				default             : 
							          break;		   			  
			}
					
		} catch (Exception $e) {
		
		}
		
	}
	public function termIntialQuoteSeller() {
		try{

			$buyer_line_items_term = explode(",", $_POST['buyer_line_item_id']);
			array_shift($buyer_line_items_term);			
			$buyer_id_for_item = CommonComponent::getBuyerId($_POST['buyer_item_id']);
			$created_at = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			if(isset($_POST['submit']) && $_POST['submit'] != ''){
					$submit_button = 1;
					$save_button = 0;
			}else{
					$save_button = 1;
					$submit_button = 0;
			}

				$arraykeys = array_keys($_POST);
				
				foreach($arraykeys as $arraykey){
					if (0 === strpos($arraykey, 'intialquote_') || 0 === strpos($arraykey, 'initial_rate_per_kg') || 0 === strpos($arraykey, 'rateper_kg_') 
							|| 0 === strpos($arraykey, 'transport_charges_') || 0 === strpos($arraykey, 'frieghthundred_charges_') || 0 === strpos($arraykey, 'frieghtlcl_charges_')) {

						$buyer_line_items_term_ids = substr($arraykey,12);
						$itemchunks = explode("_",$arraykey);
						$itemId = end($itemchunks);
						
						$termBuyerQuoteSellersQuotes  =  new TermBuyerQuoteSellersQuotesPrice();

						
						$termLineItemsExits = DB::table('term_buyer_quote_sellers_quotes_prices')
						->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_item_id','=',$itemId)
						->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::User ()->id)
						->select('*')
						->get();
						//echo $_POST['rateper_kg_'.$buyer_line_items_term_ids];
						//echo count($termLineItemsExits);
						
						if(count($termLineItemsExits) == 0){
							if((isset($_POST['intialquote_'.$buyer_line_items_term_ids]) && $_POST['intialquote_'.$buyer_line_items_term_ids]!="") || (isset($_POST['initial_rate_per_kg_'.$itemId]) && $_POST['initial_rate_per_kg_'.$itemId]!="")
									|| (isset($_POST['rateper_kg_'.$itemId]) && $_POST['rateper_kg_'.$itemId]!="") || (isset($_POST['transport_charges_'.$itemId]) && $_POST['od_charges_'.$itemId]!="")
									|| (isset($_POST['frieghthundred_charges_'.$itemId]) && $_POST['frieghthundred_charges_'.$itemId]!="") || (isset($_POST['frieghtlcl_charges_'.$itemId]) && $_POST['frieghtlcl_charges_'.$itemId]!="")){
								
								$termBuyerQuoteSellersQuotes->buyer_id = $buyer_id_for_item[0];
								$termBuyerQuoteSellersQuotes->term_buyer_quote_item_id = $itemId;//$buyer_line_items_term_ids;
								$termBuyerQuoteSellersQuotes->term_buyer_quote_id = $_POST['buyer_item_id'];
								$termBuyerQuoteSellersQuotes->seller_id = Auth::id();
								$termBuyerQuoteSellersQuotes->initial_quote_price = isset($_POST['intialquote_'.$buyer_line_items_term_ids]) ? $_POST['intialquote_'.$buyer_line_items_term_ids] : "";
								$termBuyerQuoteSellersQuotes->final_quote_price = "";

								$termBuyerQuoteSellersQuotes->initial_rate_per_kg = isset($_POST['initial_rate_per_kg_'.$itemId]) ? $_POST['initial_rate_per_kg_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->final_rate_per_kg = "";
								$termBuyerQuoteSellersQuotes->initial_kg_per_cft = isset($_POST['initial_kg_per_cft_'.$itemId]) ? $_POST['initial_kg_per_cft_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->final_kg_per_cft = "";
								
								$termBuyerQuoteSellersQuotes->transport_charges = isset($_POST['transport_charges_'.$itemId]) ? $_POST['transport_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->odcharges = isset($_POST['od_charges_'.$itemId]) ? $_POST['od_charges_'.$itemId] : "";
								
								$termBuyerQuoteSellersQuotes->rate_per_cft = isset($_POST['rateper_kg_'.$itemId]) ? $_POST['rateper_kg_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->transit_days = isset($_POST['transit_days_'.$itemId]) ? $_POST['transit_days_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->crating_charges = isset($_POST['crating_charges_'.$_POST['buyer_item_id']]) ? $_POST['crating_charges_'.$_POST['buyer_item_id']] : "";
								$termBuyerQuoteSellersQuotes->storage_charges = isset($_POST['storate_charges_'.$_POST['buyer_item_id']]) ? $_POST['storate_charges_'.$_POST['buyer_item_id']] : "";
								$termBuyerQuoteSellersQuotes->escort_charges = isset($_POST['escort_charges_'.$_POST['buyer_item_id']]) ? $_POST['escort_charges_'.$_POST['buyer_item_id']] : "";
								$termBuyerQuoteSellersQuotes->handyman_charges = isset($_POST['handyman_charges_'.$_POST['buyer_item_id']]) ? $_POST['handyman_charges_'.$_POST['buyer_item_id']] : "";
								$termBuyerQuoteSellersQuotes->property_charges = isset($_POST['property_search_'.$_POST['buyer_item_id']]) ? $_POST['property_search_'.$_POST['buyer_item_id']] : "";
								$termBuyerQuoteSellersQuotes->brokerage_charge = isset($_POST['brokerage_'.$_POST['buyer_item_id']]) ? $_POST['brokerage_'.$_POST['buyer_item_id']] : "";
								
								$termBuyerQuoteSellersQuotes->fright_hundred = isset($_POST['frieghthundred_charges_'.$itemId]) ? $_POST['frieghthundred_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->fright_three_hundred = isset($_POST['frieghtthreehundred_charges_'.$itemId]) ? $_POST['frieghtthreehundred_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->fright_five_hundred = isset($_POST['frieghtfivehundred_charges_'.$itemId]) ? $_POST['frieghtfivehundred_charges_'.$itemId] : "";
								
								$termBuyerQuoteSellersQuotes->odlcl_charges = isset($_POST['odlcl_charges_'.$itemId]) ? $_POST['odlcl_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->odtwentyft_charges = isset($_POST['odtwentyft_charges_'.$itemId]) ? $_POST['odtwentyft_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->odfortyft_charges = isset($_POST['odfortyft_charges_'.$itemId]) ? $_POST['odfortyft_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->frieghtlcl_charges = isset($_POST['frieghtlcl_charges_'.$itemId]) ? $_POST['frieghtlcl_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->frieghttwentft_charges = isset($_POST['frieghttwenty_charges_'.$itemId]) ? $_POST['frieghttwenty_charges_'.$itemId] : "";
								$termBuyerQuoteSellersQuotes->frieghtfortyft_charges = isset($_POST['frieghtforty_charges_'.$itemId]) ? $_POST['frieghtforty_charges_'.$itemId] : "";
								
								$termBuyerQuoteSellersQuotes->seller_acceptance = "";
								$termBuyerQuoteSellersQuotes->firm_price = "";
								$termBuyerQuoteSellersQuotes->lkp_service_id = Session::get ( 'service_id' );
								$termBuyerQuoteSellersQuotes->initial_quote_created_at = $created_at;
								$termBuyerQuoteSellersQuotes->counter_quote_created_at = $created_at;
								$termBuyerQuoteSellersQuotes->final_quote_created_at = $created_at;
								$termBuyerQuoteSellersQuotes->is_saved = $save_button;
								$termBuyerQuoteSellersQuotes->is_submitted = $submit_button;
								$termBuyerQuoteSellersQuotes->created_by = Auth::id();
								$termBuyerQuoteSellersQuotes->created_at = $created_at;
								$termBuyerQuoteSellersQuotes->created_ip = $createdIp;
								$termBuyerQuoteSellersQuotes->save();
								
								if($submit_button == 1){
									//Sending mail to users after bid edit
									$buyers_selected_sellers_email = DB::table('users')->where('id', $buyer_id_for_item[0])->get();
									CommonComponent::send_email(SELLER_QUOTE_SUBMITTED_TERM, $buyers_selected_sellers_email);

									//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
									$getRandnumber  =   CommonComponent::getTransactionNumber($_POST['buyer_item_id']);

									if(Session::get('service_id') == ROAD_PTL){
										$servicename = 'LTL Term';
									}
									if(Session::get('service_id') == ROAD_FTL){
										$servicename = 'FTL Term';
									}
									if(Session::get('service_id') == RAIL){
										$servicename = 'RAIL Term';
									}
									if(Session::get('service_id') == AIR_DOMESTIC){
										$servicename = 'AIRDOMESTIC Term';
									}
									if(Session::get('service_id') == AIR_INTERNATIONAL){
										$servicename = 'AIRINTERNATIONAL Term';
									}
									if(Session::get('service_id') == OCEAN){
										$servicename = 'OCEAN Term';
									}
									if(Session::get('service_id') == COURIER){
										$servicename = 'COURIER Term';
									}
									if(Session::get('service_id') == RELOCATION_DOMESTIC){
										$servicename = 'RELOCATION Term';
									}
									if(Session::get('service_id') == RELOCATION_INTERNATIONAL){
										if((isset($_POST['frieghthundred_charges_'.$itemId]) && $_POST['frieghthundred_charges_'.$itemId]!="")){
										$servicename = 'RElOCATION AIR ITERNATIONAL Term';
										}else{
										$servicename = 'RElOCATION OCEAN ITERNATIONAL Term';
										}
									}
									if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY){
									
										$servicename = 'RElOCATION GLOBAL MOBILITY Term';
									
									}
									$msg_params = array(
											'randnumber' => $getRandnumber,
											'sellername' => Auth::User()->username,
											'servicename' => $servicename
									);
									$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_id_for_item[0]);
									CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_TERM_SMS,$msg_params);
									//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
								}
							}
						}else{
							$updatedAt = date ( 'Y-m-d H:i:s' );
							$updatedIp = $_SERVER ["REMOTE_ADDR"];
							if((isset($_POST['intialquote_'.$buyer_line_items_term_ids]) && $_POST['intialquote_'.$buyer_line_items_term_ids]!="") || (isset($_POST['initial_rate_per_kg_'.$itemId]) && $_POST['initial_rate_per_kg_'.$itemId]!="")
									|| (isset($_POST['rateper_kg_'.$itemId]) && $_POST['rateper_kg_'.$itemId]!="") || (isset($_POST['transport_charges_'.$itemId]) && $_POST['od_charges_'.$itemId]!="")
									|| (isset($_POST['frieghthundred_charges_'.$itemId]) && $_POST['frieghthundred_charges_'.$itemId]!="") || (isset($_POST['frieghtlcl_charges_'.$itemId]) && $_POST['frieghtlcl_charges_'.$itemId]!="")){
								$termBuyerQuoteSellersQuotes::where ( array("term_buyer_quote_item_id" => $itemId,'lkp_service_id' => Session::get ( 'service_id' ),'seller_id' => Auth::id()) )->update ( array (
										'buyer_id' => $buyer_id_for_item[0],
										'term_buyer_quote_item_id' => $itemId,
										'term_buyer_quote_id' => $_POST['buyer_item_id'],
										'initial_quote_price' => isset($_POST['intialquote_'.$buyer_line_items_term_ids]) ? $_POST['intialquote_'.$buyer_line_items_term_ids] : "",
										'final_quote_price' => "",
										'initial_rate_per_kg' => isset($_POST['initial_rate_per_kg_'.$itemId]) ? $_POST['initial_rate_per_kg_'.$itemId] : "",
										'final_rate_per_kg' => "",
										'initial_kg_per_cft' => isset($_POST['initial_kg_per_cft_'.$itemId]) ? $_POST['initial_kg_per_cft_'.$itemId] : "",
										'final_kg_per_cft' => "",
										'rate_per_cft' => isset($_POST['rateper_kg_'.$itemId]) ? $_POST['rateper_kg_'.$itemId] : "",
										'transport_charges' => isset($_POST['transport_charges_'.$itemId]) ? $_POST['transport_charges_'.$itemId]: "",
										'odcharges' => isset($_POST['od_charges_'.$itemId]) ? $_POST['od_charges_'.$itemId] : "",
										'transit_days' => isset($_POST['transit_days_'.$itemId]) ? $_POST['transit_days_'.$itemId] : "",
										'crating_charges' => isset($_POST['crating_charges_'.$_POST['buyer_item_id']]) ? $_POST['crating_charges_'.$_POST['buyer_item_id']] : "",
										'storage_charges' => isset($_POST['storate_charges_'.$_POST['buyer_item_id']]) ? $_POST['storate_charges_'.$_POST['buyer_item_id']] : "",
										'escort_charges' => isset($_POST['escort_charges_'.$_POST['buyer_item_id']]) ? $_POST['escort_charges_'.$_POST['buyer_item_id']] : "",
										'handyman_charges' => isset($_POST['handyman_charges_'.$_POST['buyer_item_id']]) ? $_POST['handyman_charges_'.$_POST['buyer_item_id']] : "",
										'property_charges' => isset($_POST['property_search_'.$_POST['buyer_item_id']]) ? $_POST['property_search_'.$_POST['buyer_item_id']] : "",
										'brokerage_charge' => isset($_POST['brokerage_'.$_POST['buyer_item_id']]) ? $_POST['brokerage_'.$_POST['buyer_item_id']] : "",
										'fright_hundred' => isset($_POST['frieghthundred_charges_'.$itemId]) ? $_POST['frieghthundred_charges_'.$itemId] : "",
										'fright_three_hundred' => isset($_POST['frieghtthreehundred_charges_'.$itemId]) ? $_POST['frieghtthreehundred_charges_'.$itemId] : "",
										'fright_five_hundred' => isset($_POST['frieghtfivehundred_charges_'.$itemId]) ? $_POST['frieghtfivehundred_charges_'.$itemId] : "",
										'odlcl_charges' => isset($_POST['odlcl_charges_'.$itemId]) ? $_POST['odlcl_charges_'.$itemId] : "",
										'odtwentyft_charges' => isset($_POST['odtwentyft_charges_'.$itemId]) ? $_POST['odtwentyft_charges_'.$itemId] : "",
										'odfortyft_charges' => isset($_POST['odfortyft_charges_'.$itemId]) ? $_POST['odfortyft_charges_'.$itemId] : "",
										'frieghtlcl_charges' => isset($_POST['frieghtlcl_charges_'.$itemId]) ? $_POST['frieghtlcl_charges_'.$itemId] : "",
										'frieghttwentft_charges' => isset($_POST['frieghttwenty_charges_'.$itemId]) ? $_POST['frieghttwenty_charges_'.$itemId] : "",
										'frieghtfortyft_charges' => isset($_POST['frieghtforty_charges_'.$itemId]) ? $_POST['frieghtforty_charges_'.$itemId] : "",
										'seller_acceptance' => "",
										'firm_price' => "",
										'lkp_service_id' => Session::get ( 'service_id' ),
										'initial_quote_created_at' => $updatedAt,
										'counter_quote_created_at' => $updatedAt,
										'final_quote_created_at' => $updatedAt,
										'is_saved' => $save_button,
										'is_submitted' => $submit_button,
										'updated_at' => $updatedAt,
										'updated_by' => Auth::User ()->id,
										'updated_ip' => $updatedIp
								) );
								
								if($submit_button == 1){
								
									//Sending mail to users after bid edit
									$buyers_selected_sellers_email = DB::table('users')->where('id', $buyer_id_for_item[0])->get();
									CommonComponent::send_email(SELLER_QUOTE_SUBMITTED_TERM, $buyers_selected_sellers_email);

									//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
									$getRandnumber  =   CommonComponent::getTransactionNumber($_POST['buyer_item_id']);

									if(Session::get('service_id') == ROAD_PTL){
										$servicename = 'LTL Term';
									}
									if(Session::get('service_id') == ROAD_FTL){
										$servicename = 'FTL Term';
									}
									if(Session::get('service_id') == RAIL){
										$servicename = 'RAIL Term';
									}
									if(Session::get('service_id') == AIR_DOMESTIC){
										$servicename = 'AIRDOMESTIC Term';
									}
									if(Session::get('service_id') == AIR_INTERNATIONAL){
										$servicename = 'AIRINTERNATIONAL Term';
									}
									if(Session::get('service_id') == OCEAN){
										$servicename = 'OCEAN Term';
									}
									if(Session::get('service_id') == COURIER){
										$servicename = 'COURIER Term';
									}
									if(Session::get('service_id') == RELOCATION_DOMESTIC){
										$servicename = 'RELOCATION Term';
									}
									if(Session::get('service_id') == RELOCATION_INTERNATIONAL){
										if((isset($_POST['frieghthundred_charges_'.$itemId]) && $_POST['frieghthundred_charges_'.$itemId]!="")){
											$servicename = 'RElOCATION AIR ITERNATIONAL Term';
										}else{
											$servicename = 'RElOCATION OCEAN ITERNATIONAL Term';
										}
									}
									if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY){
										
											$servicename = 'RElOCATION GLOBAL MOBILITY Term';
										
									}
									$msg_params = array(
											'randnumber' => $getRandnumber,
											'sellername' => Auth::User()->username,
											'servicename' => $servicename
									);
									$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_id_for_item[0]);
									CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_TERM_SMS,$msg_params);
									//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
								}
								
								
							}
						}

					}
				}
			
		}catch (Exception $e) {
		
		}
		
	}
	
	//Courier Term Quote submission
	public function courierTermIntialQuoteSeller() {
		try{
			
			$itemId = $_POST['buyer_item_id'];
			
			$buyer_id_for_item = CommonComponent::getBuyerId($_POST['buyer_item_id']);
			$created_at = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			if(isset($_POST['submit']) && $_POST['submit'] != ''){
				$submit_button = 1;
				$save_button = 0;
			}else{
				$save_button = 1;
				$submit_button = 0;
			}
			
			
	
			
			if (isset($_POST['buyer_item_id'])) {
	
					$termBuyerQuoteSellersQuotes  =  new TermBuyerQuoteSellersQuotesPrice();
					$termLineItemsExits = DB::table('term_buyer_quote_sellers_quotes_prices')
					->where('term_buyer_quote_sellers_quotes_prices.term_buyer_quote_id','=',$itemId)
					->where('term_buyer_quote_sellers_quotes_prices.seller_id','=',Auth::User ()->id)
					->select('*')
					->get();
					
					if(count($termLineItemsExits) == 0){
						if((isset($_POST['conversion_'.$itemId]) && $_POST['conversion_'.$itemId]!="")){
	
							$termBuyerQuoteSellersQuotes->buyer_id = $buyer_id_for_item[0];
							$termBuyerQuoteSellersQuotes->term_buyer_quote_item_id = $_POST['buyer_item_id'];
							$termBuyerQuoteSellersQuotes->term_buyer_quote_id = $_POST['buyer_item_id'];
							$termBuyerQuoteSellersQuotes->seller_id = Auth::id();
							
							$termBuyerQuoteSellersQuotes->incremental_weight = isset($_POST['increment_weight_'.$itemId]) ? $_POST['increment_weight_'.$itemId] : "";
							$termBuyerQuoteSellersQuotes->incremental_weight_price = isset($_POST['increment_value_'.$itemId]) ? $_POST['increment_value_'.$itemId] : "";
							
							$termBuyerQuoteSellersQuotes->conversion_factor = isset($_POST['conversion_'.$itemId]) ? $_POST['conversion_'.$itemId] : "";
							$termBuyerQuoteSellersQuotes->max_weight_accepted = isset($_POST['maxweightaccept_'.$itemId]) ? $_POST['maxweightaccept_'.$itemId] : "";
							$termBuyerQuoteSellersQuotes->transit_days = isset($_POST['transitdays_'.$itemId]) ? $_POST['transitdays_'.$itemId] : "";
							
							$termBuyerQuoteSellersQuotes->fuel_charges = isset($_POST['fuel_surcharge_'.$itemId]) ? $_POST['fuel_surcharge_'.$itemId] : "";
							$termBuyerQuoteSellersQuotes->cod_charges = isset($_POST['cod_charge_'.$itemId]) ? $_POST['cod_charge_'.$itemId] : "";							
							$termBuyerQuoteSellersQuotes->freight_charges = isset($_POST['freight_charge_'.$itemId]) ? $_POST['freight_charge_'.$itemId] : "";	
							$termBuyerQuoteSellersQuotes->arc_charges = isset($_POST['arc_charge_'.$itemId]) ? $_POST['arc_charge_'.$itemId] : "";	
							$termBuyerQuoteSellersQuotes->max_value = isset($_POST['max_value_'.$itemId]) ? $_POST['max_value_'.$itemId] : "";	
							
							$termBuyerQuoteSellersQuotes->seller_acceptance = "";
							$termBuyerQuoteSellersQuotes->firm_price = "";
							$termBuyerQuoteSellersQuotes->lkp_service_id = Session::get ( 'service_id' );
							$termBuyerQuoteSellersQuotes->initial_quote_created_at = $created_at;
							$termBuyerQuoteSellersQuotes->counter_quote_created_at = $created_at;
							$termBuyerQuoteSellersQuotes->final_quote_created_at = $created_at;
							$termBuyerQuoteSellersQuotes->is_saved = $save_button;
							$termBuyerQuoteSellersQuotes->is_submitted = $submit_button;
							$termBuyerQuoteSellersQuotes->created_by = Auth::id();
							$termBuyerQuoteSellersQuotes->created_at = $created_at;
							$termBuyerQuoteSellersQuotes->created_ip = $createdIp;
							$termBuyerQuoteSellersQuotes->save();
	
							
							
							if($submit_button == 1){
								//Sending mail to users after bid edit
								$buyers_selected_sellers_email = DB::table('users')->where('id', $buyer_id_for_item[0])->get();
								CommonComponent::send_email(SELLER_QUOTE_SUBMITTED_TERM, $buyers_selected_sellers_email);
	
								//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
								$getRandnumber  =   CommonComponent::getTransactionNumber($_POST['buyer_item_id']);
	
								
								$servicename = 'COURIER Term';
								
								$msg_params = array(
										'randnumber' => $getRandnumber,
										'sellername' => Auth::User()->username,
										'servicename' => $servicename
								);
								$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_id_for_item[0]);
								CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_TERM_SMS,$msg_params);
								//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
							}
							if($_POST['increment']>0){
							
								for($i=1;$i<=$_POST['increment'];$i++){
										
									$termBuyerQuoteSellersQuotesSlabs  =  new TermBuyerQuoteSellersQuotesPriceSlab();
									$termBuyerQuoteSellersQuotesSlabs->term_buyer_quote_id = $_POST['buyer_item_id'];
									$termBuyerQuoteSellersQuotesSlabs->term_buyer_quote_sellers_quotes_price_id = $termBuyerQuoteSellersQuotes->id;
									$termBuyerQuoteSellersQuotesSlabs->seller_id = Auth::id();
									$termBuyerQuoteSellersQuotesSlabs->buyer_id = $buyer_id_for_item[0];
									$termBuyerQuoteSellersQuotesSlabs->slab_min_rate = $_POST['slab_min_'.$i.'_'.$itemId];
									$termBuyerQuoteSellersQuotesSlabs->slab_max_rate = $_POST['slab_max_'.$i.'_'.$itemId];
									$termBuyerQuoteSellersQuotesSlabs->slab_rate = $_POST['slab_'.$i.'_'.$itemId];
									$termBuyerQuoteSellersQuotesSlabs->created_by =Auth::id();
									$termBuyerQuoteSellersQuotesSlabs->created_at = $created_at;
									$termBuyerQuoteSellersQuotesSlabs->created_ip = $createdIp;
									$termBuyerQuoteSellersQuotesSlabs->save();
								}
							}
						}
						
					}else{
					
						$updatedAt = date ( 'Y-m-d H:i:s' );
						$updatedIp = $_SERVER ["REMOTE_ADDR"];
						if(isset($_POST['buyer_item_id']) && $_POST['buyer_item_id']!=""){
							$termBuyerQuoteSellersQuotes::where ( array(
										"term_buyer_quote_id" => $itemId,
										'lkp_service_id' => Session::get ( 'service_id' ),
										'seller_id' => Auth::id()) )
									->update ( array (
											
									'incremental_weight' => isset($_POST['increment_weight_'.$itemId]) ? $_POST['increment_weight_'.$itemId] : "",
									'incremental_weight_price' => isset($_POST['increment_value_'.$itemId]) ? $_POST['increment_value_'.$itemId] : "",
									
									'conversion_factor' => isset($_POST['conversion_'.$itemId]) ? $_POST['conversion_'.$itemId] : "",
									'max_weight_accepted' => isset($_POST['maxweightaccept_'.$itemId]) ? $_POST['maxweightaccept_'.$itemId] : "",
									'transit_days' => isset($_POST['transitdays_'.$itemId]) ? $_POST['transitdays_'.$itemId] : "",
									
									
									'fuel_charges' => isset($_POST['fuel_surcharge_'.$itemId]) ? $_POST['fuel_surcharge_'.$itemId] : "",
									'cod_charges' => isset($_POST['cod_charge_'.$itemId]) ? $_POST['cod_charge_'.$itemId] : "",
									'freight_charges' => isset($_POST['freight_charge_'.$itemId]) ? $_POST['freight_charge_'.$itemId] : "",
									'arc_charges' => isset($_POST['arc_charge_'.$itemId]) ? $_POST['arc_charge_'.$itemId] : "",
									'max_value' => isset($_POST['max_value_'.$itemId]) ? $_POST['max_value_'.$itemId] : "",
									
									
									'lkp_service_id' => Session::get ( 'service_id' ),
									'initial_quote_created_at' => $updatedAt,
									'counter_quote_created_at' => $updatedAt,
									'final_quote_created_at' => $updatedAt,
									'is_saved' => $save_button,
									'is_submitted' => $submit_button,
									'updated_at' => $updatedAt,
									'updated_by' => Auth::User ()->id,
									'updated_ip' => $updatedIp
							) );

									
									
							
							if($_POST['increment']>0){
							
								for($i=1;$i<=$_POST['increment'];$i++){
								$updatedAt = date ( 'Y-m-d H:i:s' );
								$updatedIp = $_SERVER ["REMOTE_ADDR"];
								$termBuyerQuoteSellersQuotesSlabs  =  new TermBuyerQuoteSellersQuotesPriceSlab();
								$termBuyerQuoteSellersQuotesSlabs::where ( array(
										"term_buyer_quote_id" => $itemId,
										'seller_id' => Auth::id(),
										'slab_min_rate'=>$_POST['slab_min_'.$i.'_'.$itemId],
										'slab_max_rate'=>$_POST['slab_max_'.$i.'_'.$itemId],) )
										->update ( array (
											'slab_rate' => $_POST['slab_'.$i.'_'.$itemId],
											'updated_at' => $updatedAt,
											'updated_by' => Auth::User ()->id,
											'updated_ip' => $updatedIp,
										) );
								}
							}	
								
							
							
									
						
							if($submit_button == 1){
						
								//Sending mail to users after bid edit
								$buyers_selected_sellers_email = DB::table('users')->where('id', $buyer_id_for_item[0])->get();
								CommonComponent::send_email(SELLER_QUOTE_SUBMITTED_TERM, $buyers_selected_sellers_email);
						
								//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
								$getRandnumber  =   CommonComponent::getTransactionNumber($_POST['buyer_item_id']);
						
								
								if(Session::get('service_id') == COURIER){
									$servicename = 'COURIER Term';
								}
								
								$msg_params = array(
										'randnumber' => $getRandnumber,
										'sellername' => Auth::User()->username,
										'servicename' => $servicename
								);
								$getMobileNumber  =   CommonComponent::getMobleNumber($buyer_id_for_item[0]);
								CommonComponent::sendSMS($getMobileNumber,SELLER_SUBMITT_QOUTE_TERM_SMS,$msg_params);
								//*******Send Sms to the buyers,seller submit a quote from a serach***********************//
							}
						
						
						}
						
						
					}
			
			
				}
			
				
			
		}catch (Exception $e) {
	
		}
	
	}
	
	
	
	
	public function TermSellerSearchResults() {
		try{
			
			if(!empty(Input::all()))  {
				$termRequestdata=Input::all();
			}
			
			$roleId = Auth::User ()->lkp_role_id;
	
			//Retrieval of post statuses
			$posts_status = CommonComponent::getPostStatuses();
	
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
	
			// Saving the user activity to the log table
			if ($roleId == SELLER) {
				// CommonComponent::activityLog("SELLER_LISTED_SEARCH_ITEMS",
				// SELLER_LISTED_SEARCH_ITEMS,0,
				// HTTP_REFERRER,CURRENT_URL);
			}
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

			$vehicletypemasters = CommonComponent::getAllVehicleTypes();
			$loadtypemasters = CommonComponent::getAllLoadTypes();
			$packagingtypesmasters = CommonComponent::getAllPackageTypes();
			$shipmenttypes = CommonComponent::getAllShipmentTypes();
			$senderidentity = CommonComponent::getAllSenderIdentities();
			$bid_type_value='';
			if(isset($_REQUEST['bid_type_value'])){
			$bid_type_value=$_REQUEST['bid_type_value'];
			}
			switch($serviceId){
				case ROAD_FTL       : 
					/*CommonComponent::activityLog("FTL_SELLER_SEARCH_FORM_RESULTS",
				FTL_SELLER_SEARCH_FORM_RESULTS,0,
				HTTP_REFERRER,CURRENT_URL);*/
				$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );

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
					$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
					$lkp_vehicle_type_name_results = CommonComponent::getVehicleType($_REQUEST['lkp_vehicle_type_id']);
				}else{
					$lkp_load_type_name_results = '';
					$lkp_vehicle_type_name_results = '';
				}


				$grid = $result ['grid'];
				$filter = $result ['filter'];
				
				if(isset($_REQUEST['term_from_city_id'])){
					$from_city_id=$_REQUEST['term_from_city_id'];
				}else{
					$from_city_id=$_REQUEST['from_city_id'];
				}
				if(isset($_REQUEST['term_to_city_id'])){
					$to_city_id=$_REQUEST['term_to_city_id'];
				}else{
					$to_city_id=$_REQUEST['to_city_id'];
				}
				if(isset($_REQUEST['term_from_location'])){
					$from_location=$_REQUEST['term_from_location'];
				}else{
					$from_location=$_REQUEST['from_location'];
				}
				if(isset($_REQUEST['term_to_location'])){
					$to_location=$_REQUEST['term_to_location'];
				}else{
					$to_location=$_REQUEST['to_location'];
				}
				return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'load_type_name'  => $lkp_load_type_name,
						'vehicle_type_name'  => $lkp_vehicle_type_name,
						'load_type_name_results'  => $lkp_load_type_name_results,
						'vehicle_type_name_results'  => $lkp_vehicle_type_name_results,
						'from_city_id'  => $from_city_id,
						'to_city_id' => $to_city_id,
						'from_location'  => $from_location,
						'to_location' => $to_location,
						'seller_district_id' => $_REQUEST['seller_district_id'],
						'lkp_load_type_id' => $_REQUEST['lkp_load_type_id'],
						'lkp_vehicle_type_id' => $_REQUEST['lkp_vehicle_type_id'],
					    'loadtypemasters' => $loadtypemasters,'vehicletypemasters' => $vehicletypemasters,
						'bid_type_value' => $bid_type_value
						] );
				break;
				case ROAD_PTL       :
				case RAIL       :
				case AIR_DOMESTIC       :
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');
					if($results_count_view == 1){
						$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						$lkp_packaging_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					}else{
						$lkp_load_type_name = '';
						$lkp_packaging_type_name = '';
					}

					if($results_count_more_view == 2){
						$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						$lkp_packaging_type_name_results = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					}else{
						$lkp_load_type_name_results = '';
						$lkp_packaging_type_name_results = '';
					}


					$grid = $result ['grid'];
					$filter = $result ['filter'];
                                        if(isset($_REQUEST['lkp_load_type_id']))
                                                    $packagingtypesmasters=  CommonComponent::getLoadBasedAllPackages($_REQUEST['lkp_load_type_id']);
					return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'load_type_name'  => $lkp_load_type_name,
						'packaging_type_name'  => $lkp_packaging_type_name,
						'load_type_name_results'  => $lkp_load_type_name_results,
						'packaging_type_name_results'  => $lkp_packaging_type_name_results,
						'from_city_id'  => $_REQUEST['term_from_location_id'],
						'to_city_id' => $_REQUEST['term_to_location_id'],
						'from_location'  => $_REQUEST['term_from_location'],
						'to_location' => $_REQUEST['term_to_location'],
						'seller_district_id' => 1,
						'lkp_load_type_id' => $_REQUEST['lkp_load_type_id'],
						'lkp_packaging_type_id' => $_REQUEST['lkp_packaging_type_id'],
						'loadtypemasters' => $loadtypemasters,
						'vehicletypemasters' => $vehicletypemasters,
						'packagingtypesmasters' => $packagingtypesmasters,
						'bid_type_value' => $bid_type_value
					] );
				break;
				case COURIER       :
					
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');
					if($results_count_view == 1){
						$lkp_load_type_name ='';
						$lkp_packaging_type_name = '';
					}else{
						$lkp_load_type_name = '';
						$lkp_packaging_type_name = '';
					}
				
					if($results_count_more_view == 2){
						$lkp_load_type_name_results = '';
						$lkp_packaging_type_name_results = '';
					}else{
						$lkp_load_type_name_results = '';
						$lkp_packaging_type_name_results = '';
					}
				
				
					$grid = $result ['grid'];
					$filter = $result ['filter'];
					if(isset($_REQUEST['courier_types'])){
						$courier_types = $_REQUEST['courier_types'];
					}else{
						$courier_types = 'Parcel';
					}
					if(isset($_REQUEST['post_delivery_type'])){
						$post_deliverytypes= $_REQUEST['post_delivery_type'];
					}else{
						$post_deliverytypes = 'Domestic';
					}
					return view ( 'term.sellers.term_seller_search_list', [
							'grid' => $grid,
							'filter' => $filter,
							'load_type_name'  => $lkp_load_type_name,
							'packaging_type_name'  => $lkp_packaging_type_name,
							'load_type_name_results'  => $lkp_load_type_name_results,
							'packaging_type_name_results'  => $lkp_packaging_type_name_results,
							'from_city_id'  => $_REQUEST['term_from_location_id'],
							'to_city_id' => $_REQUEST['term_to_location_id'],
							'from_location'  => $_REQUEST['term_from_location'],
							'to_location' => $_REQUEST['term_to_location'],
							'seller_district_id' => 1,
							'courier_types' => $courier_types,
							'courier_delivery_type' => $post_deliverytypes,
							'loadtypemasters' => $loadtypemasters,
							'vehicletypemasters' => $vehicletypemasters,
							'packagingtypesmasters' => $packagingtypesmasters,
							'bid_type_value' => $bid_type_value
							] );
					break;

				case AIR_INTERNATIONAL       :
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );

					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');

					if($results_count_view == 1){
						$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						$lkp_packaging_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					}else{
						$lkp_load_type_name = '';
						$lkp_packaging_type_name = '';
					}

					if($results_count_more_view == 2){
						if(isset($_REQUEST['lkp_load_type_id'])){
							$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						}else{
							$lkp_load_type_name_results = "";
						}
						if(isset($_REQUEST['lkp_packaging_type_id'])){
							$lkp_packaging_type_name_results = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
						}else{
							$lkp_packaging_type_name_results = "";
						}

					}else{
						if(isset($_REQUEST['lkp_load_type_id'])){
							$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						}else{
							$lkp_load_type_name_results = "";
						}
						if(isset($_REQUEST['lkp_packaging_type_id'])){
							$lkp_packaging_type_name_results = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
						}else{
							$lkp_packaging_type_name_results = "";
						}
					}


					$grid = $result ['grid'];
					$filter = $result ['filter'];
					return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'load_type_name'  => $lkp_load_type_name,
						'packaging_type_name'  => $lkp_packaging_type_name,
						'load_type_name_results'  => $lkp_load_type_name_results,
						'packaging_type_name_results'  => $lkp_packaging_type_name_results,
						'from_city_id'  => $_REQUEST['term_from_location_id'],
						'to_city_id' => $_REQUEST['term_to_location_id'],
						'from_location'  => $_REQUEST['term_from_location'],
						'to_location' => $_REQUEST['term_to_location'],
						'seller_district_id' => 1,
						'lkp_load_type_id' => $_REQUEST['lkp_load_type_id'],
						'lkp_packaging_type_id' => $_REQUEST['lkp_packaging_type_id'],
						'loadtypemasters' => $loadtypemasters,'vehicletypemasters' => $vehicletypemasters,'packagingtypesmasters' => $packagingtypesmasters,'shipmenttypes' => $shipmenttypes,'senderidentity' =>$senderidentity,
							'bid_type_value' => $bid_type_value
					] );
				break;
				case OCEAN       :
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');
					if($results_count_view == 1){
						$lkp_load_type_name = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						$lkp_packaging_type_name = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					}else{
						$lkp_load_type_name = '';
						$lkp_packaging_type_name = '';
					}

					if($results_count_more_view == 2){
						$lkp_load_type_name_results = CommonComponent::getLoadType($_REQUEST['lkp_load_type_id']);
						$lkp_packaging_type_name_results = CommonComponent::getPackageType($_REQUEST['lkp_packaging_type_id']);
					}else{
						$lkp_load_type_name_results = '';
						$lkp_packaging_type_name_results = '';
					}


					$grid = $result ['grid'];
					$filter = $result ['filter'];
					return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'load_type_name'  => $lkp_load_type_name,
						'packaging_type_name'  => $lkp_packaging_type_name,
						'load_type_name_results'  => $lkp_load_type_name_results,
						'packaging_type_name_results'  => $lkp_packaging_type_name_results,
						'from_city_id'  => $_REQUEST['term_from_location_id'],
						'to_city_id' => $_REQUEST['term_to_location_id'],
						'from_location'  => $_REQUEST['term_from_location'],
						'to_location' => $_REQUEST['term_to_location'],
						'seller_district_id' => 1,
						'lkp_load_type_id' => $_REQUEST['lkp_load_type_id'],
						'lkp_packaging_type_id' => $_REQUEST['lkp_packaging_type_id'],
						'loadtypemasters' => $loadtypemasters,'vehicletypemasters' => $vehicletypemasters,'packagingtypesmasters' => $packagingtypesmasters,'shipmenttypes' => $shipmenttypes,'senderidentity' =>$senderidentity,
						'bid_type_value' => $bid_type_value
					] );
				break;
				case RELOCATION_DOMESTIC       :
				$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
				$results_count_view = Session::get('results_count');
				$results_count_more_view = Session::get('results_count_more');

				$grid = $result ['grid'];
				$filter = $result ['filter'];
				$ratecarttype = "";
				if(isset($_REQUEST['term_post_rate_card_type'])){
					$ratecarttype = DB::table('lkp_post_ratecard_types')->where('id','=',$_REQUEST['term_post_rate_card_type'])->pluck('ratecard_type');
				}

				if(isset($_REQUEST['term_from_city_id'])){
					$from_city_id=$_REQUEST['term_from_city_id'];
				}else{
					$from_city_id=$_REQUEST['from_city_id'];
				}
				if(isset($_REQUEST['term_to_city_id'])){
					$to_city_id=$_REQUEST['term_to_city_id'];
				}else{
					$to_city_id=$_REQUEST['to_city_id'];
				}
				if(isset($_REQUEST['term_from_location'])){
					$from_location=$_REQUEST['term_from_location'];
				}else{
					$from_location=$_REQUEST['from_location'];
				}
				if(isset($_REQUEST['term_to_location'])){
					$to_location=$_REQUEST['term_to_location'];
				}else{
					$to_location=$_REQUEST['to_location'];
				}
				return view ( 'term.sellers.term_seller_search_list', [
					'grid' => $grid,
					'filter' => $filter,
					'from_city_id'  => $from_city_id,
					'to_city_id' => $to_city_id,
					'from_location'  => $from_location,
					'to_location' => $to_location,
					'seller_district_id' => $_REQUEST['seller_district_id'],
					'bid_type_value' => $bid_type_value,
					'ratecarttype' => $ratecarttype,

				] );
				break;
				case RELOCATION_INTERNATIONAL       :
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');

					$grid = $result ['grid'];
					$filter = $result ['filter'];
					$ratecarttype = "";
					if(isset($_REQUEST['term_post_rate_card_type'])){
						$ratecarttype = DB::table('lkp_post_ratecard_types')->where('id','=',$_REQUEST['term_post_rate_card_type'])->pluck('ratecard_type');
					}

					if(isset($_REQUEST['term_from_city_id'])){
						$from_city_id=$_REQUEST['term_from_city_id'];
					}else{
						$from_city_id=$_REQUEST['from_city_id'];
					}
					if(isset($_REQUEST['term_to_city_id'])){
						$to_city_id=$_REQUEST['term_to_city_id'];
					}else{
						$to_city_id=$_REQUEST['to_city_id'];
					}
					if(isset($_REQUEST['term_from_location'])){
						$from_location=$_REQUEST['term_from_location'];
					}else{
						$from_location=$_REQUEST['from_location'];
					}
					if(isset($_REQUEST['term_to_location'])){
						$to_location=$_REQUEST['term_to_location'];
					}else{
						$to_location=$_REQUEST['to_location'];
					}
					return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'from_city_id'  => $from_city_id,
						'to_city_id' => $to_city_id,
						'from_location'  => $from_location,
						'to_location' => $to_location,
						'seller_district_id' => $_REQUEST['seller_district_id'],
						'bid_type_value' => $bid_type_value,
						'ratecarttype' => $ratecarttype,

					] );
					break;
				case ROAD_INTRACITY :
					break;
				case ROAD_TRUCK_HAUL:
					break;
				case RELOCATION_GLOBAL_MOBILITY:
					$result = TermSellerComponent::getTermSellerSearchList ( $roleId, $serviceId,$statusId );
					$results_count_view = Session::get('results_count');
					$results_count_more_view = Session::get('results_count_more');

					$grid = $result ['grid'];
					$filter = $result ['filter'];
					$ratecarttype = "";
					if(isset($_REQUEST['term_post_rate_card_type'])){
						$ratecarttype = DB::table('lkp_post_ratecard_types')->where('id','=',$_REQUEST['term_post_rate_card_type'])->pluck('ratecard_type');
					}

					if(isset($_REQUEST['term_from_city_id'])){
						$from_city_id=$_REQUEST['term_from_city_id'];
					}else{
						$from_city_id=$_REQUEST['from_city_id'];
					}
					if(isset($_REQUEST['term_from_location'])){
						$from_location=$_REQUEST['term_from_location'];
					}else{
						$from_location=$_REQUEST['from_location'];
					}

				    $lkp_relgm_services = array();
        			$lkp_relgm_services = CommonComponent::getLkpRelocationGMServices();				    
					return view ( 'term.sellers.term_seller_search_list', [
						'grid' => $grid,
						'filter' => $filter,
						'from_city_id'  => $from_city_id,
						'from_location'  => $from_location,
						'seller_district_id' => $_REQUEST['seller_district_id'],
						'bid_type_value' => $bid_type_value,
						'ratecarttype' => $ratecarttype,
						'lkp_relgm_services' => $lkp_relgm_services,
						'spot_or_term' => $_REQUEST['spot_or_term'],
					] );
				break;
				default             :
					break;
			}
	
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
			
	}
        
        
        //term contract detail page
        public function showContractDetails($id) {
        Log::info('Seller has viewed Contract Details page:' . Auth::User ()->id, array('c' => '1'));
        if (isset($id) && ($id > 0)) {
            $serviceId = Session::get('service_id');
         
            $query = DB::table('term_contracts as tc');
            
            $query->leftJoin('term_buyer_quotes as bq', 'tc.term_buyer_quote_id', '=', 'bq.id')
                ->leftjoin('term_buyer_quote_items as bqi', 'tc.term_buyer_quote_item_id', '=', 'bqi.id')
                ->leftJoin('term_buyer_quote_sellers_quotes_prices as sp', 'sp.term_buyer_quote_item_id', '=', 'tc.term_buyer_quote_item_id');
                    
                    
                switch ($serviceId) {
                    case ROAD_FTL :
                    case ROAD_INTRACITY :
                    case RELOCATION_INTERNATIONAL :
                    $query->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                    break;
                    case ROAD_PTL :
                    case RAIL :
                    case AIR_DOMESTIC :
                    $query->leftJoin('lkp_ptl_pincodes as lc', 'lc.id', '=', 'bqi.from_location_id');
                    $query->leftJoin('lkp_ptl_pincodes as lcity', 'lcity.id', '=', 'bqi.to_location_id');
                    break;
                    case COURIER :
                    	
                    $query->leftjoin('lkp_ptl_pincodes as lc',  'lc.id', '=', 'bqi.from_location_id');
                    $query->leftjoin('lkp_ptl_pincodes as lcity', function($join)
                    {
                    		$join->on('bqi.to_location_id', '=', 'lcity.id');
                    		$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
                    		 
                    });
                    $query->leftjoin('lkp_countries as lcity1', function($join)
                    {
                    		$join->on('bqi.to_location_id', '=', 'lcity1.id');
                    		$join->on(DB::raw('bq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
                    		 
                    });
                    	
                  
                    break;
                    case AIR_INTERNATIONAL :
                    $query->leftJoin('lkp_airports as lc', 'lc.id', '=', 'bqi.from_location_id');
                    $query->leftJoin('lkp_airports as lcity', 'lcity.id', '=', 'bqi.to_location_id');
                    break;
                    case OCEAN :
                    $query->leftJoin('lkp_seaports as lc', 'lc.id', '=', 'bqi.from_location_id');
                    $query->leftJoin('lkp_seaports as lcity', 'lcity.id', '=', 'bqi.to_location_id');
                    break;
                    case RELOCATION_GLOBAL_MOBILITY :
                    	$query->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
                    break;
                    default :
                    $query->leftJoin('lkp_cities as lc', 'bqi.from_location_id', '=', 'lc.id');
                    $query->leftJoin('lkp_cities as lcity', 'bqi.to_location_id', '=', 'lcity.id');
                    break;
                }
                    $query->leftJoin('lkp_order_statuses as os', 'tc.contract_status', '=', 'os.id')
                    ->leftJoin('lkp_bid_types as bt', 'bt.id', '=', 'bq.lkp_bid_type_id')
                    ->leftJoin('lkp_vehicle_types as lvt', 'bqi.lkp_vehicle_type_id', '=', 'lvt.id')
                    ->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'bqi.lkp_load_type_id')
                    ->leftjoin('users as u', 'u.id', '=', 'tc.created_by')
                    ->where('tc.seller_id', '=', Auth::User ()->id)
                    ->where('tc.term_buyer_quote_id', '=', $id)
                    ->where('tc.lkp_service_id',$serviceId)
                    ->groupBy('tc.term_buyer_quote_item_id');
                    switch ($serviceId) {
                    case ROAD_FTL :
                    case ROAD_INTRACITY :  
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','bt.bid_type','bqi.quantity','sp.initial_quote_price','bq.from_date','bq.to_date');
                    break;
                    case ROAD_PTL :
                    case RAIL :
                    case AIR_DOMESTIC :
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from','lcity.postoffice_name as to','bt.bid_type','bqi.*','sp.initial_quote_price','bq.from_date','bq.to_date');
                    break;
                    case COURIER :
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from',
                    DB::raw("(case when `bq`.`lkp_courier_delivery_type_id` = 1 then lcity.postoffice_name  when `bq`.`lkp_courier_delivery_type_id` = 2 then lcity1.country_name end) as 'to'"),
                    'bt.bid_type','bqi.*','sp.initial_quote_price','bq.from_date','bq.to_date');
                    break;
                    case AIR_INTERNATIONAL :
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.airport_name as from','lcity.airport_name as to','bt.bid_type','bqi.*','sp.initial_quote_price','bq.from_date','bq.to_date');
                    break;
                    case OCEAN :
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.seaport_name as from','lcity.seaport_name as to','bt.bid_type','bqi.*','sp.initial_quote_price','bq.from_date','bq.to_date');
                    break;
                    case RELOCATION_INTERNATIONAL :
                    $query->select('tc.*','u.username','os.order_status','lc.city_name as from','lcity.city_name as to','bt.bid_type','bqi.avg_kg_per_move','bqi.number_loads','bq.from_date','bq.to_date');
                    break;
                    case RELOCATION_GLOBAL_MOBILITY :
                    $query->select('tc.*','u.username','os.order_status','lc.city_name as from','bt.bid_type','bqi.lkp_gm_service_id','bqi.measurement','bqi.measurement_units','bq.from_date','bq.to_date');
                    break;
                    default :
                    $query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','bt.bid_type','bqi.*','sp.initial_quote_price','bq.from_date','bq.to_date');
                        break;
                }
                    
                    $contractDetails   =   $query->get();
                    return view('term.sellers.seller_contract_details', array(
                    		'contractDetails' => $contractDetails,
                    		'serviceId'=>$serviceId
                    		
                    ));
          
            
        } else {
            return view('orders.seller_order');
        }
    }
	
}