<?php
namespace App\Http\Controllers;

use App\Components\CommonComponent;
use App\Components\Term\TermBuyerComponent;
use App\Models\TermBuyerQuoteSlab;
use App\Components\Ftl\FtlBuyerComponent;
use App\Components\BuyerComponent;
use App\Components\MessagesComponent;
use App\Models\CartItem;
use App\Models\ViewCartItem;

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
use ZipArchive;
use App\Models\TermBuyerQuote;
use App\Models\TermBuyerQuoteItem;
use App\Models\TermBuyerQuoteSelectedSeller;
use App\Models\TermBuyerBidDate;
use App\Models\TermBuyerQuoteBidTermsFile;
use App\Models\TermContract;
use App\Models\TermBuyerQuoteSellersQuotesPrice;




/* Term controller switch cases.
 * Create quote functions pass below controller
 */
class BuyerTermController extends Controller {
	
	public function buyerTermPosts() {
		
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
				case ROAD_FTL : CommonComponent::activityLog("FTL_BUYER_LISTED_POSTS",
											 FTL_BUYER_LISTED_POSTS,0,
											 HTTP_REFERRER,CURRENT_URL);
				                      $result = TermBuyerComponent::getTermBuyerPostlists($serviceId,$post_status,$enquiry_type);
									  $grid = $result ['grid'];
									  $filter = $result ['filter'];
									  //rendering the view with the data grid
									  return view ( 'term.buyers.term_buyer_posts_list', [
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
	
	public function getTermPostBuyerCounterOffer($buyerQuoteItemId){
		
		try {
			$roleId = Auth::User()->lkp_role_id;
			$serviceId = Session::get('service_id');
		
		
			//Loading respective service data grid
			switch($serviceId){
				case ROAD_FTL       :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :
                    case RELOCATION_GLOBAL_MOBILITY :
                         $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
					$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
					
					return view('term.buyers.termbuyerpostcounteroffer',
							[
							'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
							'fromLocation' => $buyerOfferDetails['fromLocation'],
							'toLocation' => $buyerOfferDetails['toLocation'],
							'deliveryDate' => $buyerOfferDetails['deliveryDate'],
							'dispatchDate' => $buyerOfferDetails['dispatchDate'],
							'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
							'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
							'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
							'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
							'packagingType' =>  $buyerOfferDetails['packagingType'],
							'countCartItems' =>  $buyerOfferDetails['countCartItems'],
							'countview' =>  $buyerOfferDetails['countview'],
							'buyerQuoteId' => $buyerQuoteItemId,
							'serviceId' => $serviceId,
							'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                    'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                    'allMessagesList' =>  $allMessagesList,    
							
							]
					);
							break;
					case ROAD_PTL  :
						$allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
						$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
							
						return view('term.buyers.termbuyerpostcounteroffer',
								[
								'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
							    'fromLocation' => $buyerOfferDetails['fromLocation'],
								'toLocation' => $buyerOfferDetails['toLocation'],
								'deliveryDate' => $buyerOfferDetails['deliveryDate'],
								'dispatchDate' => $buyerOfferDetails['dispatchDate'],
								'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
								'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
								'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
								'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
								'packagingType' =>  $buyerOfferDetails['packagingType'],
								'countCartItems' =>  $buyerOfferDetails['countCartItems'],
								'countview' =>  $buyerOfferDetails['countview'],
                                                                'buyerQuoteId' => $buyerQuoteItemId,
								'serviceId' => $serviceId,
								'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                                                'allMessagesList' =>  $allMessagesList,
                                                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
								
						]
						);
						break;
						
						
						
						case COURIER  :
							$allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
							$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
							
							return view('term.buyers.termbuyerpostcounteroffer',
									[
									'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
									'fromLocation' => $buyerOfferDetails['fromLocation'],
									'toLocation' => $buyerOfferDetails['toLocation'],
									'deliveryDate' => $buyerOfferDetails['deliveryDate'],
									'dispatchDate' => $buyerOfferDetails['dispatchDate'],
									'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
									'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
									'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
									'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
									'packagingType' =>  $buyerOfferDetails['packagingType'],
									'countCartItems' =>  $buyerOfferDetails['countCartItems'],
									'countview' =>  $buyerOfferDetails['countview'],
									'buyerQuoteId' => $buyerQuoteItemId,
									'serviceId' => $serviceId,
									'quotesCount' =>$buyerOfferDetails['quotesCount'],
									'allMessagesList' =>  $allMessagesList,
									'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
								
									]
							);
							break;
							
					case RAIL :
					case AIR_DOMESTIC :
                                                $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
						$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
					
							
						return view('term.buyers.termbuyerpostcounteroffer',
								[
								'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                                                'fromLocation' => $buyerOfferDetails['fromLocation'],
								'toLocation' => $buyerOfferDetails['toLocation'],
								'deliveryDate' => $buyerOfferDetails['deliveryDate'],
								'dispatchDate' => $buyerOfferDetails['dispatchDate'],
								'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
								'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
								'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
								'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
								'packagingType' =>  $buyerOfferDetails['packagingType'],
								'countCartItems' =>  $buyerOfferDetails['countCartItems'],
								'countview' =>  $buyerOfferDetails['countview'],
								'buyerQuoteId' => $buyerQuoteItemId,
								'serviceId' => $serviceId,
								'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                                                'allMessagesList' =>  $allMessagesList,
                                                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
							    
						]
						);
						break;
					case AIR_DOMESTIC :
                                                $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
						$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
							
						return view('term.buyers.termbuyerpostcounteroffer',
								[
								'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                                                'fromLocation' => $buyerOfferDetails['fromLocation'],
								'toLocation' => $buyerOfferDetails['toLocation'],
								'deliveryDate' => $buyerOfferDetails['deliveryDate'],
								'dispatchDate' => $buyerOfferDetails['dispatchDate'],
								'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
								'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
								'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
								'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
								'packagingType' =>  $buyerOfferDetails['packagingType'],
								'countCartItems' =>  $buyerOfferDetails['countCartItems'],
								'countview' =>  $buyerOfferDetails['countview'],
								'buyerQuoteId' => $buyerQuoteItemId,
								'serviceId' => $serviceId,
								'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                                                'allMessagesList' =>  $allMessagesList,
                                                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
								
									]
								);
						break;

					case AIR_INTERNATIONAL :
                                                $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
						$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
						
							
						return view('term.buyers.termbuyerpostcounteroffer',
								[
								'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                                                'fromLocation' => $buyerOfferDetails['fromLocation'],
                                                                'toLocation' => $buyerOfferDetails['toLocation'],
								'deliveryDate' => $buyerOfferDetails['deliveryDate'],
								'dispatchDate' => $buyerOfferDetails['dispatchDate'],
								'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
								'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
								'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
								'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
								'packagingType' =>  $buyerOfferDetails['packagingType'],
								'countCartItems' =>  $buyerOfferDetails['countCartItems'],
								'countview' =>  $buyerOfferDetails['countview'],
								'buyerQuoteId' => $buyerQuoteItemId,
								'serviceId' => $serviceId,
								'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                                                'allMessagesList' =>  $allMessagesList,
                                                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
								
						]
						);
						break;
					case OCEAN :
                        $allMessagesList = MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$buyerQuoteItemId,1);
						$buyerOfferDetails = TermBuyerComponent::getPostBuyerCounterOfferForTerm($buyerQuoteItemId,$serviceId, $roleId);
					
						return view('term.buyers.termbuyerpostcounteroffer',
								[
								'arrayBuyerCounterOffer' => $buyerOfferDetails['arrayBuyerCounterOffer'],
                                                                'fromLocation' => $buyerOfferDetails['fromLocation'],
								'toLocation' => $buyerOfferDetails['toLocation'],
								'deliveryDate' => $buyerOfferDetails['deliveryDate'],
								'dispatchDate' => $buyerOfferDetails['dispatchDate'],
								'arrayBuyerQuoteSellersQuotesPrices' => TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($buyerQuoteItemId,$serviceId),
								'countBuyerLeads' => $buyerOfferDetails['countBuyerLeads'],
								'sourceLocation' =>  $buyerOfferDetails['sourceLocation'],
								'destinationLocation' =>  $buyerOfferDetails['destinationLocation'],
								'packagingType' =>  $buyerOfferDetails['packagingType'],
								'countCartItems' =>  $buyerOfferDetails['countCartItems'],
								'countview' =>  $buyerOfferDetails['countview'],
								'buyerQuoteId' => $buyerQuoteItemId,
								'serviceId' => $serviceId,
								'quotesCount' =>$buyerOfferDetails['quotesCount'],
                                                                'privateSellerNames' => $buyerOfferDetails['privateSellerNames'],
                                                                'allMessagesList' =>  $allMessagesList      
								
						]
						);
						break;
					case ROAD_INTRACITY :break;
		    		case ROAD_TRUCK_HAUL: break;
					default :break;
			}
							//rendering the view with the data grid
			} catch (Exception $e) {
		
			}

	}
	

/*
 * Edit buyer bid closing date for conform posts
 */
	public function BidDateEditForm($quoteId) {
		try {
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			/*Switch cases for term edit post bid dates
			*
			*/
			
			Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
			$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
			$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
			$bidEndDates = TermBuyerComponent::getBidDatesData($serviceId,$quoteId);			
			
			
			return view('term.buyers.term_edit_bid_date',array('serviceId' => $serviceId,
																'quoteId'=>$quoteId,
																'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
																'termQuotes'=>$termQuotes,
																'bidEndDates'=>$bidEndDates,
																
			));
			
	
			} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
		
		/*
		 * Edit buyer bid closing date for confirm posts
		 */
		public function UpdateBidDate() {
			try {
				
				if(!empty(Input::all()))  {
					$allRequestdata=Input::all();
				}
				/*Switch cases for term edit post bid dates
				*
				*/	
						Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
						$updateBidDate = TermBuyerComponent::updateDatesData($allRequestdata);
						if ($updateBidDate == 1) {
							return redirect('/gettermbuyercounteroffer/'.$allRequestdata['quoteid'])->with('updatebid', 'Buyer bid  date and time updated successfully.');
						}						
						
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}	
	
	/*
	 * Edit buyer bid draft edit form for conform posts
	 */
	public function BidEditDraftForm($quoteId) {
		try {
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			/*Switch cases for term draft edit post 
				*
				*/
			switch($serviceId){
				case ROAD_FTL       :
					Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
					$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
					$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
					$loadtypemasters = CommonComponent::getAllLoadTypes ();
					$vehicletypemasters = CommonComponent::getAllVehicleType();
					$bid_type = CommonComponent::getAllBidTypes();
					$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
					$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
					$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
					
					return view('term.buyers.term_edit_draft',array(
                                            'serviceId' => $serviceId,
                                            'quoteId'=>$quoteId,
                                            'bid_type'=>$bid_type,
                                            'load_type'=>$loadtypemasters,
                                            'vehicle_type'=>$vehicletypemasters,
                                            'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
                                            'bidEndDates'=>$bidEndDates,
                                            'termQuotes'=>$termQuotes,
                                            'termConditionFiles'=>$termConditionFiles,
                                            'privateSellerNames'=>$privateSellerNames
					));
					break;
				case ROAD_PTL       :
				case RAIL:
				case AIR_DOMESTIC:
				case AIR_INTERNATIONAL:
				case OCEAN:
					Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
					$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
					
					$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
					$loadtypemasters = CommonComponent::getAllLoadTypes ();
					$vehicletypemasters = CommonComponent::getAllVehicleType();
					$packagetypes = CommonComponent::getAllPackageTypes();
					$bid_type = CommonComponent::getAllBidTypes();
					$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
					$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
					$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
					$unitsWeightTypes = CommonComponent::getUnitsWeight();
					$senderIdentity = CommonComponent::getSenderIdentity();
					$shipmentTypes = CommonComponent::getShipmentTypes();
			
					return view('term.buyers.term_edit_draft',array(
						'serviceId' => $serviceId,
						'quoteId'=>$quoteId,
						'bid_type'=>$bid_type,
						'loadTypes'=>$loadtypemasters,
						'vehicle_type'=>$vehicletypemasters,
						'packageTypes'=>$packagetypes,
						'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
						'bidEndDates'=>$bidEndDates,
						'termQuotes'=>$termQuotes,
						'termConditionFiles'=>$termConditionFiles,
						'privateSellerNames'=>$privateSellerNames,
						'unitsWeightTypes' => $unitsWeightTypes,
						'senderIdentity' => $senderIdentity,
						'shipmentTypes' => $shipmentTypes,
					));
					break;
					
					
					case COURIER:
						Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
						$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
						
						$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
						$loadtypemasters = CommonComponent::getAllLoadTypes ();
						$vehicletypemasters = CommonComponent::getAllVehicleType();
						$packagetypes = CommonComponent::getAllPackageTypes();
						$bid_type = CommonComponent::getAllBidTypes();
						$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
						$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
						$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
						$unitsWeightTypes = CommonComponent::getUnitsWeight();
						$volumeWeightTypes = CommonComponent::getUnitsWeight ();
						$senderIdentity = CommonComponent::getSenderIdentity();
						$shipmentTypes = CommonComponent::getShipmentTypes();
						$pricelabs = CommonComponent::getSlabs($quoteId,Auth::User ()->id);
						$pricelabs_count = count($pricelabs);
					
						
						return view('term.buyers.term_edit_draft',array(
								'serviceId' => $serviceId,
								'quoteId'=>$quoteId,
								'bid_type'=>$bid_type,
								'loadTypes'=>$loadtypemasters,
								'vehicle_type'=>$vehicletypemasters,
								'packageTypes'=>$packagetypes,
								'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
								'bidEndDates'=>$bidEndDates,
								'termQuotes'=>$termQuotes,
								'termConditionFiles'=>$termConditionFiles,
								'privateSellerNames'=>$privateSellerNames,
								'volumeWeightTypes'=>$volumeWeightTypes,
								'pricelabs' =>	$pricelabs,
								'pricelabs_count' => $pricelabs_count,
								'unitsWeightTypes' => $unitsWeightTypes,
								'senderIdentity' => $senderIdentity,
								'shipmentTypes' => $shipmentTypes,
						));
						break;
				case RELOCATION_DOMESTIC       :
					Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
					$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
					$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
					$loadtypemasters = CommonComponent::getAllLoadTypes ();
					$vehicletypemasters = CommonComponent::getAllVehicleType();
					$bid_type = CommonComponent::getAllBidTypes();
					$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
					$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
					$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
					$vehicletypecategories = CommonComponent::getAllVehicleCategories();
					$vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();
				
					return view('term.buyers.term_edit_draft',array(
						'serviceId' => $serviceId,
						'quoteId'=>$quoteId,
						'bid_type'=>$bid_type,
						'load_type'=>$loadtypemasters,
						'vehicle_type'=>$vehicletypemasters,
						'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
						'bidEndDates'=>$bidEndDates,
						'termQuotes'=>$termQuotes,
						'termConditionFiles'=>$termConditionFiles,
						'privateSellerNames'=>$privateSellerNames,
						'vehicletypecategories' => $vehicletypecategories,
						'vehicletypecategorietypes' => $vehicletypecategorietypes
					));
					break;
				case RELOCATION_INTERNATIONAL       :
					Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
					$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
					$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
					$loadtypemasters = CommonComponent::getAllLoadTypes ();
					$vehicletypemasters = CommonComponent::getAllVehicleType();
					$bid_type = CommonComponent::getAllBidTypes();
					$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
					$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
					$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
					$vehicletypecategories = CommonComponent::getAllVehicleCategories();
					$vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();

					return view('term.buyers.term_edit_draft',array(
						'serviceId' => $serviceId,
						'quoteId'=>$quoteId,
						'bid_type'=>$bid_type,
						'load_type'=>$loadtypemasters,
						'vehicle_type'=>$vehicletypemasters,
						'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
						'bidEndDates'=>$bidEndDates,
						'termQuotes'=>$termQuotes,
						'termConditionFiles'=>$termConditionFiles,
						'privateSellerNames'=>$privateSellerNames,
						'vehicletypecategories' => $vehicletypecategories,
						'vehicletypecategorietypes' => $vehicletypecategorietypes
					));
					break;
				case RELOCATION_GLOBAL_MOBILITY       :
					Log::info('Update buyer draft bid quote: ' . Auth::id(), array('c' => '1'));
					$getBuyerTermQuotesdata = TermBuyerComponent::getBuyerQuotesTermdata($serviceId,$quoteId);
					$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
					$bid_type = CommonComponent::getAllBidTypes();
					$bidEndDates = TermBuyerComponent::getLastUpdatedBidDatesData($serviceId,$quoteId);
					$termConditionFiles = TermBuyerComponent::getTermFiles($serviceId,$quoteId);
					$privateSellerNames = TermBuyerComponent::getTermPrivateSellerNames($quoteId);
					$lkp_relgm_services = CommonComponent::getLkpRelocationGMServices();
					return view('term.buyers.term_edit_draft',array(
						'serviceId' => $serviceId,
						'quoteId'=>$quoteId,
						'bid_type'=>$bid_type,
						'getBuyerTermQuotesdata'=>$getBuyerTermQuotesdata,
						'bidEndDates'=>$bidEndDates,
						'termQuotes'=>$termQuotes,
						'termConditionFiles'=>$termConditionFiles,
						'privateSellerNames'=>$privateSellerNames,
						'lkp_relgm_services' => $lkp_relgm_services
					));
					break;
			}
	
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}


	public function UpdateTermPost(Request $request, $id, $lineitem = null) {

		try {
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			/*Switch cases for term draft edit post
				*
				*/
			switch($serviceId){
				case ROAD_FTL:
				case ROAD_PTL:
				case RAIL:
				case AIR_DOMESTIC:
				case AIR_INTERNATIONAL:
				case OCEAN:
				case COURIER:
				case RELOCATION_DOMESTIC:
				case RELOCATION_INTERNATIONAL:
				case RELOCATION_GLOBAL_MOBILITY:
					if (! empty ( Input::all () )) {
					
						if (!empty($_POST['confirm_but']) && isset($_POST['confirm_but'])) {
							$postStatus= OPEN;
						} else {
							$postStatus= SAVEDASDRAFT;
						}

						//buyer quote
						
						//for All services
						$fromcities = array();
						
						$buyerQuote = new TermBuyerQuote();

						
						
						if(Session::get('service_id') == COURIER){
						
							if(isset($_POST ['check_max_weight_assign']) && $request->input ( 'check_max_weight_assign' ) == 1){
								$check_max_weight = 1;
							}else{
								$check_max_weight = 0;
							}
						
							if(DB::table('term_buyer_quote_slabs')->where('buyer_quote_id',$id)->delete()){
								$low_price=1;
								$high_price=1;
								for($i=1;$i<=$request->price_slap_hidden_value;$i++){
						
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
						
									$buyerpost_lineitem_slab->buyer_quote_id = $id;
									$buyerpost_lineitem_slab->buyer_id = Auth::id ();
									$created_at = date ( 'Y-m-d H:i:s' );
									$createdIp = $_SERVER ['REMOTE_ADDR'];
									$buyerpost_lineitem_slab->created_by = Auth::id ();
									$buyerpost_lineitem_slab->created_at = $created_at;
									$buyerpost_lineitem_slab->created_ip = $createdIp;
									$buyerpost_lineitem_slab->save ();
						
								}
							}
							
							$arr = array (
									'from_date' => CommonComponent::convertDateForDatabase($request->input ( 'dispatch_date' )),
									'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'delivery_date' )),
									'lkp_post_status_id' => $postStatus,
									'max_weight_accepted' => $_POST['max_weight_accepted_text'],
									'lkp_ict_weight_uom_id' => $_POST['units_max_weight'],
									'is_incremental' => $check_max_weight,
									'increment_weight' => $_POST['incremental_weight_text'],
									'buyer_notes' => $_POST['buyer_notes'],
									'lkp_quote_access_id' => $_POST['quoteaccess_id'],
							);
							$buyerQuote::where ( "id", $id )->update ($arr);
							
							
						}else{
						
						$arr = array (
							'from_date' => CommonComponent::convertDateForDatabase($request->input ( 'dispatch_date' )),
							'to_date' => CommonComponent::convertDateForDatabase($request->input ( 'delivery_date' )),
							'lkp_post_status_id' => $postStatus,
							//'lkp_bid_type_id' => $_POST['bid_type'],
							'buyer_notes' => $_POST['buyer_notes'],
							'lkp_quote_access_id' => $_POST['quoteaccess_id'],
							'is_door_pickup' => isset($_POST['is_door_pickup']) ? $_POST['is_door_pickup'] : "",
							'is_door_delivery' => isset($_POST['is_door_delivery']) ? $_POST['is_door_delivery'] : "",
						);
						$buyerQuote::where ( "id", $id )->update ($arr);
						}
						//buyer quote item
						for($i = 0; $i < count($_POST['post_id']); $i++) {
							$Quote_Lineitems = new TermBuyerQuoteItem();
							
							
							$fromcities[] = $_POST['from_location'][$i];
							
							$Quote_Lineitems::where("id", $_POST['post_id'][$i])->update(array(
								'from_location_id' => isset($_POST['from_location'][$i]) ? $_POST['from_location'][$i] : "",
								'to_location_id' => isset($_POST['to_location'][$i]) ? $_POST['to_location'][$i] : "",
								'lkp_load_type_id' => isset($_POST['load_type'][$i]) ? $_POST['load_type'][$i] : "",
								'lkp_vehicle_type_id' => isset($_POST['vechile_type'][$i]) ? $_POST['vechile_type'][$i] : "",
								'lkp_packaging_type_id' => isset($_POST['package_type'][$i]) ? $_POST['package_type'][$i] : "",
								'units' => isset($_POST['capacity'][$i]) ? $_POST['capacity'][$i] : "" ,
								'quantity' => isset($_POST['quantity'][$i]) ? $_POST['quantity'][$i] : "",
								'number_packages' => isset($_POST['number_packages'][$i]) ? $_POST['number_packages'][$i] : "",
								'volume' => isset($_POST['volume'][$i]) ? $_POST['volume'][$i] : "",
								'product_made' => isset($_POST['product_made'][$i]) ? $_POST['product_made'][$i] : "",
								'ie_code' => isset($_POST['ie_code'][$i]) ? $_POST['ie_code'][$i] : "",
								'lkp_air_ocean_shipment_type_id' => isset($_POST['lkp_air_ocean_shipment_type_id'][$i]) ? $_POST['lkp_air_ocean_shipment_type_id'][$i] : "",
								'lkp_air_ocean_sender_identity_id' => isset($_POST['lkp_air_ocean_sender_identity_id'][$i]) ? $_POST['lkp_air_ocean_sender_identity_id'][$i] : "",
								'lkp_vehicle_category_id' => isset($_POST['lkp_vehicle_category_id'][$i]) ? $_POST['lkp_vehicle_category_id'][$i] : "",
								'lkp_vehicle_category_type_id' => isset($_POST['lkp_vehicle_category_type_id'][$i]) ? $_POST['lkp_vehicle_category_type_id'][$i] : "",
								'vehicle_model' => isset($_POST['vehicle_model'][$i]) ? $_POST['vehicle_model'][$i] : "",
								'no_of_vehicles' => isset($_POST['no_of_vehicles'][$i]) ? $_POST['no_of_vehicles'][$i] : "",
								'number_loads' => isset($_POST['number_loads'][$i]) ? $_POST['number_loads'][$i] : "",
								'avg_kg_per_move' => isset($_POST['avg_kg_per_move'][$i]) ? $_POST['avg_kg_per_move'][$i] : "",
								'lkp_gm_service_id' => isset($_POST['service_ids'][$i]) ? $_POST['service_ids'][$i] : "",
								'measurement' => isset($_POST['measurements'][$i]) ? $_POST['measurements'][$i] : "",
								'measurement_units' => isset($_POST['measurement_units'][$i]) ? $_POST['measurement_units'][$i] : "",
							));
						}

						//buyer bid
						$created_at = date('Y-m-d H:i:s');
						$createdIp = $_SERVER ['REMOTE_ADDR'];
						$buyerBidDate = new TermBuyerBidDate();
						$arr = array (
							'bid_end_date' => CommonComponent::convertDateForDatabase($request->input ( 'dispatch_date' )),
							'bid_end_time' => $_POST['bid_close_time'],
						);
						$buyerBidDate::where ( "term_buyer_quote_id", $id )->update ($arr);
						//private sellers
						if(isset($_POST['quoteaccess_id']) && $_POST['quoteaccess_id'] == 2){
							if (isset($_POST['term_seller_list']) && $_POST['term_seller_list'] != "") {
								DB::table('term_buyer_quote_selected_sellers')->where('term_buyer_quote_id', $id)->delete();
								$seller_list = explode(",", $_POST['term_seller_list']);
								$seller_list_count = count($seller_list);
								
								for ($i = 0; $i < $seller_list_count; $i ++) {
									$Quote_seller_list = new TermBuyerQuoteSelectedSeller();
									$Quote_seller_list->term_buyer_quote_id = $id;
									$Quote_seller_list->seller_id = $seller_list[$i];
									$Quote_seller_list->lkp_service_id = $serviceId;
									$Quote_seller_list->created_by = Auth::id();
									$Quote_seller_list->created_at = $created_at;
									$Quote_seller_list->created_ip = $createdIp;
									$Quote_seller_list->save();
									
									//Maintaining a log of data for buyer new seller data multiple  creation
									CommonComponent::auditLog($id, 'buyer_quote_selected_sellers');
									
									
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
											case RELOCATION_DOMESTIC :
												$servicename = 'RELOCATION TERM';
												break;
											default :
												$servicename = 'LTL TERM';
												break;
										
										}
										
										$transaction_term = DB::table('term_buyer_quotes')->where('id','=',$id)->pluck('transaction_id');
										
										//*******Send Sms to the private Sellers***********************//
										$msg_params = array(
												'randnumber' => $transaction_term,
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
									case RELOCATION_DOMESTIC :
										$servicename = 'RELOCATION TERM';
										break;
									default :
										$servicename = 'LTL TERM';
										break;
							
								}
								
								$transaction_term = DB::table('term_buyer_quotes')->where('id','=',$id)->pluck('transaction_id');
								//*******Send Sms to the private Sellers***********************//
								$msg_params = array(
										'randnumber' => $transaction_term,
										'buyername' => Auth::User()->username,
										'servicename' => $servicename
								);
								
								if($serviceId == ROAD_FTL){
									$getSellerIds  =   CommonComponent::getTermSellerList($fromcities);
								}else{
									$getSellerIds  =   CommonComponent::getAllSellerList($fromcities);
								}
								
								for($i=0;$i<count($getSellerIds);$i++){
									$getMobileNumber  =   CommonComponent::getMobleNumber($getSellerIds[$i]['id']);
									if($getMobileNumber)
										CommonComponent::sendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
								}
								//*******Send Sms to the private Sellers***********************//
							
							}
						}
						//bid documents
						if(count($_FILES)>0){
							//Files save data in uploads docs
							$target_dir = 'uploads/buyer/'.Auth::id().'/Terms/'.$id."/" ;
							if (!is_dir ( $target_dir )) {
								mkdir ( $target_dir, 0777, true );
							}
							$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_defualt"]["name"]);
							$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
							$_FILES["terms_condtion_types_term_defualt"]["size"];
							$_FILES["terms_condtion_types_term_defualt"]["name"];
							move_uploaded_file($_FILES["terms_condtion_types_term_defualt"]["tmp_name"], $target_file);

							$created_at = date('Y-m-d H:i:s');
							$createdIp = $_SERVER ['REMOTE_ADDR'];
							$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
							$buyerBidTermFiles->term_buyer_quote_id = $id;
							$buyerBidTermFiles->file_name = $_FILES["terms_condtion_types_term_defualt"]["name"];
							$buyerBidTermFiles->file_type = $imageFileType;
							$buyerBidTermFiles->file_size = $_FILES["terms_condtion_types_term_defualt"]["size"];
							$buyerBidTermFiles->file_path = $target_file;
							$buyerBidTermFiles->lkp_service_id = $serviceId;
							$buyerBidTermFiles->created_by = Auth::id();
							$buyerBidTermFiles->created_at = $created_at;
							$buyerBidTermFiles->created_ip = $createdIp;
							$buyerBidTermFiles->save();


							//Docuements uploads
							$j =1;

							for($j=1;$j<=$_POST['term_next_terms_count_search'];$j++){
								if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] == '') {
									$j++;
								}
								if (isset ( $_FILES['terms_condtion_types_term_'.$j] ) && $_FILES['terms_condtion_types_term_'.$j] != '') {
									$target_file = $target_dir . basename($_FILES["terms_condtion_types_term_$j"]["name"]);
									$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
									move_uploaded_file($_FILES["terms_condtion_types_term_$j"]["tmp_name"], $target_file);
									$buyerBidTermFiles = new TermBuyerQuoteBidTermsFile();
									$buyerBidTermFiles->term_buyer_quote_id = $id;
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
						//making zip file start
						$documents = DB::table('term_buyer_quote_bid_terms_files')
							->where('term_buyer_quote_id', $id)
							->where('file_name', "!=","")
							->select('file_name', 'file_path')
							->get();

					if(!empty($documents)){
						if($documents[0]->file_name!=''){
							$files = array();
							foreach($documents as $document){
								$files[] = $document->file_path;
							}
							$zippath = (isset($files['0'])) ? explode("/",$files['0']) : array();

							array_pop($zippath);
							$zippath = implode("/",$zippath);
							if(file_exists($zippath."/biddocuments.zip")){
								unlink($zippath."/biddocuments.zip");
							}



							if(file_exists($zippath)) {
								$zipname = $zippath . '/biddocuments.zip';
								$zip = new ZipArchive;
								$zip->open($zipname, ZipArchive::CREATE);
								foreach ($files as $file) {
									$zip->addFile($file);
								}
							}
						}
					}

					$transactionId = DB::table('term_buyer_quotes')->where('id','=',$id)->pluck('transaction_id');
					$multi_data_count = count($_REQUEST['post_id']);
					$postType = 2;

					return redirect('/termdraftedit/'.$id)->with('transactionId', $transactionId)->with('postsCount',$multi_data_count)->with('postType',$postType);
					


					}
					break;
			}

		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

	}

        
        public function UpdateTermPostSeller($id) {
            
            //echo "<pre>"; print_r($_REQUEST); die;
            if(Session::get ( 'service_id' ) != ''){
                 $serviceId = Session::get ( 'service_id' );
            }
            $created_at = date('Y-m-d H:i:s');
	    $createdIp = $_SERVER ['REMOTE_ADDR'];
            if(isset($_POST['quoteaccess_id']) && $_POST['quoteaccess_id'] == 2){
                        if (isset($_POST['term_seller_list']) && $_POST['term_seller_list'] != "") {
                                DB::table('term_buyer_quote_selected_sellers')->where('term_buyer_quote_id', $id)->delete();
                                $seller_list = explode(",", $_POST['term_seller_list']);
                                $seller_list_count = count($seller_list);
                                
                                for ($i = 0; $i < $seller_list_count; $i ++) {
                                        $Quote_seller_list = new TermBuyerQuoteSelectedSeller();
                                        $Quote_seller_list->term_buyer_quote_id = $id;
                                        $Quote_seller_list->seller_id = $seller_list[$i];
                                        $Quote_seller_list->lkp_service_id = $serviceId;
                                        $Quote_seller_list->created_by = Auth::id();
                                        $Quote_seller_list->created_at = $created_at;
                                        $Quote_seller_list->created_ip = $createdIp;
                                        $Quote_seller_list->save();
                                        
                                        //Maintaining a log of data for buyer new seller data multiple  creation
                                        CommonComponent::auditLog($id, 'buyer_quote_selected_sellers');



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
                                                        case RELOCATION_DOMESTIC :
                                                                $servicename = 'RELOCATION TERM';
                                                                break;
                                                        case RELOCATION_INTERNATIONAL :
                                                                $servicename = 'RELOCATION INTERNATIONAL';
                                                                break;
                                                        default :
                                                                $servicename = 'LTL TERM';
                                                                break;

                                                }

                                                $transaction_term = DB::table('term_buyer_quotes')->where('id','=',$id)->pluck('transaction_id');

                                                //*******Send Sms to the private Sellers***********************//
                                                $msg_params = array(
                                                                'randnumber' => $transaction_term,
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
                
                return redirect('/gettermbuyercounteroffer/'.$id)->with('sumsg', 'Seller updated successfully.');
            
        }
        
        
		
  /*
   * Generate Seller Contract
   */		
	public function generateContractBuyer(){
		try {
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			if(!empty(Input::all()))  {
				$allRequestdata=Input::all();
			}			
			switch($serviceId){
				case ROAD_FTL       :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :	
                    case RELOCATION_GLOBAL_MOBILITY :	
					Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
					break;
				case COURIER       :
					Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContractCourier($allRequestdata,$serviceId);
					break;
					
				case ROAD_PTL       :
						Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
						break;
						
				case RAIL       :
							Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
							break;
			    case AIR_DOMESTIC       :
								Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
								break;
				case AIR_INTERNATIONAL       :
									Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
									break;
			   case OCEAN       :
								 Log::info('Update buyer bid date quote: ' . Auth::id(), array('c' => '1'));
					$contract_no    =   TermBuyerComponent::generateSellerContract($allRequestdata,$serviceId);
								break;

							
			}
            echo $contract_no;
		
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}		
	}	
	/**
	 * get buyer orders in term page
	 * Cancel term quote
	 * @param integer $buyerQuoteId
	 * @return type
	 */
	public function cancelBuyerTerm($buyerContractId) {
		Log::info('Cancel the term quote: ' . Auth::id(), array('c' => '1'));
		try {
			
			$serviceId = Session::get('service_id');				
			//Loading respective service data grid	
			$query = DB::table('term_contracts as tc');
			 $query->where('tc.id',$buyerContractId);
			 $query->select('tc.contract_status');
			 $contractStatusDetails   =   $query->get();
			 	
			 if($contractStatusDetails[0]->contract_status==11){
			 echo 2;
			 }else{
			TermBuyerComponent::cancelBuyerTermQuote($buyerContractId, $serviceId);
			echo 1;
			 }
								
		} catch (Exception $e) {
	
		}
	}
	/**
	 * get buyer orders details in term page
	 * 
	 * @param integer $buyerQuoteId
	 * @return type
	 */
	public function showBuyerContractDetails($buyerContractId) {
		Log::info('buyer has viewed Contract Details page:' . Auth::User ()->id, array('c' => '1'));
		if (isset($buyerContractId) && ($buyerContractId > 0)) {
			$serviceId = Session::get('service_id');
			$querynum = DB::table('term_contracts as tc');
			$querynum->where('tc.id',$buyerContractId);
			$querynum->select('tc.contract_no');
			$contractNumber   =   $querynum->get();
			
			$query = DB::table('term_contracts as tc');
			$query->leftJoin('term_buyer_quotes as tbq', 'tc.term_buyer_quote_id', '=', 'tbq.id');
			$query->leftjoin('term_buyer_quote_items as tbqi', 'tc.term_buyer_quote_item_id', '=', 'tbqi.id');
	
			switch ($serviceId) {
				case ROAD_FTL :
				case ROAD_INTRACITY :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :
					$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
					$query->leftJoin('lkp_cities as lcity', 'tbqi.to_location_id', '=', 'lcity.id');
					break;
				case ROAD_PTL :
				case RAIL :
				case AIR_DOMESTIC :
					$query->leftJoin('lkp_ptl_pincodes as lc', 'lc.id', '=', 'tbqi.from_location_id');
					$query->leftJoin('lkp_ptl_pincodes as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
					break;
				case COURIER :
					$query->leftJoin('lkp_ptl_pincodes as lc', 'lc.id', '=', 'tbqi.from_location_id');
					
					
					
					$query->leftjoin('lkp_ptl_pincodes as lcity', function($join)
					{
						$join->on('tbqi.to_location_id', '=', 'lcity.id');
						$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
					
					});
					$query->leftjoin('lkp_countries as lppt', function($join)
					{
						$join->on('tbqi.to_location_id', '=', 'lppt.id');
						$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
					
					});
					
					
					break;
				case AIR_INTERNATIONAL :
					$query->leftJoin('lkp_airports as lc', 'lc.id', '=', 'tbqi.from_location_id');
					$query->leftJoin('lkp_airports as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
					break;
				case OCEAN :
					$query->leftJoin('lkp_seaports as lc', 'lc.id', '=', 'tbqi.from_location_id');
					$query->leftJoin('lkp_seaports as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
					break;
				case RELOCATION_GLOBAL_MOBILITY :
					$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
					break;
				default :
					$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
					$query->leftJoin('lkp_cities as lcity', 'tbqi.to_location_id', '=', 'lcity.id');
					break;
			}
			
			
			$query->leftJoin('lkp_order_statuses as os', 'tc.contract_status', '=', 'os.id')			
			->leftJoin('lkp_vehicle_types as lvt', 'tbqi.lkp_vehicle_type_id', '=', 'lvt.id')
			->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'tbqi.lkp_load_type_id')
			->leftjoin('users as u', 'u.id', '=', 'tc.seller_id')
			->where('tc.created_by', '=', Auth::User ()->id)			
			->where('tc.lkp_service_id',$serviceId)
			->where('tc.contract_no',$contractNumber[0]->contract_no)
			->groupby('tc.term_buyer_quote_item_id');
			switch ($serviceId) {
				case ROAD_FTL :
				case ROAD_INTRACITY :
				case RELOCATION_DOMESTIC :
				case RELOCATION_INTERNATIONAL :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','tbq.lkp_bid_type_id','tbqi.quantity','lvt.units','tbq.from_date','tbq.to_date','lvt.capacity',
					'tbqi.lkp_vehicle_category_id','tbqi.lkp_vehicle_category_type_id','tbqi.vehicle_model','tbqi.no_of_vehicles','tbqi.number_loads','tbqi.avg_kg_per_move');
					break;
				case ROAD_PTL :
				case RAIL :
				case AIR_DOMESTIC :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from','lcity.postoffice_name as to','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date', 'tbqi.volume');
					break;
					
				case COURIER :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from',
					DB::raw("(case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcity.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lppt.country_name end) as 'to'"),
					'tbq.lkp_bid_type_id','tbq.lkp_courier_type_id','tbqi.volume','tbqi.number_packages','tbq.from_date','tbq.to_date', 'tbqi.volume');
					break;
					
				case AIR_INTERNATIONAL :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.airport_name as from','lcity.airport_name as to','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date');
					break;
				case OCEAN :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.seaport_name as from','lcity.seaport_name as to','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date');
					break;
				case RELOCATION_GLOBAL_MOBILITY :
						$query->select('tc.*','u.username','os.order_status','lc.city_name as from','tbq.lkp_bid_type_id','tbq.from_date','tbq.to_date','tbqi.lkp_gm_service_id','tbqi.measurement','tbqi.measurement_units');
						break;
				default :
					$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date');
					break;
			}
	
			$contractDetails   =   $query->get();
			//echo "<pre>";print_r($contractDetails);exit;
			$volumeWeightTypes = CommonComponent::getVolumeWeightTypes();
            $unitsWeightTypes = CommonComponent::getUnitsWeight();
			
			if($serviceId==RELOCATION_DOMESTIC){
				
				
				Session::forget('masterBedRoom');
				Session::forget('masterBedRoom1');
				Session::forget('masterBedRoom2');
				Session::forget('masterBedRoom3');
				Session::forget('lobby','');
				Session::forget('kitchen');
				Session::forget('bathroom');
				Session::forget('living');
				
				$payment_methods = CommonComponent::getPaymentTerms ();
				$ratecardTypes = CommonComponent::getAllRatecardTypes();
				$propertyTypes = CommonComponent::getAllPropertyTypes();
				$loadTypes = CommonComponent::getAllLoadCategories();
				$roomTypes = CommonComponent::getAllRoomTypes();
				$vehicletypecategories = CommonComponent::getAllVehicleCategories();
				$vehicletypecategorietypes = CommonComponent::getAllVehicleCategoryTypes();
				
			return view('term.buyers.buyer_contract_details', array(
						'contractDetails' => $contractDetails,
						'serviceId'=>$serviceId,
						'volumeWeightTypes'=>$volumeWeightTypes,
						'unitsWeightTypes'=>$unitsWeightTypes,
						'paymentterms' => $payment_methods,
						'ratecardtypes' => $ratecardTypes,
						'property_types' => $propertyTypes,
						'load_types' => $loadTypes,
						'room_types' =>$roomTypes,
						'vehicletypecategories' => $vehicletypecategories,
						'vehicletypecategorietypes' => $vehicletypecategorietypes
				));
				
			}elseif($serviceId==RELOCATION_INTERNATIONAL){
				
				$payment_methods = CommonComponent::getPaymentTerms ();
				$cartons    =   CommonComponent::getCartons();
				$propertyTypes = CommonComponent::getAllPropertyTypes();
				$roomTypes = CommonComponent::getAllRoomTypes();
				
				return view('term.buyers.buyer_contract_details', array(
						'contractDetails' => $contractDetails,
						'serviceId'=>$serviceId,
						'paymentterms' => $payment_methods,
						'cartons' => $cartons,
						'property_types' => $propertyTypes,
						'room_types' =>$roomTypes
						
						
				));
			}
			else{
			if($serviceId==COURIER){	
					$CourierTypes = CommonComponent::getAllCourierPorposeTypes();
					$getQuoteAddtionalDetails = CommonComponent::getQuoteAddtionalDetails($contractDetails[0]->term_buyer_quote_id,$contractDetails[0]->seller_id);
					return view('term.buyers.buyer_contract_details', array(
							'contractDetails' => $contractDetails,	
							'CourierTypes'=>$CourierTypes,
							'serviceId'=>$serviceId,
							'getQuoteAddtionalDetails'=>$getQuoteAddtionalDetails,
							'volumeWeightTypes'=>$volumeWeightTypes,
		                    'unitsWeightTypes'=>$unitsWeightTypes
					));
			}else{
					return view('term.buyers.buyer_contract_details', array(
							'contractDetails' => $contractDetails,
								
							'serviceId'=>$serviceId,
							'volumeWeightTypes'=>$volumeWeightTypes,
							'unitsWeightTypes'=>$unitsWeightTypes
					));
			}
			
			
			}	
		} else {
		return view('orders.buyer_orders');
		}
	}
		
		/*
		 * Booknow form for term in all services controller
		 */
		public function TermBooknow() {
			try {
				if(Session::get ( 'service_id' ) != ''){
					$serviceId = Session::get ( 'service_id' );
				}


				/*Switch cases for term booknow pages**/
				$input = Input::all();
				if(count($input) == 0){
					return redirect('/home');
				}
				
				
				$query = DB::table('term_contracts as tc');
				$query->leftJoin('term_buyer_quotes as tbq', 'tc.term_buyer_quote_id', '=', 'tbq.id');
				$query->leftjoin('term_buyer_quote_items as tbqi', 'tc.term_buyer_quote_item_id', '=', 'tbqi.id');
		
				switch ($serviceId) {
					case ROAD_FTL :
					case ROAD_INTRACITY :
					case RELOCATION_DOMESTIC :
					case RELOCATION_INTERNATIONAL :
						$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
						$query->leftJoin('lkp_cities as lcity', 'tbqi.to_location_id', '=', 'lcity.id');
						break;
					case ROAD_PTL :
					case RAIL :
					case AIR_DOMESTIC :
						$query->leftJoin('lkp_ptl_pincodes as lc', 'lc.id', '=', 'tbqi.from_location_id');
						$query->leftJoin('lkp_ptl_pincodes as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
						break;

					case COURIER :
						
						$query->leftJoin('lkp_ptl_pincodes as lc', 'lc.id', '=', 'tbqi.from_location_id');
						$query->leftjoin('lkp_ptl_pincodes as lcity', function($join)
						{
							$join->on('tbqi.to_location_id', '=', 'lcity.id');
							$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(1));
								
						});
						$query->leftjoin('lkp_countries as lppt', function($join)
						{
							$join->on('tbqi.to_location_id', '=', 'lppt.id');
							$join->on(DB::raw('tbq.lkp_courier_delivery_type_id'),'=',DB::raw(2));
								
						});
						break;
					case AIR_INTERNATIONAL :
						$query->leftJoin('lkp_airports as lc', 'lc.id', '=', 'tbqi.from_location_id');
						$query->leftJoin('lkp_airports as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
						break;
					case OCEAN :
						$query->leftJoin('lkp_seaports as lc', 'lc.id', '=', 'tbqi.from_location_id');
						$query->leftJoin('lkp_seaports as lcity', 'lcity.id', '=', 'tbqi.to_location_id');
						break;
					case RELOCATION_GLOBAL_MOBILITY :
							$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
						break;
					default :
						$query->leftJoin('lkp_cities as lc', 'tbqi.from_location_id', '=', 'lc.id');
						$query->leftJoin('lkp_cities as lcity', 'tbqi.to_location_id', '=', 'lcity.id');
						break;
				}
				$query->leftJoin('lkp_order_statuses as os', 'tc.contract_status', '=', 'os.id')			
				->leftJoin('lkp_vehicle_types as lvt', 'tbqi.lkp_vehicle_type_id', '=', 'lvt.id')
				->leftjoin('lkp_load_types', 'lkp_load_types.id', '=', 'tbqi.lkp_load_type_id')
				->leftjoin('users as u', 'u.id', '=', 'tc.seller_id')
				->where('tc.created_by', '=', Auth::User ()->id)
				->where('tc.term_buyer_quote_id', '=', $input['quoteId'])
				->where('tc.id', '=', $input['contract_id'])
				->where('tc.lkp_service_id',$serviceId)
				->groupby('tc.term_buyer_quote_item_id');
				switch ($serviceId) {
					case ROAD_FTL :
					case ROAD_INTRACITY :
					case RELOCATION_DOMESTIC :
					case RELOCATION_INTERNATIONAL:
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','tbq.lkp_bid_type_id','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbqi.volume','tbqi.number_packages','tbq.from_date','tbq.to_date','tbqi.number_loads','tbqi.avg_kg_per_move','tbqi.lkp_load_type_id');
						break;
					case ROAD_PTL :
					case RAIL :
					case AIR_DOMESTIC :
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from','lcity.postoffice_name as to','tbq.lkp_bid_type_id','tbqi.volume','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbq.from_date','tbq.to_date', 'tbqi.volume','tbqi.lkp_load_type_id');
						break;
					case AIR_INTERNATIONAL :
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.airport_name as from','lcity.airport_name as to','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date','tbqi.lkp_load_type_id');
						break;
					case COURIER :
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.postoffice_name as from',
						DB::raw("(case when `tbq`.`lkp_courier_delivery_type_id` = 1 then lcity.postoffice_name  when `tbq`.`lkp_courier_delivery_type_id` = 2 then lppt.country_name end) as 'to'"),
						'tbq.lkp_bid_type_id','tbqi.volume','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbq.from_date','tbq.to_date', 'tbqi.volume','tbq.lkp_courier_type_id','tbq.lkp_courier_delivery_type_id');
						break;
					case OCEAN :
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.seaport_name as from','lcity.seaport_name as to','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date','tbqi.lkp_load_type_id');
						break;
					case RELOCATION_GLOBAL_MOBILITY:
							$query->select('tc.*','u.username','os.order_status','lc.city_name as from','tbq.lkp_bid_type_id','tbqi.lkp_gm_service_id','tbqi.from_location_id','tbqi.from_location_id','tbqi.to_location_id','tbqi.measurement','tbqi.measurement_units','tbq.from_date','tbq.to_date','tbqi.number_loads','tbqi.avg_kg_per_move');
							break;
					default :
						$query->select('tc.*','u.username','lkp_load_types.load_type','lvt.vehicle_type','os.order_status','lc.city_name as from','lcity.city_name as to','tbqi.quantity','tbqi.from_location_id','tbqi.to_location_id','tbq.lkp_bid_type_id','tbqi.volume','tbq.from_date','tbq.to_date');
						break;
				}
		
				$contractDetails   =   $query->get();
				
				Session::put ( 'indentdata', $input);
                $quoteId = $input['quoteId'];
				$quoteItemId = $input['quote_item_id'];
                $contractId = $input['contract_id'];
                $price = $input['total_hidden_amnt_'.$contractId];
                $sellerId = $input['seller_id'];
                $contractFromDate = $input['contract_from_date'];
                $contractToDate = $input['contract_to_date'];
				Log::info('Booknow form for buyer term: ' . Auth::id(), array('c' => '1'));
				$termQuotes = TermBuyerComponent::getTermQuotes($serviceId,$quoteId);
				$sourceLocationType = BuyerComponent::getSourceDestinationLocation('Source');
				$destinationLocationType = BuyerComponent::getSourceDestinationLocation('Destination');
				
				if($serviceId==ROAD_FTL || $serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
				$packagingType =  CommonComponent::getLoadBasedAllPackages($contractDetails[0]->lkp_load_type_id);
				}else{
				$packagingType = BuyerComponent::getPackagingType('Destination');		
				}
							
				return view('term.buyers.termbooknow',array('serviceId' => $serviceId,
						'quoteId'=>$quoteId,
						'termQuotes'=>$termQuotes,
						'sourceLocation'=>$sourceLocationType,
						'destinationLocation'=>$destinationLocationType,
						'packagingType'=>$packagingType,
						'buyerQuoteId'=>$quoteItemId,
						'isltl'=>0,
						'contractId'=>$contractId,
						'sellerId'=>$sellerId,
						'contractFromDate'=>$contractFromDate,
						'contractToDate'=>$contractToDate,
						'price'=>$price,
						'contractDetails'=> $contractDetails,
						'from_location_id'=>$contractDetails[0]->from_location_id,
						'to_location_id'=>$contractDetails[0]->to_location_id,
						'toLocationid'=>$contractDetails[0]->to_location_id,
						
				));
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
		
  public function CompareSellerQuotes($buyerQuoteId){
 	
  	try {
  		if(Session::get ( 'service_id' ) != ''){
  			$serviceId = Session::get ( 'service_id' );
  		}
  		/*Switch cases for term booknow pages**/
  		
  		TermBuyerComponent::getTermCompareQuotePrices($buyerQuoteId,$serviceId);
  		
  	} catch (Exception $e) {
  		echo 'Caught exception: ', $e->getMessage(), "\n";
  	}
  	
   }
   
    /**
    * Set buyer term booknow page
    * Insert values for booknow in term
    * @param Request $request
    * @return type
    */
    public function setTermBuyerBooknow(Request $request) 
    {
        Log::info('Insert the buyer booknow data: ' . Auth::id(), array('c' => '1'));
        try {

        	$cartContractDetails = DB::table('cart_items')
            ->where('cart_items.buyer_id',$request['buyerId'])
            ->select( 'cart_items.is_contract')
            ->get();

            if(!empty($cartContractDetails)){
                $is_contract = $cartContractDetails[0]->is_contract;
            }
            if((isset($is_contract) && $is_contract == 0) ){
                return array('success' => 0, 
                        'message' => "You can't proceed with book now,because the spot order exists in the cart!");
            } else {
	           
	            $serviceId = Session::get('service_id');
	            //Saving the user activity to the log table
	            
	            $input = Input::all();
	            if(isset($input['quoteId']) && $input['quoteId']!=''){
	            $quoteDetails = DB::table('term_buyer_quotes')
	            ->where('term_buyer_quotes.id',$input['quoteId'])
	            ->get();
	            }
	            
	            $itemDetails = DB::table('term_buyer_quote_items')
	            ->leftjoin('lkp_cities as c1', 'term_buyer_quote_items.from_location_id', '=', 'c1.id')
	            ->leftjoin('lkp_cities as c2', 'term_buyer_quote_items.to_location_id', '=', 'c2.id')
	            ->where('term_buyer_quote_items.id',$input['quoteItemId'])
	            ->select( 'c1.city_name as from_city','c2.city_name as to_city')
	            ->get();
	            
	            
	            $booknowAddToCart  =  new CartItem();
	            $booknowAddToCart->seller_id = $input['sellerId'];
	            $booknowAddToCart->buyer_id = $input['buyerId'];
	            $booknowAddToCart->lkp_service_id = Session::get('service_id');
	            $booknowAddToCart->buyer_quote_item_id = $input['quoteItemId'];
	            $booknowAddToCart->lkp_payment_mode_id = '4';
	            $booknowAddToCart->seller_post_item_id = $input['postItemId'];
	            $booknowAddToCart->lkp_src_location_type_id = $input['sourceLocationType'];
	            if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->lkp_dest_location_type_id = $input['destinationLocationType'];
	            $booknowAddToCart->lkp_packaging_type_id = $input['packagingType'];
	            }
                    //other fields
                    if($input['sourceLocationType']=='11')
                    $booknowAddToCart->other_src_location_type = $input['sourceLocationTypeOther'];
                    if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                    if($input['destinationLocationType']=='11')
	            $booknowAddToCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                    if($input['packagingType']=='13')
	            $booknowAddToCart->other_packaging_type = $input['packagingTypeOther'];
                    } 
	            $booknowAddToCart->price = $input['price'];
	            if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	            $booknowAddToCart->buyer_consignment_value = $input['consignmentValue'];
	            $booknowAddToCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
	            }
	            if($serviceId==RELOCATION_GLOBAL_MOBILITY){
	            	$booknowAddToCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['termContractDispatchDate']);
	            }
	            $booknowAddToCart->buyer_consignor_name = $input['consignorName'];
	            $booknowAddToCart->buyer_consignor_mobile = $input['consignorNumber'];
	            $booknowAddToCart->buyer_consignor_email = $input['consignorEmail'];
	            $booknowAddToCart->buyer_consignor_address = $input['consignorAddress'];
	            $booknowAddToCart->buyer_consignor_pincode = $input['consignorPin'];
	            if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->buyer_consignee_name = $input['consigneeName'];
	            $booknowAddToCart->buyer_consignee_mobile = $input['consigneeNumber'];
	            $booknowAddToCart->buyer_consignee_email = $input['consigneeEmail'];
	            $booknowAddToCart->buyer_consignee_pincode = $input['consigneePin'];
	            $booknowAddToCart->buyer_consignee_address = $input['consigneeAddress'];
	            $booknowAddToCart->buyer_additional_details = $input['additionalDetails'];
	            }
	            
	            $booknowAddToCart->term_contract_id = $input['contractId'];
	            if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->from_location = $itemDetails[0]->from_city;
	            }
	            if($serviceId==RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->to_location = $itemDetails[0]->from_city;
	            }else{
	            $booknowAddToCart->to_location = $itemDetails[0]->to_city;
	            }
	            $booknowAddToCart->is_contract = 1;
	            if($serviceId==RELOCATION_GLOBAL_MOBILITY){
	            $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['termContractDispatchDate']);
	            }else{
	            $booknowAddToCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	            }
	            $booknowAddToCart->delivery_date = '';
	            $booknowAddToCart->contract_from_date = CommonComponent::convertDateForDatabase($input['contractFromDate']);
	            $booknowAddToCart->contract_to_date = CommonComponent::convertDateForDatabase($input['contractToDate']);

	            $created_at = date ( 'Y-m-d H:i:s' );
	            $createdIp = $_SERVER['REMOTE_ADDR'];
	            $booknowAddToCart->created_by = Auth::id();
	            $booknowAddToCart->created_at = $created_at;
	            $booknowAddToCart->created_ip = $createdIp;
	            $booknowAddToCart->save();
	            if($booknowAddToCart->save()){
	                CommonComponent::auditLog($booknowAddToCart->id,'cart_items');
	                $cartInsertId = $booknowAddToCart->id;

	                switch ($serviceId) {
                    case ROAD_FTL:
                    case RELOCATION_DOMESTIC:
                    case RELOCATION_INTERNATIONAL:
	                    $cartData =  DB::select( DB::raw("SELECT
		                    q.*, u.username, q.price, service.service_name, q.dispatch_date,
		                    pz1.city_name as from_location, pzt1.city_name as to_location,
		                    bq1.lkp_post_status_id as post_status
		                    FROM
		                    cart_items q
		                    LEFT JOIN users u on u.id = q.seller_id
		                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id
		                    LEFT JOIN term_buyer_quote_items bq1 on bq1.id = q.buyer_quote_item_id and q.lkp_service_id = 1
		                    LEFT JOIN lkp_cities pz1
		                        ON (bq1.from_location_id = pz1.id and q.lkp_service_id = 1)
		                    LEFT JOIN lkp_cities pzt1
		                        ON (bq1.to_location_id = pzt1.id and q.lkp_service_id = 1)       
		                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case ROAD_PTL:
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                    concat(pp.pincode,'-',pp.postoffice_name) as from_location,
	                    concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bq on bq.id = q.buyer_quote_item_id and q.lkp_service_id = 2                
	                    LEFT JOIN lkp_ptl_pincodes pp
	                          ON pp.id = bq.from_location_id and q.lkp_service_id = 2
	                    LEFT JOIN lkp_ptl_pincodes ppt
	                          ON ppt.id = bq.to_location_id and q.lkp_service_id = 2   
	                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case COURIER:
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                   concat(pp.pincode,'-',pp.postoffice_name) as from_location,
                            (CASE
                                WHEN bq.lkp_courier_delivery_type_id = 1 THEN concat(ppt.pincode,'-',ppt.postoffice_name)
                                WHEN bq.lkp_courier_delivery_type_id = 2 THEN concat(ct.country_name)
                            END ) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bqi on bqi.id = q.buyer_quote_item_id and q.lkp_service_id = 21
	                    LEFT JOIN term_buyer_quotes bq on bq.id = bqi.term_buyer_quote_id and q.lkp_service_id = 21                
	                    LEFT JOIN lkp_ptl_pincodes pp
                                    ON (pp.id = bqi.from_location_id)        
                            
                            LEFT JOIN lkp_ptl_pincodes ppt
                                  ON (ppt.id = bqi.to_location_id   AND bq.lkp_courier_delivery_type_id = 1)  
                            LEFT JOIN lkp_countries ct
                                  ON (ct.id = bqi.to_location_id   AND bq.lkp_courier_delivery_type_id = 2)  
	                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case RAIL:
                    
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                    concat(pp.pincode,'-',pp.postoffice_name) as from_location,
	                    concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bq on bq.id = q.buyer_quote_item_id and q.lkp_service_id = 6                   
	                    LEFT JOIN lkp_ptl_pincodes pp
	                          ON pp.id = bq.from_location_id and q.lkp_service_id = 6
	                    LEFT JOIN lkp_ptl_pincodes ppt
	                          ON ppt.id = bq.to_location_id and q.lkp_service_id = 6   
	                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case AIR_DOMESTIC:
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                    concat(pp.pincode,'-',pp.postoffice_name) as from_location,
	                    concat(ppt.pincode,'-',ppt.postoffice_name) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bq on bq.id = q.buyer_quote_item_id  and q.lkp_service_id = 7                  
	                    LEFT JOIN lkp_ptl_pincodes pp
	                          ON pp.id = bq.from_location_id and q.lkp_service_id = 7
	                    LEFT JOIN lkp_ptl_pincodes ppt
	                          ON ppt.id = bq.to_location_id   and q.lkp_service_id = 7   
	                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case AIR_INTERNATIONAL:
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                    concat(pp.location,'-',pp.airport_name) as from_location,
	                    concat(ppt.location,'-',ppt.airport_name) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bq on bq.id = q.buyer_quote_item_id and q.lkp_service_id = 8              
	                    LEFT JOIN lkp_airports pp
	                          ON pp.id = bq.from_location_id and q.lkp_service_id = 8
	                    LEFT JOIN lkp_airports ppt
	                          ON ppt.id = bq.to_location_id   and q.lkp_service_id = 8   
	                    where q.id ='".$cartInsertId."'"));
	                    break;
                    case OCEAN:
	                    $cartData =  DB::select( DB::raw("SELECT
	                    q.*,
	                    u.username,
	                    q.price,
	                    concat(pp.country_name,'-',pp.seaport_name) as from_location,
	                    concat(ppt.country_name,'-',ppt.seaport_name) as to_location,
	                    service.service_name,
	                    q.dispatch_date,
	                    bq.lkp_post_status_id as post_status
	                    FROM
	                    cart_items q
	                    LEFT JOIN users u on u.id = q.seller_id
	                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id                    
	                    LEFT JOIN term_buyer_quote_items bq on bq.id = q.buyer_quote_item_id  and q.lkp_service_id = 9                  
	                    LEFT JOIN lkp_seaports pp
	                          ON pp.id = bq.from_location_id and q.lkp_service_id = 9
	                    LEFT JOIN lkp_seaports ppt
	                          ON ppt.id = bq.to_location_id and q.lkp_service_id = 9
	                    where q.id ='".$cartInsertId."'"));
	                    break;
	               case RELOCATION_GLOBAL_MOBILITY:
	                    	$cartData =  DB::select( DB::raw("SELECT
		                    q.*, u.username, q.price, service.service_name, q.dispatch_date,
		                    pz1.city_name as from_location,
		                    bq1.lkp_post_status_id as post_status
		                    FROM
		                    cart_items q
		                    LEFT JOIN users u on u.id = q.seller_id
		                    LEFT JOIN lkp_services service on service.id = q.lkp_service_id
		                    LEFT JOIN term_buyer_quote_items bq1 on bq1.id = q.buyer_quote_item_id and q.lkp_service_id = 1
		                    LEFT JOIN lkp_cities pz1
		                        ON (bq1.from_location_id = pz1.id and q.lkp_service_id = 1)
		                    where q.id ='".$cartInsertId."'"));
	                    	break;
                    }
	                
                   
	                $booknowAddToViewCart  =  new ViewCartItem();
	                $booknowAddToViewCart->id = $cartInsertId;
	                $booknowAddToViewCart->seller_id = $input['sellerId'];
	                $booknowAddToViewCart->buyer_id = $input['buyerId'];
	                $booknowAddToViewCart->lkp_service_id = Session::get('service_id');
	                $booknowAddToViewCart->buyer_quote_item_id = $input['quoteItemId'];
	                $booknowAddToViewCart->lkp_payment_mode_id = '4';
	                $booknowAddToViewCart->seller_post_item_id = $input['postItemId'];
	                $booknowAddToViewCart->lkp_src_location_type_id = $input['sourceLocationType'];
	                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	                $booknowAddToViewCart->lkp_dest_location_type_id = $input['destinationLocationType'];
	                $booknowAddToViewCart->lkp_packaging_type_id = $input['packagingType'];
	                }
                        //other fields in booknow form
	                	if($input['sourceLocationType']=='11')
                        $booknowAddToViewCart->other_src_location_type = $input['sourceLocationTypeOther'];
                        if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
                        if($input['destinationLocationType']=='11')
                        $booknowAddToViewCart->other_dest_location_type = $input['destinationLocationTypeOther'];
                        if($input['packagingType']=='13')
                        $booknowAddToViewCart->other_packaging_type = $input['packagingTypeOther'];
                        }
	                $booknowAddToViewCart->price = $input['price'];
	                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	                $booknowAddToViewCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	                $booknowAddToViewCart->buyer_consignment_value = $input['consignmentValue'];
	                $booknowAddToViewCart->buyer_consignment_needs_insurance = $input['consignmentNeedInsurance'];
	                }
	                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
                            $booknowAddToViewCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['termContractDispatchDate']);
	                }else{
                            $booknowAddToViewCart->buyer_consignment_pick_up_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
                        }
                        $booknowAddToViewCart->buyer_consignor_name = $input['consignorName'];
	                $booknowAddToViewCart->buyer_consignor_mobile = $input['consignorNumber'];
	                $booknowAddToViewCart->buyer_consignor_email = $input['consignorEmail'];
	                $booknowAddToViewCart->buyer_consignor_address = $input['consignorAddress'];
	                $booknowAddToViewCart->buyer_consignor_pincode = $input['consignorPin'];
	                if($serviceId!=RELOCATION_GLOBAL_MOBILITY){
	                $booknowAddToViewCart->buyer_consignee_name = $input['consigneeName'];
	                $booknowAddToViewCart->buyer_consignee_mobile = $input['consigneeNumber'];
	                $booknowAddToViewCart->buyer_consignee_email = $input['consigneeEmail'];
	                $booknowAddToViewCart->buyer_consignee_pincode = $input['consigneePin'];
	                $booknowAddToViewCart->buyer_consignee_address = $input['consigneeAddress'];
	                $booknowAddToViewCart->buyer_additional_details = $input['additionalDetails'];
	                }
	                if($serviceId==RELOCATION_GLOBAL_MOBILITY){
	                $booknowAddToViewCart->dispatch_date = CommonComponent::convertDateForDatabase($input['termContractDispatchDate']);
	                }else{
	                $booknowAddToViewCart->dispatch_date = CommonComponent::convertDateForDatabase($input['consignmentPickupDate']);
	                }
		            $booknowAddToViewCart->delivery_date = '';
	                $booknowAddToViewCart->username = $cartData[0]->username;
	                if($serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_INTERNATIONAL){
	                $booknowAddToViewCart->from_location = $itemDetails[0]->from_city;
	                $booknowAddToViewCart->to_location = $itemDetails[0]->to_city;
	                $booknowAddToViewCart->lkp_international_type_id = $quoteDetails[0]->lkp_lead_type_id;
	                }elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
	                $booknowAddToViewCart->to_location = $itemDetails[0]->from_city;
	                }else{
	                $booknowAddToViewCart->from_location = $cartData[0]->from_location;
	                $booknowAddToViewCart->to_location = $cartData[0]->to_location;
	                }
	
	                $booknowAddToViewCart->post_status = $cartData[0]->post_status;
	                $booknowAddToViewCart->service_name = $cartData[0]->service_name;
	                
	                $booknowAddToViewCart->term_contract_id = $cartData[0]->term_contract_id;
	              
	                $booknowAddToViewCart->is_contract = '1';
	              
	                $booknowAddToViewCart->delivery_date = '';
	                $booknowAddToViewCart->contract_from_date = $cartData[0]->contract_from_date;
	                $booknowAddToViewCart->contract_to_date = $cartData[0]->contract_to_date;

	                $booknowAddToViewCart->created_by = Auth::id();
	                $booknowAddToViewCart->created_at = $created_at;
	                $booknowAddToViewCart->created_ip = $createdIp;
	                $booknowAddToViewCart->save();
	               }
            	}

            	TermBuyerComponent::saveIndentDetails($serviceId);
            	
            return array('success' => 1, 'message' => "Item is added to cart successfully.", 'id' => $booknowAddToViewCart->id);;
            
        } catch (Exception $e) {
            
        }
    }

	public function DownloadBuyerBids($buyerQuoteId){
		try {
			
			$buyerId = DB::table('term_buyer_quotes')->where('id','=',$buyerQuoteId)->pluck('created_by');
			$filepath = "uploads/buyer/$buyerId/Terms/$buyerQuoteId/biddocuments.zip";
			if(file_exists($filepath)){
				header('Content-Type: application/zip');
				header('Content-disposition: attachment; filename='.$filepath);
				header('Content-Length: ' . filesize($filepath));
				readfile($filepath);
			}else{
				echo '<script language="javascript">';
				echo 'alert("something happend wrong")';
				echo '</script>';
				if(Session::get('service_id') == ROAD_FTL){
					return Redirect::back()->with('message_create_post','something happend wrong');
				}else{
					return Redirect::back()->with('message_create_post_ptl','something happend wrong');
				}
			}

			
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}
	
	
/******* Below Script for get seller list in Term************** */
	public function getTermSellerList() {		
		$results = array();
		try {
			Log::info('Get Seller lsit from depends in Term: ' . Auth::id(), array('c' => '1'));
			$roleId = Auth::User()->lkp_role_id;
			//Update the user activity to the buyer get seller list
			if ($roleId == BUYER) {
				CommonComponent::activityLog("BUYER_SELLERLIST", BUYER_SELLERLIST, 0, HTTP_REFERRER, CURRENT_URL);
			}			
			$seller_data = DB::table('users')			
			->leftjoin ('seller_details', 'users.id', '=', 'seller_details.user_id')
			->leftjoin ('seller_details', 'users.id', '=', 'sellers.user_id')->orderBy ( 'users.username', 'asc' )
			->where(['users.is_active' => 1])			
			->where('users.lkp_role_id', SELLER)
                        ->orWhere('users.secondary_role_id', SELLER)
			->groupBy('sellers.user_id')
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
	
  /**Updatint term buyer items */

	public function termPostItemupdate(){
		
		TermBuyerQuoteItem::where([
				"id" => $_REQUEST['buyer_item_id'],
				
		])
		->update(
				array('from_location_id' => $_REQUEST['term_from_location_id'],
						'to_location_id' => $_REQUEST['term_to_location_id'],
						'lkp_load_type_id' => $_REQUEST['term_load_type'],
						'lkp_vehicle_type_id' => $_REQUEST['term_vehicle_type'],
						'quantity' => $_REQUEST['term_quantity']
				)
		);
		
		$load_type=CommonComponent::getLoadType($_REQUEST['term_load_type']);
		$vehicle_type=CommonComponent::getVehicleType($_REQUEST['term_vehicle_type']);
		
		echo $_REQUEST['term_from_location']."|".$_REQUEST['term_to_location']."|".$load_type."|".$vehicle_type."|".$_REQUEST['term_quantity'];
		
	}
	public function termPostItemDelete(){
		
		 TermBuyerQuoteBidTermsFile::where([
				"id" => $_REQUEST['fileid'],
		])->delete();		
	}
	
	//Send Request values to term view count in buyer -->srinu
	public function termViewCount(){	
		try {
			$buyerQuoteId=$_REQUEST['term_quote_id'];
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			$userId =  Auth::id();
			/*Switch cases for term viewcount  components**/	
			CommonComponent::termViewBuyerCountUpdate($buyerQuoteId,$serviceId,$userId);	
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
		 
	}
	
	public function forgetInventorySession(){
		
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
		
	}
	
	
	/**
	 * get the buyer onfly calculation for courier term book Now
	 * Inserts fields
	 * @param Request $request
	 * @return type
	 */
	public function getCourierTermFreightDetails() {
		Log::info('Get posted Seller offer: ' . Auth::id(), array('c' => '1'));
		try {
	
			$serviceId = Session::get('service_id');
	
			if (empty($serviceId) ) {
				return;
			}
	
			$input = Input::all();
			
			$freightDetails = TermBuyerComponent::getCourierTermFreightDetailsCal($input);
			return [
			'success' => 1,
			'freightDetails' => [
			'totalAmount' => $freightDetails['totalAmount'],
			'totalFrieght' => $freightDetails['freight']
			]
			];
		} catch (Exception $e) {
	
		}
	}
	
	
}