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
use App\Models\IctBuyerQuote;
use App\Models\IctBuyerQuoteItem;
use Log;

class SellerintracityController extends Controller {
	/**
	 * Create a new SellerintracityController instance.
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
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$cities = \DB::table ( 'lkp_cities as lc' )->leftJoin ( 'lkp_localities as ll', 'lc.id', '=', 'll.lkp_city_id' )->leftJoin ( 'seller_intracity_localities as sil', 'll.id', '=', 'sil.lkp_locality_id' )->select ( 'lc.*' )->where ( 'sil.user_id', '=', $this->user_pk )->distinct ( 'lc.city_name' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		
		$vehicleType = \DB::table ( 'lkp_vehicle_types as lvt' )->orderBy ( 'vehicle_type', 'asc' )->lists ( 'vehicle_type', 'id' );
		
		return view ( 'intracity/sellers.create_post', array (
				'cities' => $cities,
				'vehicleType' => $vehicleType 
		) );
	}
	
	/**
	 * Selects locality list when user selects city
	 */
	public function loadIntraLocality() {
		Log::info ( 'Ajax call to dependent intracityLocality:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "GET_INTRACITY_FROM_LOCALITY", GET_INTRACITY_FROM_LOCALITY, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ["cities"] ) && $_POST ["cities"] != '') {
			$cityId = $_POST ["cities"];
			
			$localityList = DB::table ( 'lkp_localities' )->where ( 'lkp_city_id', $cityId )->lists ( 'locality_name', 'id' );
			
			$str = '';
			foreach ( $localityList as $k => $v ) {
				
				$str .= '<option value = "' . $k . '">' . $v . '</option>';
			}
			echo $str;
		}
	}
	
	
}
