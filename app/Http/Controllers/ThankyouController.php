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
use App\Models\User;
use Log;

class ThankyouController extends Controller {
	
	/**
	 * Create a new Thankyou controller instance.
	 *
	 * @return void
	 */
	public $user_pk;
	public $randomId;
	public function __construct() {
		
		if (isset ( Auth::User ()->id )) {
			$this->user_pk = Auth::User ()->id;
		} else if (Session::get ( 'user_id' ) != '') {
			$this->user_pk = Session::get ( 'user_id' );
		}
		$this->randomId = rand ( 100, 999 );
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		Log::info ( 'User has been successfully redirected to Thankyou Page:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		return view ( 'thankyou.index');
	}
	
	/**
	 * Display a payment page for seller
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function seller_thanx() {
		Log::info ( 'Seller has been successfully redirected to Payment Page:' . $this->user_pk, array (
				'c' => '1' 
		) );
		DB::table('users')->where('id',$this->user_pk)->update(['is_business'=>1]);
		return view ( 'thankyou.seller_pay' );
	}
	
	/**
	 * function to add seller subscription start and end date
	 * in seller and seller_details tables
	 */
	public function addSubscription($startDate, $endDate, $is_business) {

			$tableName = 'seller_details';
		
		
	try{	DB::table ( $tableName )->where ( 'user_id', $this->user_pk )->update ( array (
				'subscription_start_date' => $startDate,
				'subscription_end_date' => $endDate 
		) );
	return 1;
	}
	catch(Exception $ex){}
	}
	
	/**
	 * send confirmation mail to seller
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sellerConfirm() {
		Log::info ( 'Seller has confirmed the payment:' . $this->user_pk, array (
				'c' => '1' 
		) );
		if (isset ( $_POST ['time'] ) && $_POST ['time'] != '') {
			$timePeriod = $_POST ['time'];
			$subscriptionStartsAt = date ( 'Y-m-d H:i:s' );
			$subscriptionEndsAt = '';
			
			if ($timePeriod == 'quarterPeriod') {
				$subscriptionEndsAt = date ( 'Y-m-d H:i:s', strtotime ( '+3 months' ) );
			} else if ($timePeriod == 'halfannualPeriod') {
				$subscriptionEndsAt = date ( 'Y-m-d H:i:s', strtotime ( '+6 months' ) );
			} else if ($timePeriod == 'annualPeriod') {
				$subscriptionEndsAt = date ( 'Y-m-d H:i:s', strtotime ( '+1 years' ) );
			} else if ($timePeriod == 'phantomPeriod') {
				$subscriptionEndsAt = date ( 'Y-m-d H:i:s', strtotime ( '+5 years' ) );
			} else if ($timePeriod == 'freeTrail') {
				$subscriptionEndsAt = date("2016-12-31 00:00:00");
			}
			
			$userRecord = \DB::table ( 'users' )->where ( 'id', '=', $this->user_pk )->first ();
			$is_business = $userRecord->is_business;
			
			// add subscription start and end date to seller
			$subscription = ThankyouController::addSubscription ( $subscriptionStartsAt, $subscriptionEndsAt, $is_business );
		if($subscription==1){
			CommonComponent::activityLog ( "SELLER_CONFIRM_PAYMENT", SELLER_CONFIRM_PAYMENT, 0, HTTP_REFERRER, CURRENT_URL );
			$stored_uid = $this->user_pk;
				
			try{
				if(isset(Auth::User()->lkp_role_id) && (Auth::User()->lkp_role_id == '1')){
					
					DB::table ( 'users' )->where ( 'id', $this->user_pk )->update ( array (
					'is_active' => 1,
					'is_confirmed' => 1,
					'is_approved' => 1,
					'secondary_role_id'=>'2',
					'is_buyer_paid'=>1,
					'mail_sent' => 1
					) );
					Session::put('last_login_role_id','2');
				}
				else{
			DB::table ( 'users' )->where ( 'id', $this->user_pk )->update ( array (
			'lkp_role_id' => 2,
			'is_active' => 1,
			'is_confirmed' => 1,
			'is_approved' => 1,
			'is_buyer_paid'=>1,
			'mail_sent' => 1
			) );
				}
			}catch(Exception $ex){
			}
			// Information email to seller after payment
				
			$userData = DB::table ( 'users' )->where ( 'id', $this->user_pk )->select ( 'users.*' )->get ();
				
			CommonComponent::send_email ( SELLER_PAYMENT_INFO_MAIL, $userData );
			
		}
			
		}
	}
}
