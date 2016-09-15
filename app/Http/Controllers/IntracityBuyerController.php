<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Components\CommonComponent;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Response;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Models\IctBuyerQuote;
use App\Models\IctBuyerQuoteItem;
use App\Components\Intracity\IntracityBuyerComponent;
use Log;
use App\Components\BuyerComponent;
use App\libraries\RestClient as RestClient;
// Intracity Models
use App\Models\IdeaApiTruckReqResponses;

class IntracityBuyerController extends Controller 

{
	/**
	 * Create a new IntracityBuyerController instance.
	 *
	 * @return void
	 */
	public $user_pk;
	public function __construct() {
		$this->middleware ( 'auth' );
		if (isset ( Auth::User ()->id )) {
			
			$this->user_pk = Auth::User ()->id;
		}
	}
	
	/**
	 * Auto complete for Intracity Create Buyer Post
	 *
	 * for to location and from location
	 */
	public function autocomplete() {
		Log::info ( 'get Intracity from location using autocomplete: ' . Auth::id (), array (
				'c' => '1' 
		) );
		try {
			$term = Input::get ( 'term' );
			$fromlocation_loc = Input::get ( 'fromlocation' );
			$city_id = Input::get ( 'city_id' );
			
			$results = array ();
			if (isset ( $fromlocation_loc )) {
				$queries = DB::table ( 'lkp_ict_locations' )->orderBy ( 'ict_location_name', 'asc' )->where ( 'ict_location_name', 'LIKE', $term . '%' )->where ( 'ict_location_name', '<>', $fromlocation_loc )->where ( 'lkp_city_id', '=', $city_id )->take ( 10 )->get ();
			} else {
				$queries = DB::table ( 'lkp_ict_locations' )->orderBy ( 'ict_location_name', 'asc' )->where ( 'ict_location_name', 'LIKE', $term . '%' )->where ( 'lkp_city_id', '=', $city_id )->take ( 10 )->get ();
			}
			foreach ( $queries as $query ) {
				$results [] = [ 
						'id' => $query->id,
						'value' => $query->ict_location_name 
				];
			}
			return Response::json ( $results );
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	public function autocompleteto() {
		Log::info ( 'get to location using autocomplete: ' . Auth::id (), array (
				'c' => '1' 
		) );
		try {
			$term = Input::get ( 'term' );
			$fromlocation_loc = Input::get ( 'fromlocation' );
			$city_id = Input::get ( 'city_id' );
			$results = array ();
			if (isset ( $fromlocation_loc )) {
				$queries = DB::table ( 'lkp_ict_locations' )->orderBy ( 'ict_location_name', 'asc' )->where ( 'ict_location_name', 'LIKE', $term . '%' )->where ( 'ict_location_name', '<>', $fromlocation_loc )->where ( 'lkp_city_id', '=', $city_id )->take ( 10 )->get ();
			} else {
				$queries = DB::table ( 'lkp_ict_locations' )->orderBy ( 'ict_location_name', 'asc' )->where ( 'ict_location_name', 'LIKE', $term . '%' )->where ( 'lkp_city_id', '=', $city_id )->take ( 10 )->get ();
			}
			foreach ( $queries as $query ) {
				$results [] = [ 
						'id' => $query->id,
						'value' => $query->ict_location_name 
				];
			}
			return Response::json ( $results );
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage (), "\n";
		}
	}
	
	/**
	 * View buyer post screen
	 */
	public function buyerPost() {	

		if(Session::get('service_id') == ROAD_FTL){
              return redirect('createbuyerquote');
        }elseif(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL 
        		|| Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL 
        		|| Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER){
              return redirect('/ptl/createbuyerquote');
        }else if(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE || Session::get('service_id') == RELOCATION_INTERNATIONAL){
                return redirect('/relocation/creatbuyerrpost');
        }else if(Session::get('service_id') == ROAD_TRUCK_HAUL){
            return redirect('truckhaul/createbuyerquote');
        }else if(Session::get('service_id') == ROAD_TRUCK_LEASE){
            return redirect('trucklease/createbuyerquote');
        }
		$cities = CommonComponent::getIntracityCities ();
		$loadType = CommonComponent::getAllLoadTypes ();
		$vehicle_types = CommonComponent::getAllVehicleTypes ();
		$rate_types = CommonComponent::getIntracityRateTypes ();
		$weight_types = CommonComponent::getIntracityUOM ();
		
		return view ( 'intracity.buyers.create_post', array (
				'cities' => $cities,
				'load_type' => $loadType,
				'rate_type' => $rate_types,
				'weight_type' => $weight_types,
				'vehicle_types' => $vehicle_types 
		) );
	}
	
	/**
	 * Creat buyer post
	 */
	public function createBuyerPost() {

		Log::info ( 'Buyer post an intracity requirement:' . $this->user_pk, array (
				'c' => '1' 
		) );
		CommonComponent::activityLog ( "INTRACITY_BUYER_POST", INTRACITY_BUYER_POST, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (Input::all ()) {
			$data = Input::all ();
			
			$buyerIntraPostArrVal = IntracityBuyerController::createBuyerIntraPost ( $data );
			$buyerIntraPostArr = explode('#',$buyerIntraPostArrVal);
			$buyerIntraPost = $buyerIntraPostArr[1];
			$buyerPostId = $buyerIntraPostArr[0];
			$obj = new RestClient;
			$obj->execute();

			$pickupDate = $data ['pickup_date'];
			$pickingDate = str_replace ( '/', '-', $pickupDate );	
                        
			$params = '<?xml version="1.0" encoding="UTF-8"?>
									<Request>
									<Calldata>
									<Request_Ref_ID>POST_'.$buyerPostId.'</Request_Ref_ID>
									<URL>'.CommonComponent::getFrapiUrlByServerUrl().'</URL>
									<Request_type>2</Request_type>
									<From_Loc_id>'.$data ['from_location_id'].'</From_Loc_id>
									<To_Loc_id>'.$data ['to_location_id'].'</To_Loc_id>  
									<Req_Date>'.DATE ( "dmY", strtotime ( $pickingDate ) ).'</Req_Date>
									<Req_Time>'.DATE("Hi", strtotime ($data ['pickup_time']) ).'</Req_Time>
									<Truck_Type>'.$data ['lkp_vehicle_id'].'</Truck_Type>
									<Load_Type>'.$data ['load_type'].'</Load_Type>
									<From_Loc_File_Name>COCHIN.wav</From_Loc_File_Name>
									<To_Loc_File_Name>BANGLORE.wav</To_Loc_File_Name>
									<Price>0</Price>
									<City>'.$data ['lkp_city_id'].'</City>
									<Trip_Type>'.$data ['lkp_rate_type'].'</Trip_Type>
									</Calldata>
									</Request>';
			//echo $params;
			//exit;						
			$models = RestClient::post("https://ibs.ideacellular.com/Logistiks/PIService.svc/Reqfortruck?", $params); 
			$response = $models->getResponse();			
			//echo $response;
			//exit;						
			//Save Idea Request 
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];

			$ideaRequest = new IdeaApiTruckReqResponses();
			$ideaRequest->reference_id = $buyerPostId;
			$ideaRequest->request_type = 2;
			$ideaRequest->idea_request_xml = $params;
			$ideaRequest->created_at = $createdAt;
			$ideaRequest->created_ip = $createdIp;
			$ideaRequest->created_by = $this->user_pk;
			$ideaRequest->save();

			if ($buyerIntraPost == '0') {
				return Redirect ( 'intracity/buyer_post' )->with ( 'error_message', 'Error occured while posting.' );
				
			} else {
				return Redirect ( 'buyerposts' )->with ( 'transacId', $buyerIntraPost );
				
			}
		}
		$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		$loadType = \DB::table ( 'lkp_load_types' )->lists ( 'load_type', 'id' );
		
		return view ( 'intracity.buyers.create_post', array (
				'cities' => $cities,
				'load_type' => $loadType 
		) );
	}
	public function createBuyerIntraPost($data) {
		Log::info ( 'Buyer intracity post creation is under process:' . $this->user_pk, array (
				'c' => '1' 
		) );
		CommonComponent::activityLog ( "INTRACITY_BUYER_POST_CREATION", INTRACITY_BUYER_POST_CREATION, 0, HTTP_REFERRER, CURRENT_URL );
		$buyerQuote = new IctBuyerQuote ();
		
		// Parametres
                //generate transactionId
		$created_year = date('Y'); 
                $postid  =   CommonComponent::getPostID(Session::get('service_id'));
		$transactionId = 'INTRA/' .$created_year .'/'. str_pad($postid, 6, "0", STR_PAD_LEFT); //generated random TransactionId
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		$buyerQuote->transaction_id = $transactionId;
		$buyerQuote->lkp_service_id = ROAD_INTRACITY;
		$buyerQuote->buyer_id = $this->user_pk;
		$buyerQuote->is_commercial = $data['is_commercial'];
		$buyerQuote->created_at = $createdAt;
		$buyerQuote->created_ip = $createdIp;
		$buyerQuote->created_by = $this->user_pk;
		
		if ($buyerQuote->save ()) {
			CommonComponent::auditLog ( $buyerQuote->id, 'ict_buyer_quotes' );			
			$buyerQuoteItem = new IctBuyerQuoteItem ();
			// changing date to mysql format			
			$pickupDate = $data ['pickup_date'];
			$pickingDate = str_replace ( '/', '-', $pickupDate );			
			$buyerQuoteItem->buyer_quote_id = $buyerQuote->id;
			$buyerQuoteItem->pickup_date = DATE ( "Y-m-d", strtotime ( $pickingDate ) );
			$buyerQuoteItem->pickup_time = $data ['pickup_time'];
			$buyerQuoteItem->ict_lkp_city_id = $data ['lkp_city_id'];
			$buyerQuoteItem->from_location_id = $data ['from_location_id'];
			$buyerQuoteItem->to_location_id = $data ['to_location_id'];
			$buyerQuoteItem->lkp_load_type_id = $data ['load_type'];
			$buyerQuoteItem->lkp_vehicle_type_id = $data ['lkp_vehicle_id'];
			$buyerQuoteItem->lkp_ict_weight_uom_id = $data ['lkp_ict_weight_parameter_id'];
			$buyerQuoteItem->units = $data ['units'];
			$buyerQuoteItem->lkp_post_status_id = OPEN;
			$buyerQuoteItem->lkp_ict_rate_type_id = $data ['lkp_rate_type'];			
			$buyerQuoteItem->created_at = $createdAt;
			$buyerQuoteItem->created_ip = $createdIp;
			$buyerQuoteItem->created_by = $this->user_pk;
			if ($buyerQuoteItem->save ()) {
				CommonComponent::auditLog ( $buyerQuoteItem->id, 'ict_buyer_quote_items' );
				return $buyerQuote->id.'#'.$buyerQuote->transaction_id;
			} else {
				return '0';
			}
		} else {
			return '0';
		}
	}

	/**
	 * CANCEL INTRACITY BUYER POST
	 * 
	 */
	public function cancelBuyerPost(){

		if (isset ( $_POST ['postId'] ) && $_POST ['postId'] != '') {
			$postId = $_POST ['postId'];
				
			$hasRecord = DB::table ( 'ict_buyer_quotes as bq' )
			->leftJoin ( 'ict_buyer_quote_items as bqi', 'bq.id', '=', 'bqi.buyer_quote_id' )
			->where ( 'bq.buyer_id', '=', Auth::User ()->id )
			->where ( 'bq.id', '=', $postId )
			->select ( 'bqi.lkp_post_status_id')
			->get ();
			
			if ($hasRecord) {
				$delete = '';
				foreach ( $hasRecord as $record ) {

					if ($record->lkp_post_status_id != BOOKED ) {
						$delete = 1;
					} else {
						$delete = 0;
					}
				}
				if ($delete == 1) {
					$is_cancelled = IntracityBuyerController::buyerPostCancellation ( $postId );
					if($is_cancelled==1){
							
						echo "Selected post has been cancelled successfully";
					}
				} else{
					echo "Can't cancel the post as you have already booked the order.";
				}
			}
		
			else {
				echo "Please select the post, need to be cancelled";
			}
		}
	}

	public function buyerPostCancellation($postId) {
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ["REMOTE_ADDR"];
	
		try {
			IctBuyerQuoteItem::where ( "buyer_quote_id", $postId )->update ( array (
			'lkp_post_status_id' => CANCELLED,
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

}
