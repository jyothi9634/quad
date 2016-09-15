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
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuyerDetail;
use App\Models\BuyerBusinessDetail;
use App\Models\Seller;
use App\Models\SellerIntracityLocality;
use App\Models\SellerPmCity;
use App\Models\SellerService;
use App\Models\UserOtp;
use App\Models\User;
use Socialize;
use App\Models\SellerDetail;
use Log;
use App\Models\LkpServices;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Matching\SellerMatchingComponent;
use App\Components\Search\TermSellerSearchComponent;
use Maatwebsite\Excel\Facades\Excel;
use App\Components\Term\TermBuyerComponent;

class TestController extends Controller {

	public function index() {

		echo \App\Components\CheckoutComponent::getSellerInvoice(1529,1);
		echo \App\Components\CheckoutComponent::getSellerInvoice(1459,2);

		die;

		/*$matchedItems['from_city_id']=185;
		$matchedItems['lkp_vehicle_type_id']=2;
		$matchedItems['valid_from']="2016-06-10";
		$matchedItems['valid_to']="2016-06-13";
		$matchedItems['lkp_trucklease_lease_term_id']=1;
		$matchedItems['minimum_lease_period']=2;
		echo "<pre>";print_R($matchedItems);echo "</pre>";
		SellerMatchingComponent::doMatching(ROAD_TRUCK_LEASE,152,2,$matchedItems);*/


		$matchedItems['from_location_id']=185;
		$matchedItems['lkp_vehicle_type_id']=2;
		$matchedItems['from_date']="2016-06-10";
		$matchedItems['to_date']="2016-06-10";
		$matchedItems['lkp_trucklease_lease_term_id']=1;
		echo "<pre>";print_R($matchedItems);echo "</pre>";


		BuyerMatchingComponent::doMatching(ROAD_TRUCK_LEASE,104,2,$matchedItems);


		/*date_default_timezone_set("Asia/Kolkata");
		echo date('Y-m-d H:i:s');;die;
		$documents = DB::table('gsa_service_wise_documents as gswd')
			->leftjoin ( 'lkp_documents as ld', 'ld.id', '=', 'gswd.document_id' )
			->where('role_id',1)
			->where('service_id',1)
			->lists("ld.title");
		$issanutryapplied = CommonComponent::IsStatutoryApplied(1,1);
		if($issanutryapplied == 1){
			$sanutrydocs = CommonComponent::getPostDocuments(1388,1);
			if(isset($sanutrydocs->incoming)){
				$documents[''] = $sanutrydocs->incoming;
			}
			if(isset($sanutrydocs->outgoing)){
				$documents[''] = $sanutrydocs->outgoing;
			}
		}*/
		
		/*$counttype = 1;
		$userId = Auth::User ()->id;
		$posts = DB::table('relocationpet_seller_posts as sps')->select('sps.id','spis.is_private')
			->join('relocationpet_seller_post_items as spis','spis.seller_post_id','=','sps.id')
			->where('sps.lkp_post_status_id', OPEN);
		if($counttype == 2){
			$posts->where("spis.is_private",0);
		}
		$posts = $posts->where('sps.seller_id', $userId)->get();
		$postsCount = CommonComponent::getMatchingPostsCount(RELOCATION_PET_MOVE,$posts,$counttype);
		echo $postsCount;die;*/
		/*$termcount = 0;
		$service = 2;

		echo $userId;die;
		$termBuyerQuotes = DB::table('term_buyer_quotes as bqi')->select('bqi.id')
			->leftjoin('term_buyer_quote_items as bqit', 'bqi.id', '=', 'bqit.term_buyer_quote_id')
			->where('bqi.created_by', Auth::User()->id)
			->where('bqi.lkp_service_id', $service)
			->where('bqi.lkp_post_status_id', 2)
			->groupBy('bqit.term_buyer_quote_id')
			->get();
		//echo "<pre>";print_R($termBuyerQuotes);
		foreach ($termBuyerQuotes as $termBuyerQuote) {
			echo count(TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($termBuyerQuote->id, $service))."<br/>";
			$termcount = $termcount + count(TermBuyerComponent::getTermBuyerQuoteSellersQuotesPricesFromId($termBuyerQuote->id, $service));
		}

		echo "---".$termcount;die;


		$messages = CommonComponent::getMessagesCount($userId,ROAD_FTL,2);
		echo $messages."te";die;


		$gridBuyer = DB::table ( 'buyer_quote_sellers_quotes_prices as bqsp' );
		$gridBuyer->join ( 'buyer_quote_items as bqi', 'bqi.id', '=', 'bqsp.buyer_quote_item_id' );
		$gridBuyer->where('bqi.lkp_post_status_id',OPEN);
		$gridBuyer->where('bqsp.buyer_id',Auth::id());
		$gridBuyer->select ( 'bqsp.id');
		
		
		$request = array();

		$orderDetails = DB::table('orders')
			->where(['id' => 902])
			->select('buyer_id','order_no','seller_id')
			->first();

		$msg_params = array(
			'ordernumber' => $orderDetails->order_no,
			'servicename' => CommonComponent::getServiceName(9),
			'datetime' => CommonComponent::convertDateDisplay("25-01-2016")
		);

		$getMobileNumberbuyer  =   array("9866343939");
		CommonComponent::sendSMS($getMobileNumberbuyer,CONSIGNMENT_DELIVERED,$msg_params);
		echo "done";die;*/
	}

	public function updateenquiries($id){
		switch($id){
			case ROAD_FTL       :
				$gridBuyer = DB::table ( 'seller_post_items as sqi' );
				$gridBuyer->join ( 'seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->select ( 'sqi.id','sqi.from_location_id','sqi.to_location_id','sqi.lkp_load_type_id','sqi.lkp_vehicle_type_id','sqi.transitdays','sqi.lkp_post_status_id', 'sp.from_date', 'sp.to_date');
				$ftlposts = $gridBuyer->get();
				foreach($ftlposts as $ftlpost){
					$request = array();
					$request['from_city_id'] = $ftlpost->from_location_id;
					$request['to_city_id'] = $ftlpost->to_location_id;
					$request['lkp_load_type_id'] = $ftlpost->lkp_load_type_id;
					$request['lkp_vehicle_type_id'] = $ftlpost->lkp_vehicle_type_id;
					$request['dispatch_date'] = CommonComponent::convertMysqlDate($ftlpost->from_date);
					$request['delivery_date'] = CommonComponent::convertMysqlDate($ftlpost->to_date);
					SellerMatchingComponent::doMatching(1, $ftlpost->id, 2, $request);
				}

				echo "FTL Matching done <br/>";
				break;
			case ROAD_PTL       :
				$gridBuyer = DB::table ( 'ptl_seller_post_items as sqi' );
				$gridBuyer->join ( 'ptl_seller_posts as sp', 'sp.id', '=', 'sqi.seller_post_id' );
				$gridBuyer->select ( 'sqi.id','sqi.from_location_id','sqi.to_location_id','sqi.transitdays','sqi.lkp_post_status_id', 'sp.from_date', 'sp.to_date','sp.lkp_ptl_post_type_id');
				$ptlposts = $gridBuyer->get();
				foreach($ptlposts as $ptlpost){
					//echo "<pre>";print_R($ptlpost);die;
					$request = array();
					$request['zone_or_location']=$ptlpost->lkp_ptl_post_type_id;
					$request['from_location_id'] = $ptlpost->from_location_id;
					$request['to_location_id'] = $ptlpost->to_location_id;
					$request['valid_from'] = CommonComponent::convertMysqlDate($ptlpost->from_date);
					$request['valid_to'] = CommonComponent::convertMysqlDate($ptlpost->to_date);
					//echo "<pre>";print_R($request);die;
					if($ptlpost->lkp_post_status_id){
						SellerMatchingComponent::doMatching(2, $ptlpost->id, 2, $request);
					}
				}

				echo "PTL Matching done <br/>";die;
				//echo $queryBuilder->tosql();die;
				break;
			case ROAD_INTRACITY :
				//coming soon
				break;
			case ROAD_TRUCK_HAUL:
				//coming soon
				break;
			default             :
				break;
		}
		die("fianl");

	}
	public function misreport(){

		if (empty(Input::all())) {
			return view('reports.misreport', [
				'date' => date("Y")
			]);
		}else{
			//FTL
			global $misreprot;
			$misreprot[1]['sheetname'] = "FTL MIS";
			$misreprot[1]['data'] = TestController::getReportData(ROAD_FTL);
			
			//LTL
			$misreprot[2]['sheetname'] = "LTL MIS";
			$misreprot[2]['data'] = TestController::getReportData(ROAD_PTL);

			//Rail
			$misreprot[6]['sheetname'] = "Rail MIS";
			$misreprot[6]['data'] = TestController::getReportData(RAIL);

			//Air Domestic
			$misreprot[7]['sheetname'] = "Air Domestic MIS";
			$misreprot[7]['data'] = TestController::getReportData(AIR_DOMESTIC);

			//Air International
			$misreprot[8]['sheetname'] = "Air internationa MIS";
			$misreprot[8]['data'] = TestController::getReportData(AIR_INTERNATIONAL);

			//Ocean
			$misreprot[9]['sheetname'] = "Ocean MIS";
			$misreprot[9]['data'] = TestController::getReportData(OCEAN);

			//Courier
			$misreprot[21]['sheetname'] = "Courier MIS";
			$misreprot[21]['data'] = TestController::getReportData(COURIER);


			$misreprot[4]['sheetname'] = "Truckhaul MIS";
			$misreprot[4]['data'] = TestController::getReportData(ROAD_TRUCK_HAUL);

			$misreprot[5]['sheetname'] = "Trucklease MIS";
			$misreprot[5]['data'] = TestController::getReportData(ROAD_TRUCK_LEASE);

			$misreprot[15]['sheetname'] = "Relocation Domestic MIS";
			$misreprot[15]['data'] = TestController::getReportData(RELOCATION_DOMESTIC);

			$misreprot[17]['sheetname'] = "Relocation Pet MIS";
			$misreprot[17]['data'] = TestController::getReportData(RELOCATION_PET_MOVE);

			$misreprot[20]['sheetname'] = "Relocation Office MIS";
			$misreprot[20]['data'] = TestController::getReportData(RELOCATION_OFFICE_MOVE);

			$misreprot[18]['sheetname'] = "Relocation International MIS";
			$misreprot[18]['data'] = TestController::getReportData(RELOCATION_INTERNATIONAL);

			$misreprot[19]['sheetname'] = "Relocation Global Mobility MIS";
			$misreprot[19]['data'] = TestController::getReportData(RELOCATION_GLOBAL_MOBILITY);

			Excel::create('ExcelExport', function ($excel) {
				global $misreprot;

				foreach($misreprot as $serviceid => $report){
					global $individualreport;
					global $indserviceid;
					$individualreport = $report;
					$indserviceid = $serviceid;
					$excel->sheet($individualreport['sheetname'], function ($sheet) {
						global $indserviceid;
						if(in_array($indserviceid,array(ROAD_TRUCK_LEASE,RELOCATION_OFFICE_MOVE,RELOCATION_GLOBAL_MOBILITY) )){
							$sheet->row(1, array('buyername','sellername','seller_pickup_date','seller_delivery_date','created_at','location','invoiceamount','service_tax_amount'));
						}else{
							$sheet->row(1, array('buyername','sellername','seller_pickup_date','seller_delivery_date','created_at','fromlocation','tolocation','invoiceamount','service_tax_amount'));
						}

						$sheet->row(1, function ($row) {
							$row->setFontWeight('bold');
							$row->setBackground('#ffff00');
						});
						global $individualreport;
						$exceldata = $individualreport['data'];
						foreach ($exceldata as $lowestrow) {
							$sheet->appendRow((array)$lowestrow);
						}
						if(in_array($indserviceid,array(ROAD_TRUCK_LEASE,RELOCATION_OFFICE_MOVE,RELOCATION_GLOBAL_MOBILITY) )){
							$tocolumn = "H30";
						}else{
							$tocolumn = "I30";
						}
						$sheet->setBorder("A1:$tocolumn", 'thin');
						$sheet->cells("A1:$tocolumn", function($cells) {
							$cells->setAlignment('center');
							$cells->setValignment('middle');
						});
					});
				}
			})->export('xls');
		}

	}


	public static function getReportData($serviceId){
		$data = array();
		$inputs = Input::all();
		$valid_from = CommonComponent::convertDateForDatabase($inputs['valid_from'])."  00:00:00";
		$valid_to = CommonComponent::convertDateForDatabase($inputs['valid_to']);
		$valid_to = date('Y-m-d', strtotime($valid_to. " + 1 days"))."  00:00:00";
		//echo $valid_from."--".$valid_to;die;
		switch ($serviceId) {
			case ROAD_FTL:
			case ROAD_TRUCK_HAUL:
			case RELOCATION_DOMESTIC:
			case RELOCATION_PET_MOVE:
			case RELOCATION_INTERNATIONAL:
				$ftlquery = "SELECT b.username AS buyername, s.username AS sellername, orders.seller_pickup_date, orders.seller_delivery_date, orders.created_at, d.city_name AS fromlocation, t.city_name AS tolocation, orders.price AS invoiceamount, inv.service_tax_amount
								FROM `orders` 
								JOIN order_invoices AS inv ON inv.order_id = orders.id
								JOIN users AS b ON b.id = buyer_id
								JOIN users AS s ON s.id = seller_id
								JOIN lkp_cities AS d ON d.id = from_city_id
								JOIN lkp_cities AS t ON t.id = to_city_id
								WHERE lkp_service_id =$serviceId
								and orders.created_at between '$valid_from' and '$valid_to'";
				$data = DB::select(DB::raw($ftlquery));
				break;
			case ROAD_TRUCK_LEASE:
			case RELOCATION_OFFICE_MOVE:
			case RELOCATION_GLOBAL_MOBILITY:
				$citymapping = ($serviceId==RELOCATION_GLOBAL_MOBILITY) ? "to_city_id" : "from_city_id";
				$singlelocationquery = "SELECT b.username AS buyername, s.username AS sellername, orders.seller_pickup_date, orders.seller_delivery_date, orders.created_at, d.city_name AS fromlocation, orders.price AS invoiceamount, inv.service_tax_amount
								FROM `orders` 
								JOIN order_invoices AS inv ON inv.order_id = orders.id
								JOIN users AS b ON b.id = buyer_id
								JOIN users AS s ON s.id = seller_id
								JOIN lkp_cities AS d ON d.id = $citymapping
								WHERE lkp_service_id =$serviceId
								and orders.created_at between '$valid_from' and '$valid_to'";
				$data = DB::select(DB::raw($singlelocationquery));
				break;
			case ROAD_PTL:
			case RAIL:
			case AIR_DOMESTIC:
			case COURIER:
				$ltlquery = "SELECT b.username AS buyername, s.username AS sellername, orders.seller_pickup_date, orders.seller_delivery_date, orders.created_at, concat(d.pincode,', ',d.postoffice_name)  AS fromlocation,concat(t.pincode,', ',t.postoffice_name) AS tolocation, orders.price AS invoiceamount, inv.service_tax_amount
									FROM `orders` 
									JOIN order_invoices AS inv ON inv.order_id = orders.id
									JOIN users AS b ON b.id = buyer_id
									JOIN users AS s ON s.id = seller_id
									JOIN lkp_ptl_pincodes AS d ON d.id = from_city_id
									JOIN lkp_ptl_pincodes AS t ON t.id = to_city_id
									WHERE lkp_service_id =$serviceId
									and orders.created_at between '$valid_from' and '$valid_to'";
				$data = DB::select(DB::raw($ltlquery));
				break;
			case AIR_INTERNATIONAL:
				$airintquery = "SELECT b.username AS buyername, s.username AS sellername, orders.seller_pickup_date, orders.seller_delivery_date, orders.created_at, d.airport_name  AS fromlocation,t.airport_name AS tolocation, orders.price AS invoiceamount, inv.service_tax_amount
										FROM `orders` 
										JOIN order_invoices AS inv ON inv.order_id = orders.id
										JOIN users AS b ON b.id = buyer_id
										JOIN users AS s ON s.id = seller_id
										JOIN lkp_airports AS d ON d.id = from_city_id
										JOIN lkp_airports AS t ON t.id = to_city_id
										WHERE lkp_service_id =$serviceId
										and orders.created_at between '$valid_from' and '$valid_to'";

				$data = DB::select(DB::raw($airintquery));
				break;

			case OCEAN:
				$oceanquery = "SELECT b.username AS buyername, s.username AS sellername, orders.seller_pickup_date, orders.seller_delivery_date, orders.created_at, concat(d.seaport_name,', ',d.country_name)  AS fromlocation,concat(t.seaport_name,', ',t.country_name) AS tolocation, orders.price AS invoiceamount, inv.service_tax_amount
										FROM `orders` 
										JOIN order_invoices AS inv ON inv.order_id = orders.id
										JOIN users AS b ON b.id = buyer_id
										JOIN users AS s ON s.id = seller_id
										JOIN lkp_seaports AS d ON d.id = from_city_id
										JOIN lkp_seaports AS t ON t.id = to_city_id
										WHERE lkp_service_id =$serviceId
										and orders.created_at between '$valid_from' and '$valid_to'";
				echo $oceanquery;die;
				$data = DB::select(DB::raw($oceanquery));

				break;

			default:

		}
		return $data;


	}
}