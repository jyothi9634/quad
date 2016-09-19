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
use Hash;
use Intervention\Image\ImageManagerStatic as Image;
use Response;

class RegisterController extends Controller {
	/*
	 * |--------------------------------------------------------------------------
	 * | Registration & Login Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller handles the registration of new users, as well as the
	 * | authentication of existing users. By default, this controller uses
	 * | a simple trait to add these behaviors. Why don't you explore it?
	 * |
	 */
	
	use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;
	
	/**
	 * Create a new Register controller instance.
	 *
	 * @return void
	 */
	public $user_pk;
	public $randomId;
	public function __construct(BuyerDetail $buyerDetail) {
		if (isset ( Auth::User ()->id )) {
			$this->user_pk = Auth::User ()->id;
		} elseif (Session::get ( 'user_id' ) != '') {
			$this->user_pk = Session::get ( 'user_id' );
		}
		$this->randomId = rand ( 100, 999 );
		$this->buyerDetail = $buyerDetail;
	}
	
	/**
	 * application home page.
	 * Displays some user specific screen
	 *
	 * @return Response
	 */
	public function home($service = null) {
		if (isset ( Auth::User ()->id ) && Auth::User ()->id != '') {
			return Redirect ( '/home' );
		}else{
			return view ( 'home.home' );
		}
	}
	
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param array $data        	
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		Log::info ( 'Validator function called for the anonymous user:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		return Validator::make ( $data, [ 
				'phone' => 'max:20|unique:users',
				'email' => 'email|max:100|unique:users',
				'password' => 'required|confirmed|min:4' 
		] );
	}
	
	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param array $data        	
	 * @return User
	 */
	protected function create(array $data) {
		Log::info ( 'Create function called for the anonymous user:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "USER_CREATE", USER_CREATE, 0, HTTP_REFERRER, CURRENT_URL );
		
		$users = new User ();
		$userEmail = $data ['user_email'];

		if (is_numeric ( $userEmail )) {
			$coloumn = 'email';
			$option = 1;
		} elseif (strpos ( $userEmail, '@' )) {
			$coloumn = 'email';
			$option = 2;
		}

		$isExist = RegisterController::checkUnique ( $userEmail, $option );
		if ($isExist !== "200") {
			return 401;
		}
		
		if (isset ( $data ['provider'] )) {
			if ($data ['provider'] == "facebook") {
				$users->fb_identifier = $data ['identifier'];
				$users->is_facebook = '1';
			} elseif ($data ['provider'] == "linkedin") {
				$users->linkedin_identifier = $data ['identifier'];
				$users->is_linkedin = '1';
			} elseif ($data ['provider'] == "google") {
				$users->google_identifier = $data ['identifier'];
				$users->is_google = '1';
			}
			if (isset ( $data ['username'] )) {
				$users->username = $data ['username'];
			}
		}
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$users->$coloumn = $userEmail;
		$users->phone = $data ['phone'];
		$users->password = bcrypt ( $data ['password'] );
		$users->lkp_role_id = '1';
		$users->primary_role_id = '1';
		$users->is_business = $data ['is_business'];
		$users->created_at = $createdAt;
		$users->created_ip = $createdIp;
		
		if($coloumn == "phone"){
			$users->is_active = '1';
			$users->is_confirmed = '1';
			$users->is_approved = '1';
			$users->is_buyer_paid = '1';
		}
		
		if ($users->save ()) {
			
			$newRole = RegisterController::sendActivationMail ( '1' , $users );
			// Maintaining a log of data for buyer quotes
			CommonComponent::auditLog ( $users->id, 'users' );
			
			$lastInsertedId = $users->id;
			
			return '1';
		} else {
			return '0';
		}
	}
	
	/**
	 * Registration.
	 */
	protected function register() {
		if (isset ( Auth::User ()->id )) {
			return Redirect ( '/home' );
		} else {
			
			if (( Input::all ()) || isset ( $_POST ['submitRegister'])) {
				
				Log::info ( 'Anonymous user tries to register by posting registration form:' . $this->randomId, array (
						'c' => '1' 
				) );
				CommonComponent::activityLog ( "USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL );
				
				new User ();
				
				$data = Input::only ( [ 
						'user_email',
						'password',
						'is_business',
						'phone'
				] );
				RegisterController::validator ( $data );
				
				

				$newUser = RegisterController::create ( $data );
				
				
				if ($newUser == 1) {
					
					return Redirect ( '/thankyou' )->with ( 'message', 'Member registration created successfully.' );
				} elseif ($newUser == 401) {
					return Redirect ( '/register' )->with ( 'message', 'Email already exist' );
				} 

				elseif ($newUser == 0) {
					$otp = '';
					return Redirect ( 'auth.register', array (
							'otp' => $otp 
					) )->withInput ();
				} else {
					
					return view ( 'auth.register', array (
							'otp' => $newUser 
					) );
				}
			}
			CommonComponent::activityLog ( "USER_REGISTERATION_DISPLAY", USER_REGISTERATION_DISPLAY, 0, HTTP_REFERRER, CURRENT_URL );
			
			$otp = '';
			// Session::put ( 'user_id', '' );
			return view ( 'auth.register', array (
					'otp' => $otp 
			) );
		}
	}
	
	/*
	|
	| Method: checkExistence
	| Purpose: check email and mobile are already exists or not, This service is used in jquery
	| return : true || false
	|
	*/
	public function checkExistence() {
		if(isset($_POST ['user_email'])){
			$userEmail = $_POST ['user_email'];
			$field = 'email';
		} else {
			$userEmail = $_POST ['phone'];
			$field = 'phone';
		}
		$exists = User::where ( $field, $userEmail )->get ();

			if (count ( $exists ) > 0) {

				return "false";
			} else {

				return "true";
			}
	}
	
	/*
	|
	| Method: Individual Registeration
	| Purpose: After user member registration continue to next step
	| return: true || false
	|
	*/
	public function individualRegistration() {
		$userRes = User::where ( 'id', $_REQUEST['user_id'] )->first();
		
		return view("auth.individual_register")->with('user',$userRes);
	}
	
	/*
	|
	| Method: save Individual 
	| Purpose: save individual registration
	| return: display confirmation dialog
	|
	|
	*/
	public function saveIndividual() {
		
		try{
			
          $registration_fields = Input::all();
		  
		  $finalArray = [
						'user_id' => $registration_fields['user_id'],
						'firstname' => $registration_fields['firstname'],
						'lastname' => $registration_fields['lastname'],
						'pincode' => $registration_fields['pincode'],
						'lkp_location_id' => $registration_fields['lkp_location_id'],
						'lkp_city_id' => $registration_fields['lkp_city_id'],
						'lkp_district_id' => $registration_fields['lkp_district_id'],
						'lkp_state_id' => $registration_fields['lkp_state_id'],
						'address1' => $registration_fields['address1'],
						'address2' => $registration_fields['address2'],
						'landline' => $registration_fields['landline'],
						'alternative_mobile' => $registration_fields['alternative_mobile'],
						'id_proof' => $registration_fields['id_proof'],
						'id_proof_value' => $registration_fields['id_proof_value']
						];
		  
		  DB::table ( 'buyer_details' )->insert($finalArray);
		  DB::table ( 'users' )->where('id',$registration_fields['user_id'])->update(['is_active'=>1]);
		  
		  session::put('user_id',$registration_fields['user_id']);
		  
		  return view("/auth.regConfirmation");
          
      } catch (Exception $ex) {
          $message = $ex->getMessage();
      }
	}

	public function marketplaceRegistration() {
		//session
		
		$services =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path' , 'id')->get();
		
		$intracity_cities_list = \DB::table ( 'lkp_cities as lc' )
						->join('lkp_ict_locations as ictl','ictl.lkp_city_id','=','lc.id')
						->join('lkp_localities as ictlt','ictlt.id','=','ictl.lkp_locality_id')
						->orderBy ( 'lc.city_name', 'asc' )
						->select ( 'lc.city_name', 'lc.id' )->get();
						
						for($k=0;$k<count($intracity_cities_list);$k++){
							$intracity_cities[$intracity_cities_list[$k]->id]= $intracity_cities_list[$k]->city_name;
						}
		$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );

		$business = \DB::table ( 'lkp_business_types' )->orderBy ( 'business_type_name', 'asc' )->lists ( 'business_type_name', 'id' );
		
		$lkp_industry = $this->getIndustries();
		
		$getEmployeeStrengths = $this->getEmployeeStrengths();
						
		return view("auth.marketplace_register",compact('services','intracity_cities','stateList','business','lkp_industry','getEmployeeStrengths'));
	}


	public function storeMarketplaceDetails() {
		
		$data = Input::all();
		//dd($data);
		Log::info ( 'User has submitted seller individual registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CREATE_SELLER_INDIVIDUAL", CREATE_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		
		$getPrincipalPlace = DB::table('lkp_ptl_pincodes as lpp')
		->where('lpp.pincode', '=', $data ['business_pincode'])
		->select('lpp.lkp_district_id','lpp.id','lpp.divisionname','lpp.state_id','lpp.postoffice_name')
		->first();
		
		//$sellerUpladFolder = 'uploads/seller/' . $this->user_pk . '/';
		//$data ['profile_picture_file'] = str_replace($sellerUpladFolder,"",$data ['profile_picture']);
		//$data ['logo_user_file'] = str_replace($sellerUpladFolder,"",$data ['logo_user']);		
		
		$sellerIndividual = new Seller();
		$services = array ();
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$sellerIndividual->user_id = $this->user_pk;
		$sellerIndividual->name = $data ['business_name'];
		$sellerIndividual->uin_number = $data ['cin_no'];
		$sellerIndividual->lkp_location_id = $data ['business_pincode'];
		$sellerIndividual->pincode = $data ['business_pincode'];
		//$sellerIndividual->lkp_city_id = $data ['business_city'];
		$sellerIndividual->lkp_state_id = $getPrincipalPlace->state_id;
		$sellerIndividual->lkp_district_id = $getPrincipalPlace->lkp_district_id;
		$sellerIndividual->lkp_country_id = 1;
		$sellerIndividual->address1 = $data ['address1'];
		$sellerIndividual->address2 = $data ['address2'];
		$sellerIndividual->established_in = $data ['year_of_est'];
		$sellerIndividual->contact_firstname = $data ['contact_fname'];
		$sellerIndividual->contact_lastname = $data ['contact_lname'];
		$sellerIndividual->lkp_employee_strength_id = 1; //$data ['employee_strn'];
		$sellerIndividual->contact_email = $data ['business_emailId'];
		$sellerIndividual->contact_mobile = $data ['business_mobile_no'];
		$sellerIndividual->contact_landline = $data ['business_landline'];
		$sellerIndividual->lkp_business_type_id = 1; //$data ['business_type_id'];
		$sellerIndividual->lkp_industry_id = 1;//$data ['industry_type_name'];
		$sellerIndividual->service_tax_number = $data ['service_taxno'];
		$sellerIndividual->pannumber = $data ['business_pan'];
		$sellerIndividual->tin = $data ['tin_no'];
		$sellerIndividual->current_turnover = $data ['current_turnover'];
		$sellerIndividual->first_year_turnover = $data ['first_year_turnover'];
		$sellerIndividual->second_year_turnover = $data ['second_year_turnover'];
		$sellerIndividual->third_year_turnover = $data ['third_year_turnover'];
		
		$sellerIndividual->bankname = $data ['bank_name'];
		$sellerIndividual->branchname = $data ['bank_branch'];
		$sellerIndividual->gta = $data ['gta_number'];
		//$sellerIndividual->joining_year = date('Y');
		
		$sellerIndividual->ifsc_code = $data ['ifsc_code'];
		$sellerIndividual->bank_acc_no = $data ['account_no'];
		$sellerIndividual->branchname = $data ['account_branch'];
		/*$sellerIndividual->bankname = $data ['bankname'];
		$sellerIndividual->branchname = $data ['branchname'];*/
		//$sellerIndividual->in_corporation_file = $data ['in_corporation_file'];
		//$sellerIndividual->tin_filepath = $data ['tin_filepath'];
		//$sellerIndividual->gta_filepath = $data ['gta_filepath'];
		//$sellerIndividual->pancard_filepath = $data ['pancard_filepath'];
		//$sellerIndividual->service_tax_filepath = $data ['service_tax_filepath'];
		//$sellerIndividual->central_excise_filepath = $data ['central_excise_filepath'];
		//$sellerIndividual->sales_tax_filepath = $data ['sales_tax_filepath'];
		$sellerIndividual->created_at = $createdAt;
		$sellerIndividual->created_ip = $createdIp;
		$sellerIndividual->created_by = $this->user_pk;
		
		try {
			if ($sellerIndividual->save ()) {
				$marketPlaceid = $sellerIndividual->id;
				$updateUserInfo = ['marketplace_id'=>$marketPlaceid,'is_business'=>1,'is_admin'=>0,'business_email_id'=>$data['business_emailId']];
				if(in_array($data['business_const'],[2,3,4])){ // if business const public private mnc then is_admin=1
					$updateUserInfo['is_admin'] =  1;
				}
				DB::table('users')->where('id',$this->user_pk)->update($updateUserInfo);
				CommonComponent::auditLog ( $sellerIndividual->id, 'sellers' );
				Session::put ( 'company_name', $sellerIndividual->name ); // session for future use
				$intracityArea = array ();
				$pamArea = array ();
				$services = array ();
				
				// see if value has been posted
			if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {
					$services = $_POST ['services'];
					$seller_services = $services;
					
				}
				if (! empty ( $seller_services )) {
					foreach ( $seller_services as $service ) {
						
						$seller_services_save = new SellerService ();
						$seller_services_save->user_id = $this->user_pk;
						$seller_services_save->lkp_service_id = $service;
						$seller_services_save->created_by = $this->user_pk;
						$seller_services_save->created_at = $createdAt;
						$seller_services_save->created_ip = $createdIp;
						$seller_services_save->save ();
						CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
					}
				}
				if (! empty ( $_POST ['intracity_locality'] [0] ) && $_POST ['intracity_locality'] [0] != '') {
					
					$intracityArea = $_POST ['intracity_locality'];
					
					foreach ( $intracityArea as $intracity ) {
						
						$intracityArea_save = new SellerIntracityLocality ();
						$intracityArea_save->user_id = $this->user_pk;
						$intracityArea_save->lkp_locality_id = $intracity;
						$intracityArea_save->created_by = $this->user_pk;
						$intracityArea_save->created_at = $createdAt;
						$intracityArea_save->created_ip = $createdIp;
						
						$intracityArea_save->save ();
						
						CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
					}
				}
				if (! empty ( $_POST ['pm_city'] [0] ) && $_POST ['pm_city'] [0] != '') {
					
					$pamArea = $_POST ['pm_city'];
					foreach ( $pamArea as $pam ) {
						
						$pam_save = new SellerPmCity ();
						$pam_save->user_id = $this->user_pk;
						$pam_save->lkp_city_id = $pam;
						$pam_save->created_by = $this->user_pk;
						$pam_save->created_at = $createdAt;
						$pam_save->created_ip = $createdIp;
						
						$pam_save->save ();
						
						CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
					}
				}
				$username = $sellerIndividual->firstname . " " . $sellerIndividual->lastname;
				
			
				User::where ( "id", $this->user_pk )->update ( array (
						'username' => $username,
						'lkp_role_id' => '2',
						'primary_role_id'=>'2',
						//'user_pic'=>$data ['profile_picture_file'],
						//'logo'=>$data ['logo_user_file'],
						'pannumber'=>$data ['business_pan'],
						'updated_at' => $createdAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $createdIp 
				) );
				
				CommonComponent::auditLog ( $this->user_pk, 'users' );
				
				# if user selected serverce offed and if he selected any one of service redirect to subscritionpage
				  # else redirect to thankyou registration page.
				  if(count($seller_services>0)) {
					  return Redirect('thankyou_seller');
				  } else {
					  return view('auth.regConfirmation');
				  }
					

			} else {
				return '0';
			}
		} catch ( Exception $ex ) {
		}
	}
	
	protected function checkUnique($userEmail = '', $optionValue = '') {

		Log::info ( 'Email / Phone uniqueness check triggers for Anonymous user :' . $this->randomId, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "USER_UNIQUE", USER_UNIQUE, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ['optionValue'] ) && $_POST ['optionValue'] != '') {
			$optionValue = $_POST ['optionValue'];
		}
		
		if (isset ( $_POST ['user_email'] ) && $_POST ['user_email'] != '') {
			
			$userEmail = $_POST ['user_email'];
		}
		if (strpos ( $userEmail, '@' )) {
			$emailExist = User::where ( 'email', $userEmail )->get ();

			if (count ( $emailExist ) > 0) {

				return "Email already exist";
			} else {

				return "200";
			}
		}
		if ($optionValue == 1) {

			
			if (is_numeric ( $userEmail )) {
				
				$phoneExist = User::where ( 'phone', $userEmail )->get ();

				if (count ( $phoneExist ) > 0) {
					return "Mobile number already exist";
				} else {
					return "200";
				}
			}
		}
	}
	
	/**
	 * Validate the given OTP

	public function validateotp() {
		Log::info ( 'Anonymous user tries validate OTP:' . $this->user_pk, array (
				'c' => '1' 
		) );

		CommonComponent::activityLog ( "VALIDATE_OTP", VALIDATE_OTP, 0, HTTP_REFERRER, CURRENT_URL );
		
		$inputOtp = '';
		if (! empty ( Input::all () )) {
			
			$data = Input::only ( 'otp' );
			// print_r($data);die();
			$inputOtp = $data ['otp'];
		}
		// echo $this->user_pk;die();
		$otpRecord = UserOtp::where ( 'user_id', '=', $this->user_pk )->first ();
		
		$savedOtp = $otpRecord->otp;
		
		if ($inputOtp == $savedOtp) {
			
			return Redirect ( 'register/buyer' )->with ( 'message', 'Registered successfully.' );
		} else {
			try {
				$deleteOtp = DB::table ( 'user_otps' )->where ( 'user_id', $this->user_pk )->delete ();
				
				$deleteUser = DB::table ( 'users' )->where ( 'id', $this->user_pk )->delete ();
				
				if ($deleteUser == 1 && $deleteOtp == 1) {
					Session::flush (); // unset $_SESSION variable for the run-time
					                   // destroy session data in storage
					return Redirect ( '/register' )->with ( 'message', 'Incorrect OTP' );
				} else {
					Session::flush (); // unset $_SESSION variable for the run-time
					                   // destroy session data in storage
					return Redirect ( '/register' )->with ( 'message', 'Incorrect OTP' );
				}
			} catch ( Exception $ex ) {
			}
		}
	}*/
	
	/**
	 * Display Individual Buyer Details form
	 */
	public function buyer() {
		
		Log::info ( 'User visits individual buyer page:' . $this->user_pk, array ('c' => '1' ) );
		CommonComponent::activityLog ( "DISPALY_BUYER", DISPALY_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
		$lkp_industry = $this->getIndustries();
		$userRecord = User::where ( 'id', '=', $this->user_pk )->first ();
		$is_buyer = $userRecord ['is_business'];
		$user_role = $userRecord ['lkp_role_id'];
		$user_email = '';
		$user_phone = '';
		if ($userRecord ['email'] != null) {
			$user_email = $userRecord ['email'];
		} elseif ($userRecord ['phone'] != null) {
			$user_phone = $userRecord ['phone'];
		}
		if ($user_role == '1') {
			if ($is_buyer == 0) {
				$buyerRecord = BuyerDetail::where ( 'user_id', '=', $this->user_pk )->first ();
			
				if (empty ( $buyerRecord )) {
					
					return view ( 'auth.buyer', array (
							'user_phone' => $user_phone,
							'user_email' => $user_email,
							'lkp_industry' => $lkp_industry
					) );
				} else {
					
					return Redirect ( 'auth/login' )->with ( 'message', 'Please verify your email' );
				}
			} elseif ($is_buyer == 1) {
				
				return Redirect ( 'register/buyer_business' );
			}
		} else {
			return Redirect ( 'register/seller' );
		}
	}
	
	/** Retrieval of all Industries**/
	public static function getIndustries()
	{
		try
		{
			$lkp_industries = DB::table('lkp_industries')->orderBy('industry_name', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('industry_name', 'id');
			return $lkp_industries;
	
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of all getSpecialities**/
	public static function getSpecialities()
	{
		try
		{
			$lkp_specialities = DB::table('lkp_specialities')->orderBy('speciality_name', 'asc' )->where('is_active','=',IS_ACTIVE)->lists('speciality_name', 'id');
			return $lkp_specialities;
	
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/** Retrieval of all getEmployeeStrengths**/
	public static function getEmployeeStrengths()
	{
		try
		{
			$lkp_employee_strengths = DB::table('lkp_employee_strengths')->where('is_active','=',IS_ACTIVE)->lists('employee_strength', 'id');
			return $lkp_employee_strengths;
	
		}
		catch(\Exception $e)
		{
			//return $e->message;
		}
	}
	
	/**
	 * Buyer Registration
	 *
	 * @param $data array        	
	 */
	public function createBuyer(array $data) {
		Log::info ( 'Create individual buyer function triggered by User:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CREATE_BUYER", CREATE_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
		
		$buyerBusinessDirectory = 'uploads/buyer/' . $this->user_pk . '/';
		
		if (is_dir ( $buyerBusinessDirectory )) {
		} else {
				
			mkdir ( $buyerBusinessDirectory, 0777, true );
		}
		
		
		//echo "<pre>";print_r($_FILES);
		//echo "=======================================================================";
		//echo "<pre>";print_r($data);exit;
		if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
			$file = 'profile_picture';
				
			$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
			$data ['profile_picture'] = $uploadedFile;
		} else {
			$data ['profile_picture'] = '';
		}
		
		if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
			$file = 'logo_user';
		
			$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
			$data ['logo_user'] = $uploadedFile;
		} else {
			$data ['logo_user'] = '';
		}
		
		$data ['profile_picture_file'] = str_replace($buyerBusinessDirectory,"",$data ['profile_picture']);
		$data ['logo_user_file'] = str_replace($buyerBusinessDirectory,"",$data ['logo_user']);
		
		$buyerDetails = new BuyerDetail ();
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$buyerDetails->user_id = $this->user_pk;
		$buyerDetails->firstname = $data ['firstname'];
		$buyerDetails->lastname = $data ['lastname'];
		$buyerDetails->mobile = $data ['mobile'];
		$buyerDetails->landline = $data ['landline'];
		$buyerDetails->contact_email = $data ['contact_email'];
		$buyerDetails->address = $data ['address'];
		$buyerDetails->pincode = $data ['pincode'];
		$buyerDetails->principal_place = $data ['principal_place'];
		$buyerDetails->lkp_industry_id = $data ['lkp_industry'];
		$buyerDetails->name = $data ['company_name'];
		$buyerDetails->description = $data ['description_user'];
		$buyerDetails->created_at = $createdAt;
		$buyerDetails->created_ip = $createdIp;
		$buyerDetails->created_by = $this->user_pk;
		
		if ($buyerDetails->save ()) {
			CommonComponent::auditLog ( $buyerDetails->id, 'buyer_details' );
			
			$username = $buyerDetails->firstname . " " . $buyerDetails->lastname;
			User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'lkp_role_id' => '1',
					'primary_role_id' => '1',
					'user_pic'=>$data ['profile_picture_file'],
					'logo'=>$data ['logo_user_file'],
					'pannumber'=>$data ['pannumber'],
					'updated_at' => $createdAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $createdIp 
			) );
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			
			Session::put ( 'buyerName', $username );
			
			return '1';
		} else {
			return '0';
		}
	}
	public function registerBuyer() {
		Log::info ( 'User submits buyer registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		CommonComponent::activityLog ( "BUYER_REGISTRATION", BUYER_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		
		$data = Input::all ();
		$newRole = RegisterController::selectRole ( '1', $data );
		if ($newRole == '1') {
			$newBuyer = RegisterController::createBuyer ( $data );
		} else {
			return Redirect ( 'register/buyer' );
		}
		if ($newBuyer == 1) {
			
			return Redirect ( '/thankyou' )->with ( 'message', 'Buyer information submitted successfully.' );
		} else {
			return Redirect ( 'register/buyer' );
		}
	}
	
	/**
	 * Edit profile for individual buyer
	 *
	 * @param $id(User id)        	
	 *
	 */
	public function viewEditBuyer() {
		Log::info ( 'User viewed Edit buyer form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPLAY_BUYER", DISPLAY_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
		
		$buyer_details = DB::table ( 'buyer_details' )
						/** Start : @jagadeesh - 02/05/2016 
						 *Reg : Getting User uploaded logo and user picture details
						 */
							->join('users as u','u.id','=','user_id')
							->select('buyer_details.*','u.logo','u.user_pic','u.pannumber')	
						/** 
						 *End : @jagadeesh - 02/05/2016 
						 */
						->where ( 'user_id', '=', $this->user_pk )
						->first ();
		$buyer_id = $buyer_details->id;
		$lkp_industry = $this->getIndustries();
		return view ( 'auth.edit_buyer', compact ( 'buyer_details' ), array (
				'buyer_id' => $buyer_id,
				'lkp_industry' => $lkp_industry
		) );
	}
	public function editBuyer($id, Request $request) {
		Log::info ( 'User Edited buyer details:' . $this->user_pk, array (
				'c' => '1' 
		) );
		CommonComponent::activityLog ( "EDIT_BUYER", EDIT_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			
			$updatedAt = date ( 'Y-m-d H:i:s' );
			$updatedIp = $_SERVER ['REMOTE_ADDR'];
			try {
				BuyerDetail::where ( "id", $id )->update ( array (
						'firstname' => $request->firstname,
						'lastname' => $request->lastname,
						'mobile' => $request->mobile,
						'landline' => $request->landline,
						'contact_email' => $request->contact_email,
						'address' => $request->address,
						'principal_place' => $request->principal_place,
						'name' => $request->company_name,
						'description' => $request->description_user,
						'lkp_industry_id' => $request->lkp_industry,
						'pincode' => $request->pincode,
						'updated_by' => $this->user_pk,
						'updated_at' => $updatedAt,
						'updated_ip' => $updatedIp 
				) );
				// log
				CommonComponent::auditLog ( $id, 'buyer_details' );
				$username = $request->firstname . " " . $request->lastname;
			/** Start : @jagadeesh - 02/05/2016 
			 *Reg : Saveing its uploaded logo / user picture details
			 */
			$buyerBusinessDirectory = BUYERUPLOADPATH . $this->user_pk . '/';
			
			if (is_dir ( $buyerBusinessDirectory )) {
			} else {
					
				mkdir ( $buyerBusinessDirectory, 0777, true );
			}

			if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
				$file = 'profile_picture';
				// Remove Previous uploaded file
				$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'user_pic');
					
				$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
				$data ['profile_picture'] = $uploadedFile;
			}else{
				$data ['profile_picture'] = '';
			} 
			
			if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
				$file = 'logo_user';
				// Remove Previous uploaded file
				$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'logo');
			
				$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
				$data ['logo_user'] = $uploadedFile;
			}else{
				$data ['logo_user'] = '';
			} 

				$update_array = array (
					'username' => $username,
					'pannumber' => $request->pannumber,
					'updated_at' => $updatedAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $updatedIp ,
				);

				if($data['profile_picture']){
					$update_array['user_pic'] = str_replace($buyerBusinessDirectory,"",$data ['profile_picture']);
				}

				if($data ['logo_user']){
					$update_array['logo'] = str_replace($buyerBusinessDirectory,"",$data ['logo_user']);
				}

				User::where ( "id", $this->user_pk )->update($update_array);
			/** 
			 *End : @jagadeesh - 02/05/2016 
			 */
			 /*User::where ( "id", $this->user_pk )->update ( array (
						'username' => $username,
						'updated_at' => $updatedAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $updatedIp ,
				) );*/
				CommonComponent::auditLog ( $this->user_pk, 'users' );
				return Redirect ( '/home' )->with ( 'edit_success_message', 'Buyer details are updated successfully.' );
			} catch ( Exception $ex ) {
			}
		}
	}
	
	/**
	 * Corporate(Business) buyer registration page
	 */
	public function buyerBusiness() {
		Log::info ( 'User viewed buyer_business page:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPLAY_BUYER_BUSINESS", DISPLAY_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$userRecord = User::where ( 'id', '=', $this->user_pk )->first ();
		$is_buyer = $userRecord ['is_business'];
		$user_role = $userRecord ['lkp_role_id'];
		$user_email = '';
		$user_phone = '';
		if ($userRecord ['email'] != null) {
			$user_email = $userRecord ['email'];
		} elseif ($userRecord ['phone'] != null) {
			$user_phone = $userRecord ['phone'];
		}
		if ($user_role == '1') {
			if ($is_buyer == 0) {
				return redirect ( '/register/buyer' );
			} elseif ($is_buyer == 1) {
				$businessBuyerRecord = BuyerBusinessDetail::where ( 'user_id', '=', $this->user_pk )->first ();
				if (empty ( $businessBuyerRecord )) {
					
					$state = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
					$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
					$business = \DB::table ( 'lkp_business_types' )->orderBy ( 'business_type_name', 'asc' )->lists ( 'business_type_name', 'id' );
					
					$lkp_industry = $this->getIndustries();
					$getSpecialities = $this->getSpecialities();
					$getEmployeeStrengths = $this->getEmployeeStrengths();
					$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016
					return view ( 'auth.buyer_business', array (
							'state' => $state,
							'country' => $country,
							'business' => $business,
							'user_phone' => $user_phone,
							'user_email' => $user_email,
							'lkp_industry' => $lkp_industry,
							'getSpecialities' => $getSpecialities,
							'getEmployeeStrengths' => $getEmployeeStrengths,
							'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016
					) );
				} else {
					return Redirect ( 'auth/login' )->with ( 'message', 'Please verify your email' );
				}
			}
		} else {
			return view ( 'register/seller' );
		}
	}
	
	/**
	 * Corporate(Business) buyer registration
	 */
	public function registerBusinessBuyer() {
		Log::info ( 'User submited  buyer_business registeration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "BUYER_BUSINESS_REGISTRATION", BUYER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		
		$data = Input::all ();
		
		$buyerBusinessDirectory = 'uploads/buyer/' . $this->user_pk . '/';
		
		if (is_dir ( $buyerBusinessDirectory )) {
		} else {
			
			mkdir ( $buyerBusinessDirectory, 0777, true );
		}
		
		
		if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
			$file = 'profile_picture';
				
			$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
			$data ['profile_picture'] = $uploadedFile;
		} else {
			$data ['profile_picture'] = '';
		}
		
		if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
			$file = 'logo_user';
		
			$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
			$data ['logo_user'] = $uploadedFile;
		} else {
			$data ['logo_user'] = '';
		}
		
		
		if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
			$file = 'in_corporation_file';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['in_corporation_file'] = $uploadedFile;
		} else {
			$data ['in_corporation_file'] = '';
		}
		if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
			$file = 'tin_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['tin_filepath'] = $uploadedFile;
		} else {
			$data ['tin_filepath'] = '';
		}
		if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
			$file = 'gta_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['gta_filepath'] = $uploadedFile;
		} else {
			$data ['gta_filepath'] = '';
		}
		if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
			$file = 'pancard_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['pancard_filepath'] = $uploadedFile;
		} else {
			$data ['pancard_filepath'] = '';
		}
		if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
			$file = 'service_tax_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['service_tax_filepath'] = $uploadedFile;
		} else {
			$data ['service_tax_filepath'] = '';
		}
		if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
			$file = 'central_excise_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['central_excise_filepath'] = $uploadedFile;
		} else {
			$data ['central_excise_filepath'] = '';
		}
		
		if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
			$file = 'sales_tax_filepath';
			
			$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
			$data ['sales_tax_filepath'] = $uploadedFile;
		} else {
			$data ['sales_tax_filepath'] = '';
		}
		
		$newRole = RegisterController::selectRole ( '1' , $data );
		if ($newRole == '1') {
			$newBuyer = RegisterController::createBusinessBuyer ( $data );
		} else {
			return Redirect ( 'register/buyer_business' );
		}
		
		if ($newBuyer == 1) {
			
			return Redirect ( '/thankyou' )->with ( 'message', 'Buyer details are submitted successfully.' );
		} else {
			return Redirect ( 'register/buyer_business' );
		}
	}
	
	/**
	 * file upload function
	 *
	 * @param $directory(filepath) &
	 *        	$file(filename)
	 * @return filepath on success;
	 *        
	 */
	public function checkUpload($directory, $file) {
		Log::info ( 'User tried to upload files:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CHECK_UPLOADS", CHECK_UPLOADS, 0, HTTP_REFERRER, CURRENT_URL );
		
		// for generating the random string to append with image name
		// Chars
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
		
		// Parametres
		$string_length = 6;
		$random_string = '';
		
		// Gererating a random string of length of 10
		for($i = 1; $i <= $string_length; $i ++) {
			$rand_number = rand ( 0, 59 );
			$random_string .= $chars [$rand_number];
		}
		
		try {
			$_FILES [$file] ['error'];
			$fileName = $_FILES [$file] ['name'];
			$uploadedFileName = pathinfo ( $fileName, PATHINFO_FILENAME );
			$extension = pathinfo ( $fileName, PATHINFO_EXTENSION );
			if (! is_array ( $fileName )) {
				
				$fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter ( $uploadedFileName );
				$uniqueFileName1 = $random_string . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
				
				if (move_uploaded_file ( $_FILES [$file] ['tmp_name'], $directory . $uniqueFileName1 )) {
					$file_path = $directory . $uniqueFileName1;
					return $file_path;
				}
			}
		} catch ( Exception $ex ) {
			// echo $ex;
			// die ();
		}
	}
	
	
	/**
	 * file upload function
	 *
	 * @param $directory(filepath) &
	 *        	$file(filename)
	 * @return filepath on success;
	 *
	 */
	public function checkUploadCrop($directory, $file) {
		Log::info ( 'User tried to upload files:' . $this->user_pk, array (
				'c' => '1'
		) );
	
		CommonComponent::activityLog ( "CHECK_UPLOADS", CHECK_UPLOADS, 0, HTTP_REFERRER, CURRENT_URL );
	
		// for generating the random string to append with image name
		// Chars
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	
		// Parametres
		$string_length = 6;
		$random_string = '';
	
		// Gererating a random string of length of 10
		for($i = 1; $i <= $string_length; $i ++) {
			$rand_number = rand ( 0, 59 );
			$random_string .= $chars [$rand_number];
		}
	
		try {
			$_FILES [$file] ['error'];
			$fileName = $_FILES [$file] ['name'];
			$uploadedFileName = pathinfo ( $fileName, PATHINFO_FILENAME );
			$extension = pathinfo ( $fileName, PATHINFO_EXTENSION );
			if (! is_array ( $fileName )) {
	
				$fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter ( $uploadedFileName );
				$uniqueFileName1 = $random_string . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
				$uniqueFileName_before = $random_string . "_" . $fileNameWithoutSpecialCharacter;
	
				if (move_uploaded_file ( $_FILES [$file] ['tmp_name'], $directory . $uniqueFileName1 )) {
					$file_path = $directory . $uniqueFileName1;
					
					$sizes = array(
							array(40,40),
							array(94,92),
							array(124,73),
							array(81,60),
							array(986,280),
					);
					foreach($sizes as $size){
						$img = Image::make(public_path($file_path));
						$img->resize($size[0], $size[1]);
						$nawimagename = $uniqueFileName_before."_".$size[0]."_".$size[1].".".$extension;
						$new_file_image = $directory . $nawimagename;
						$img->save(public_path($new_file_image));
					}
					
					return $file_path;
				}
			}
		} catch ( Exception $ex ) {
			// echo $ex;
			// die ();
		}
	}
	
	
	/**
	 *
	 * Saving business buyer details in database
	 *
	 * @param
	 *        	$data
	 *        	
	 *        	
	 */
	public function createBusinessBuyer(array $data) {
		Log::info ( 'Creat_buyer_business action is triggered by User:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CREATE_BUSINESS_BUYER", CREATE_BUSINESS_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
		
		
		$sellerUpladFolder = 'uploads/buyer/' . $this->user_pk . '/';

		if(isset($data ['profile_picture']) && $data ['profile_picture'] != ''){
			$data ['profile_picture_file'] = str_replace($sellerUpladFolder,"",$data ['profile_picture']);
		}else{
			$data ['profile_picture_file'] = '';
		}
		if(isset($data ['logo_user']) && $data ['logo_user'] != ''){
			$data ['logo_user_file'] = str_replace($sellerUpladFolder,"",$data ['logo_user']);
		}else{
			$data ['logo_user_file'] = '';
		}
				
		$buyerBusiness = new BuyerBusinessDetail ();
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$buyerBusiness->user_id = $this->user_pk;
		$buyerBusiness->name = $data ['name'];
		$buyerBusiness->lkp_business_type_id = $data ['lkp_business_type_id'];
		$buyerBusiness->lkp_country_id = $data ['lkp_country_id'];
		if ($buyerBusiness->lkp_business_type_id == 8) {
			$buyerBusiness->other_business_type = $data ['other_business_type'];
		}
		$buyerBusiness->description = $data ['description_user'];
		$buyerBusiness->lkp_state_id = $data ['lkp_state_id'];
		$buyerBusiness->principal_place = $data ['principal_place'];
		$buyerBusiness->address = $data ['address'];
		$buyerBusiness->pincode = $data ['pincode'];
		$buyerBusiness->current_turnover = $data ['current_turnover'];
		$buyerBusiness->first_year_turnover = $data ['first_year_turnover'];
		$buyerBusiness->second_year_turnover = $data ['second_year_turnover'];
		$buyerBusiness->third_year_turnover = $data ['third_year_turnover'];
		$buyerBusiness->contact_firstname = $data ['contact_firstname'];
		$buyerBusiness->contact_lastname = $data ['contact_lastname'];
		$buyerBusiness->contact_designation = $data ['contact_designation'];
		$buyerBusiness->contact_email = $data ['contact_email'];
		$buyerBusiness->contact_mobile = $data ['contact_mobile'];
		$buyerBusiness->contact_landline = $data ['contact_landline'];
		$buyerBusiness->established_in = $data ['established_in'];
		$buyerBusiness->lkp_employee_strength_id = $data ['employee_strengths'];
		$buyerBusiness->lkp_industry_id = $data ['lkp_industry'];
		$buyerBusiness->lkp_speciality_id = $data ['lkp_specialities'];
		$buyerBusiness->joining_year = date('Y');
		$buyerBusiness->gta = $data ['gta'];
		$buyerBusiness->tin = $data ['tin'];
		$buyerBusiness->service_tax_number = $data ['service_tax_number'];
		$buyerBusiness->bankname = $data ['bankname'];
		$buyerBusiness->branchname = $data ['branchname'];
		$buyerBusiness->in_corporation_file = $data ['in_corporation_file'];
		$buyerBusiness->tin_filepath = $data ['tin_filepath'];
		$buyerBusiness->gta_filepath = $data ['gta_filepath'];
		$buyerBusiness->pancard_filepath = $data ['pancard_filepath'];
		$buyerBusiness->service_tax_filepath = $data ['service_tax_filepath'];
		$buyerBusiness->central_excise_filepath = $data ['central_excise_filepath'];
		$buyerBusiness->sales_tax_filepath = $data ['sales_tax_filepath'];
		$buyerBusiness->created_at = $createdAt;
		$buyerBusiness->created_ip = $createdIp;
		$buyerBusiness->created_by = $this->user_pk;
		
		if ($buyerBusiness->save ()) {
			CommonComponent::auditLog ( $this->user_pk, 'buyer_business_details' );
			Session::put ( 'company_name', $buyerBusiness->name );
			$username = $data ['contact_firstname'].' '.$data ['contact_lastname'];
			User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'pannumber' => $data ['pannumber'],
					'lkp_role_id' => '1',
					'primary_role_id' => '1',
					'user_pic'=>$data ['profile_picture_file'],
					'logo'=>$data ['logo_user_file'],
					'updated_at' => $createdAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $createdIp 
			) );
			
			Session::put ( 'buyerName', $username );
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			return '1';
		} else {
			return '0';
		}
	}
	
	/**
	 * display selection screen
	 */
	public function selectUser() {
		Log::info ( 'User viewed select USER TYPE page :' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPLAY_SELECT_ROLE", DISPLAY_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL );
		
		$userRecord = User::where ( 'id', '=', $this->user_pk )->first ();
		$is_mailed = $userRecord ['mail_sent'];		
		$is_business = $userRecord ['is_business'];
		if ($is_mailed == 1) {
			if ($is_business == 1) {
				return redirect ( 'register/seller_business' );
			} elseif ($is_business == 0) {
				if (isset ( Auth::User ()->id )) {
					return redirect ( '/home' );
				} else {
					return redirect ( 'auth/login' );
				}
			}
		} elseif ($is_mailed == 0) {
			return view ( 'auth.select_user' );
		}
	}
	
	
	/**
	 * Choosing role will redirect the users accordingly
	 * & will save the appropriate details about users in user table
	 */
	public function tempRole() {
		Log::info ( 'User selected an option BUYER / SELLER temporary:' . $this->user_pk, array (
		'c' => '1'
				) );
	
		CommonComponent::activityLog ( "USER_SELECT_ROLE", USER_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL );
		// echo "swapna";die();
		if (isset ( $_POST ['user_role'] ) && $_POST ['user_role'] != '') {
				
			// getting role_id posted by user
			$roleId = $_POST ['user_role'];
			$userRecord = User::where ( 'id', '=', $this->user_pk )->first ();
				
			$is_business = $userRecord['is_business'];			
				
			DB::table ( 'users' )->where ( 'id', $this->user_pk )->update ( array (
			'lkp_role_id' => $roleId
			) );
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			$message ='';
			
			if ($roleId == 1) {
				$user = 'Buyer';
				if($is_business == '0'){
				$redirect = 'buyer';
				}else{
					$redirect = 'buyer_business';
				}
	
				
			} else {

				$selected = (isset($_POST['selection']) && ($_POST['selection'] == 'both')) ? "both" : "seller";
				$message ='';
				$user = 'Seller';
			if($is_business == '0'){
				$redirect = "seller?selected=$selected";
				}else{
					$redirect = "seller_business?selected=$selected";
				}
			}
				
			$msg_array = array (
					'message' => $message,
					'redirect' => $redirect
			);
			// print_r($msg_array);die();
			echo json_encode ( $msg_array );
		}
	}
	
	public function sendActivationMail($userRole,$data = NULL) {
		
		Log::info ( 'User selected an option BUYER / SELLER:' . $data->id, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "USER_SELECT_ROLE", USER_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $userRole ) && $userRole != '') {
			
			// getting role_id posted by user
			$roleId = $userRole;
			
			// for generating the random string
			// Chars
			$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
			
			// Parametres
			$string_length = 10;
			$random_string = '';
			
			// Gererating a random string of length of 10
			for($i = 1; $i <= $string_length; $i ++) {
				$rand_number = rand ( 0, 59 );
				$random_string .= $chars [$rand_number];
			}
			
			$stored_uid = $data->id;
			
			DB::table ( 'users' )->where ( 'id', $data->id )->update ( array (
					'lkp_role_id' => $roleId,
					'primary_role_id'=>$roleId,
					'mail_sent' => 1,
					'activation_key' => $random_string 
			) );
			CommonComponent::auditLog ( $data->id, 'users' );
			
			if ($roleId == 1) {
				$user = 'Buyer';
				$redirect = '/thankyou';
				// Mail functionality to send email to buyer
				$userData = DB::table ( 'users' )->where ( 'id', $data->id )->select ( 'users.*' )->get ();
				

				if ($userData [0]->email) {
					$activation_url = url () . '/user_activation?key=' . $random_string . '&u_id=' . $stored_uid . '&role_id=' . $roleId;
					$userData [0]->activation_url = $activation_url;
					CommonComponent::send_email ( BUYER_ACCOUNT_ACTIVATION_MAIL, $userData );
				} elseif ($userData [0]->phone) {
					$user = 'Buyer';
			
			$redirect = '/thankyou';
				}
				return 1;
			} else {
				return 1;
			}
		}
	}
	
	/**
	 * Choosing role will redirect the users accordingly
	 * & will save the appropriate details about users in user table
	 */
	public function selectRole($userRole,$data = NULL) {
		Log::info ( 'User selected an option BUYER / SELLER:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "USER_SELECT_ROLE", USER_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL );
		// echo "swapna";die();
		if (isset ( $userRole ) && $userRole != '') {
			
			// getting role_id posted by user
			$roleId = $userRole;
			
			// for generating the random string
			// Chars
			$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
			
			// Parametres
			$string_length = 10;
			$random_string = '';
			
			// Gererating a random string of length of 10
			for($i = 1; $i <= $string_length; $i ++) {
				$rand_number = rand ( 0, 59 );
				$random_string .= $chars [$rand_number];
			}
			
			$stored_uid = $this->user_pk;
			
			DB::table ( 'users' )->where ( 'id', $this->user_pk )->update ( array (
					'lkp_role_id' => $roleId,
					'primary_role_id'=>$roleId,
					'mail_sent' => 1,
					'activation_key' => $random_string 
			) );
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			
			if ($roleId == 1) {
				$user = 'Buyer';
				$redirect = '/thankyou';
				// Mail functionality to send email to buyer
				$userData = DB::table ( 'users' )->where ( 'id', $this->user_pk )->select ( 'users.*' )->get ();
				/**
				 * Start
				 * Passing First name and last name to email
				 * Jagadeesh - 03052016
				 */
					if($userData[0]->username){

					}else if(isset($data['firstname'])){
						$userData[0]->username = $data['firstname'] . " " . $data['lastname'];	
					}else {
						$userData[0]->username = $data['contact_firstname'] . " " . $data['contact_lastname'];	
					}
				/**
 				 * Jagadeesh - 03052016
				 * End 
				 */

				if ($userData [0]->email) {
					$activation_url = url () . '/user_activation?key=' . $random_string . '&u_id=' . $stored_uid . '&role_id=' . $roleId;
					$userData [0]->activation_url = $activation_url;
					CommonComponent::send_email ( BUYER_ACCOUNT_ACTIVATION_MAIL, $userData );
				} elseif ($userData [0]->phone) {
					$user = 'Buyer';
					$redirect = '/thankyou';
				}
				return 1;
			} else {
				return 1;
			}
		}
	}
	
	/**
	 * Selects state list when user selects country
	 */
	public function getState() {
		Log::info ( 'Ajax call to dependent statelist:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "GET_STATE", GET_STATE, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ["country_id"] ) && $_POST ["country_id"] != '') {
			$stateList = \DB::table ( 'lkp_states' )->where ( 'lkp_country_id', $_POST ["country_id"] )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
			
			$str = '<option value = "">Select State</option>';
			foreach ( $stateList as $k => $v ) {
				
				$str .= '<option value = "' . $k . '">' . $v . '</option>';
			}
			echo $str;
		}
	}
	
	/**
	 * Selects locality list when user selects city
	 */
	public function getIntraLocality() {
		Log::info ( 'Ajax call to dependent intracityLocality:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "GET_INTRACITY_LOCALITY", GET_INTRACITY_LOCALITY, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ["cities"] ) && $_POST ["cities"] != '') {
			$cityId = array ();
			$cities = $_POST ["cities"];
			$cityId = explode ( ",", $cities );
			
			$stateList = DB::table ( 'lkp_localities' )->whereIn ( 'lkp_city_id', $cityId )->lists ( 'locality_name', 'id' );
			// \DB::table('lkp_localities')->whereIn('lkp_city_id', $cityId)->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
			
			$str = '<option value = "">Select Locality</option>';
			foreach ( $stateList as $k => $v ) {
				
				$str .= '<option value = "' . $k . '">' . $v . '</option>';
			}
			echo $str;
		}
	}
	
	/**
	 * Selects locality list when user selects city
	 */
	public function getPaMCity() {
		Log::info ( 'Ajax call to dependent Packer&Movers Citylist:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "GET_PM_CITY", GET_PM_CITY, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ["stateList"] ) && $_POST ["stateList"] != '') {
			$stateId = array ();
			$states = $_POST ["stateList"];
			$stateId = explode ( ",", $states ); // making string as array
			
			$cityList = DB::table ( 'lkp_cities' )->whereIn ( 'lkp_state_id', $stateId )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
			// \DB::table('lkp_localities')->whereIn('lkp_city_id', $cityId)->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
			
			$str = '<option value = "">Select City</option>';
			foreach ( $cityList as $k => $v ) {
				
				$str .= '<option value = "' . $k . '">' . $v . '</option>';
			}
			echo $str;
		}
	}
	
	/**
	 * Render the seller business page with database drop downs
	 */
	public function sellerBusiness() {
		
		Log::info ( 'User viewed Seller business pages:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPALY_SELLER_BUSINESS", DISPALY_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$userRecord = User::where ( 'id', '=', $this->user_pk )->first ();
		$role_id = $userRecord ['lkp_role_id'];
		$is_seller = $userRecord ['is_business'];
		$is_active = $userRecord['is_active'];
		$user_email = '';
		$user_phone = '';
		if ($userRecord ['email'] != null) {
			$user_email = $userRecord ['email'];
		} elseif ($userRecord ['phone'] != null) {
			$user_phone = $userRecord ['phone'];
		}
		
		if ($role_id == '1') {
			return Redirect ( 'register/buyer' );
		} else {
			
			if ($is_seller == '0') {
				return Redirect ( 'register/seller' );
			} else {
				
				$sellerRecord = Seller::where ( 'user_id', '=', $this->user_pk )->first ();
				if (empty ( $sellerRecord )) {
					
					$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
					$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
					$business = \DB::table ( 'lkp_business_types' )->orderBy ( 'id', 'asc' )->lists ( 'business_type_name', 'id' );
					// $services = \DB::table ( 'lkp_services' )->where ( 'id', '<', '11' )->lists ( 'service_name', 'id' );
					// $packaging = \DB::table ( 'lkp_services' )->where ( 'id', '>', '10' )->lists ( 'service_name', 'id' );
					$services = LkpServices::Where ( 'id', '<', 11 )->orderBy ( 'group_name' )->select ( 'id', 'service_name', 'group_name' )->get ();
					
					$packaging = \DB::table ( 'lkp_services' )->where ( 'id', '>', '10' )->lists ( 'service_name', 'id' );
					
					$locality = \DB::table ( 'lkp_localities' )->orderBy ( 'locality_name', 'asc' )->lists ( 'locality_name', 'id' );
					
					$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
					
					// $buyer_business = DB::table ( 'buyer_business_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
					$myservices =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path','id')->get();
 				
					$lkp_industry = $this->getIndustries();
					$getSpecialities = $this->getSpecialities();
					$getEmployeeStrengths = $this->getEmployeeStrengths();
					$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016

					return view ( 'auth.seller_business', array (
							'myservices'=> $myservices,
							'stateList' => $stateList,
							'country' => $country,
							'business' => $business,
							'services' => $services,
							'packaging' => $packaging,
							'cities' => $cities,
							'locality' => $locality,
							'user_phone' => $user_phone,
							'user_email' => $user_email,
							'lkp_industry' => $lkp_industry,
							'getSpecialities' => $getSpecialities,
							'getEmployeeStrengths' => $getEmployeeStrengths,
							'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016

					) );
				} else {
				
						if($is_active == '1'){
						return Redirect ( 'home' );
						}else{
							return Redirect ( '/thankyou_seller' );
						}
					
				}
			}
		}
	}
	public function registerSellerBusiness() {
		
		Log::info ( 'User submitted Seller business registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "SELLER_BUSINESS_REGISTRATION", SELLER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			$data = Input::all ();
		
			$sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';
			
			if (is_dir ( $sellerBusinessDirectory )) {
			} else {
				mkdir ( $sellerBusinessDirectory, 0777, true );
			}
			
			
			if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
				$file = 'profile_picture';
					
				$uploadedFile = RegisterController::checkUploadCrop ( $sellerBusinessDirectory, $file );
				$data ['profile_picture'] = $uploadedFile;
			} else {
				$data ['profile_picture'] = '';
			}
			
			if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
				$file = 'logo_user';
			
				$uploadedFile = RegisterController::checkUploadCrop ( $sellerBusinessDirectory, $file );
				$data ['logo_user'] = $uploadedFile;
			} else {
				$data ['logo_user'] = '';
			}
				
			
			
			if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
				$file = 'in_corporation_file';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['in_corporation_file'] = $uploadedFile;
			} else {
				$data ['in_corporation_file'] = '';
			}
			if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
				$file = 'tin_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['tin_filepath'] = $uploadedFile;
			} else {
				$data ['tin_filepath'] = '';
			}
			if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
				$file = 'gta_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['gta_filepath'] = $uploadedFile;
			} else {
				$data ['gta_filepath'] = '';
			}
			if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
				$file = 'pancard_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['pancard_filepath'] = $uploadedFile;
			} else {
				$data ['pancard_filepath'] = '';
			}
			if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
				$file = 'service_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['service_tax_filepath'] = $uploadedFile;
			} else {
				$data ['service_tax_filepath'] = '';
			}
			if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
				$file = 'central_excise_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['central_excise_filepath'] = $uploadedFile;
			} else {
				$data ['central_excise_filepath'] = '';
			}
			
			if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
				$file = 'sales_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['sales_tax_filepath'] = $uploadedFile;
			} else {
				$data ['sales_tax_filepath'] = '';
			}
			$data['secondary_role_id']='0';
			$newRole = RegisterController::selectRole ( '2' );
			if ($newRole == '1') {
				$newBuyer = RegisterController::createSellerBusiness ( $data );
			} else {
				return Redirect ( 'register/seller_business' );
			}
			
			if ($newBuyer == 1) {
				
				return Redirect ( 'thankyou_seller' )->with ( 'message', 'Seller business details are submitted successfully.' );
			} else {
				return Redirect ( 'register/seller_business' );
			}
		}
	}
	
	/**
	 *
	 * Saving Seller business details in database
	 *
	 * @param
	 *        	$data
	 *        	
	 *        	
	 */
	public function createSellerBusiness(array $data) {
		Log::info ( 'Seller business details are getting saved by create_business_seller_action:' . $this->user_pk, array (
				'c' => '1' 
		) );
		//echo "<pre>";	print_r($data);die();
		$newBuyer = RegisterController::createBusinessBuyer ( $data );
		if ($newBuyer == '1') {
			
			CommonComponent::activityLog ( "CREATE_SELLER_BUSINESS", CREATE_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
			
			$sellerUpladFolder = 'uploads/seller/' . $this->user_pk . '/';

			if(isset($data ['profile_picture']) && $data ['profile_picture'] != ''){
				$data ['profile_picture_file'] = str_replace($sellerUpladFolder,"",$data ['profile_picture']);
			}else{
				$data ['profile_picture_file'] = '';
			}
			if(isset($data ['logo_user']) && $data ['logo_user'] != ''){
				$data ['logo_user_file'] = str_replace($sellerUpladFolder,"",$data ['logo_user']);
			}else{
				$data ['logo_user_file'] = '';
			}
			
			$sellerBusiness = new Seller ();
			$services = array ();
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];
			
			$sellerBusiness->user_id = $this->user_pk;
			$sellerBusiness->name = $data ['name'];
			$sellerBusiness->established_in = $data ['established_in'];
			$sellerBusiness->lkp_business_type_id = $data ['lkp_business_type_id'];
			if ($sellerBusiness->lkp_business_type_id == 8) {
				$sellerBusiness->other_business_type = $data ['other_business_type'];
			}
			$sellerBusiness->lkp_country_id = $data ['lkp_country_id'];
			$sellerBusiness->lkp_state_id = $data ['lkp_state_id'];
			$sellerBusiness->principal_place = $data ['principal_place'];
			$sellerBusiness->address = $data ['address'];
			$sellerBusiness->pincode = $data ['pincode'];
			$sellerBusiness->current_turnover = $data ['current_turnover'];
			$sellerBusiness->first_year_turnover = $data ['first_year_turnover'];
			$sellerBusiness->second_year_turnover = $data ['second_year_turnover'];
			$sellerBusiness->third_year_turnover = $data ['third_year_turnover'];
			$sellerBusiness->contact_firstname = $data ['contact_firstname'];
			$sellerBusiness->contact_lastname = $data ['contact_lastname'];
			$sellerBusiness->contact_designation = $data ['contact_designation'];
			$sellerBusiness->contact_email = $data ['contact_email'];
			$sellerBusiness->contact_landline = $data ['contact_landline'];
			$sellerBusiness->contact_mobile = $data ['contact_mobile'];
			$sellerBusiness->description = $data ['description_user'];
			$sellerBusiness->lkp_employee_strength_id = $data ['employee_strengths'];
			$sellerBusiness->lkp_industry_id = $data ['lkp_industry'];
			$sellerBusiness->lkp_speciality_id = $data ['lkp_specialities'];
			$sellerBusiness->joining_year = date('Y');
			
			$sellerBusiness->gta = $data ['pannumber'];
			$sellerBusiness->gta = $data ['gta'];
			$sellerBusiness->tin = $data ['tin'];
			$sellerBusiness->service_tax_number = $data ['service_tax_number'];
			$sellerBusiness->bankname = $data ['bankname'];
			$sellerBusiness->branchname = $data ['branchname'];
			$sellerBusiness->in_corporation_file = $data ['in_corporation_file'];
			$sellerBusiness->tin_filepath = $data ['tin_filepath'];
			$sellerBusiness->gta_filepath = $data ['gta_filepath'];
			$sellerBusiness->pancard_filepath = $data ['pancard_filepath'];
			$sellerBusiness->service_tax_filepath = $data ['service_tax_filepath'];
			$sellerBusiness->central_excise_filepath = $data ['central_excise_filepath'];
			$sellerBusiness->sales_tax_filepath = $data ['sales_tax_filepath'];
			$sellerBusiness->created_at = $createdAt;
			$sellerBusiness->created_ip = $createdIp;
			$sellerBusiness->created_by = $this->user_pk;
			
			if ($sellerBusiness->save ()) {
				CommonComponent::auditLog ( $sellerBusiness->id, 'sellers' );
				Session::put ( 'company_name', $sellerBusiness->name ); // session for future use
				$intracityArea = array ();
				$pamArea = array ();
				$services = array ();				
				
				// see if value has been posted
				if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {
					$services = $_POST ['services'];
					$seller_services = $services;
					
				}
				 
				if (! empty ( $seller_services )) {
					foreach ( $seller_services as $service ) {
						
						$seller_services_save = new SellerService ();
						$seller_services_save->user_id = $this->user_pk;
						$seller_services_save->lkp_service_id = $service;
						$seller_services_save->created_by = $this->user_pk;
						$seller_services_save->created_at = $createdAt;
						$seller_services_save->created_ip = $createdIp;
						$seller_services_save->save ();
						CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
					}
				}
				if(isset($_POST ['services']) && in_array("3", $_POST ['services'])) {
				
				if (isset ( $_POST ['intracity_locality'] ) && $_POST ['intracity_locality'] != '') {
					
					$intracityArea = $_POST ['intracity_locality'];
					
					foreach ( $intracityArea as $intracity ) {
						
						$intracityArea_save = new SellerIntracityLocality ();
						$intracityArea_save->user_id = $this->user_pk;
						$intracityArea_save->lkp_locality_id = $intracity;
						$intracityArea_save->created_by = $this->user_pk;
						$intracityArea_save->created_at = $createdAt;
						$intracityArea_save->created_ip = $createdIp;
						
						$intracityArea_save->save ();
						
						CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
					}
				}}
				if (isset ( $_POST ['pm_city'] ) && $_POST ['pm_city'] != '') {
					
					$pamArea = $_POST ['pm_city'];
					foreach ( $pamArea as $pam ) {
						
						$pam_save = new SellerPmCity ();
						$pam_save->user_id = $this->user_pk;
						$pam_save->lkp_city_id = $pam;
						$pam_save->created_by = $this->user_pk;
						$pam_save->created_at = $createdAt;
						$pam_save->created_ip = $createdIp;
						
						$pam_save->save ();
						
						CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
					
					}
				}

				$username = $data ['contact_firstname'].' '.$data ['contact_lastname'];
				
				if($data['secondary_role_id'] == 2){
					User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'user_pic'=>$data ['profile_picture_file'],
					'logo'=>$data ['logo_user_file'],
					'pannumber'=>$data ['pannumber'],
					'updated_at' => $createdAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $createdIp
					) );
					
				}else{
					User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'lkp_role_id' => '2',
					'primary_role_id'=>'2',
					'user_pic'=>$data ['profile_picture_file'],
					'logo'=>$data ['logo_user_file'],
					'pannumber'=>$data ['pannumber'],
					'updated_at' => $createdAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $createdIp
					) );
				}
				
				CommonComponent::auditLog ( $this->user_pk, 'users' );
				return '1';
			} else {
				return '0';
			}
		} else {
			return '0';
		}
	}
	public function viewEditBuyerBusiness() {
		Log::info ( 'User has viwed edit buyer_business page by  My Profile:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DIAPLAY_EDIT_BUYER_BUSINESS", DIAPLAY_EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$state = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
		$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
		$business = \DB::table ( 'lkp_business_types' )->orderBy ( 'id', 'asc' )->lists ( 'business_type_name', 'id' );
		
		$buyer_business = DB::table ( 'buyer_business_details' )
						/** Start : @jagadeesh - 02/05/2016 
						 *Reg : Getting User uploaded logo and user picture details
						 */
							->join('users as u','u.id','=','user_id')
							->select('buyer_business_details.*','u.logo','u.user_pic','u.pannumber')	
						/** 
						 *End : @jagadeesh - 02/05/2016 
						 */
						->where ( 'user_id', '=', $this->user_pk )
						->first ();
		// echo "<pre>";print_r($buyer_business);
		
		$in_corporation_file = explode ( "/", $buyer_business->in_corporation_file );
		$buyer_business->in_corporation_file = end ( $in_corporation_file );
		
		$tin = explode ( "/", $buyer_business->tin_filepath );
		$buyer_business->tin_filepath = end ( $tin );
		
		$gta = explode ( "/", $buyer_business->gta_filepath );
		$buyer_business->gta_filepath = end ( $gta );
		
		$pancard = explode ( "/", $buyer_business->pancard_filepath );
		$buyer_business->pancard_filepath = end ( $pancard );
		
		$service_tax = explode ( "/", $buyer_business->service_tax_filepath );
		$buyer_business->service_tax_filepath = end ( $service_tax );
		
		$central_excise = explode ( "/", $buyer_business->central_excise_filepath );
		$buyer_business->central_excise_filepath = end ( $central_excise );
		
		$sales_tax = explode ( "/", $buyer_business->sales_tax_filepath );
		$buyer_business->sales_tax_filepath = end ( $sales_tax );
		
		$buyer_id = $buyer_business->id;
		
		$lkp_industry = $this->getIndustries();
		$getSpecialities = $this->getSpecialities();
		$getEmployeeStrengths = $this->getEmployeeStrengths();
		$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-02052016

		return view ( 'auth.edit_buyer_business', compact ( 'buyer_business' ), array (
				'state' => $state,
				'country' => $country,
				'business' => $business,
				'buyer_id' => $buyer_id,
				'userId' => $this->user_pk,
				'lkp_industry' => $lkp_industry,
				'getSpecialities' => $getSpecialities,
				'getEmployeeStrengths' => $getEmployeeStrengths,
				'getYearofEstablished' => $getYearofEstablished
		) );
	}
	public function editBuyerBusiness($id) {
		Log::info ( 'User has submitted edit_buyer_business form from My Profile:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "EDIT_BUYER_BUSINESS", EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			
			$data = Input::all ();
			
			// if selected 'others' option in business type
			// change the other_business_type field value as null
			if ($data ['lkp_business_type_id'] != 8) {
				$data ['other_business_type'] = null;
			}
			
			$buyer_business = DB::table ( 'buyer_business_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
			
			$buyerBusinessDirectory = 'uploads/buyer/' . $this->user_pk . '/';
			if (is_dir ( $buyerBusinessDirectory )) {
			} else {
				mkdir ( $buyerBusinessDirectory, 0777, true );
			}
			if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
				$file = 'in_corporation_file';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['in_corporation_file'] = $uploadedFile;
			} else {
				$data ['in_corporation_file'] = $buyer_business->in_corporation_file;
			}
			if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
				$file = 'tin_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['tin_filepath'] = $uploadedFile;
			} else {
				$data ['tin_filepath'] = $buyer_business->tin_filepath;
			}
			if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
				$file = 'gta_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['gta_filepath'] = $uploadedFile;
			} else {
				$data ['gta_filepath'] = $buyer_business->gta_filepath;
			}
			if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
				$file = 'pancard_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['pancard_filepath'] = $uploadedFile;
			} else {
				$data ['pancard_filepath'] = $buyer_business->pancard_filepath;
			}
			if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
				$file = 'service_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['service_tax_filepath'] = $uploadedFile;
			} else {
				$data ['service_tax_filepath'] = $buyer_business->service_tax_filepath;
			}
			if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
				$file = 'central_excise_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['central_excise_filepath'] = $uploadedFile;
			} else {
				$data ['central_excise_filepath'] = $buyer_business->central_excise_filepath;
			}
			
			if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
				$file = 'sales_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $buyerBusinessDirectory, $file );
				$data ['sales_tax_filepath'] = $uploadedFile;
			} else {
				$data ['sales_tax_filepath'] = $buyer_business->sales_tax_filepath;
			}
			
			$newBuyer = RegisterController::createEditBusinessBuyer ( $id, $data );

			if ($newBuyer == 1) {
				/** Start : @jagadeesh - 02/05/2016 
				 *Reg : Saveing its uploaded logo / user picture details
				 */
				$buyerBusinessDirectory = BUYERUPLOADPATH . $this->user_pk . '/';
				
				if (is_dir ( $buyerBusinessDirectory )) {
				} else {
						
					mkdir ( $buyerBusinessDirectory, 0777, true );
				}

				if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
					$file = 'profile_picture';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'user_pic');
						
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['profile_picture'] = $uploadedFile;
				}else{
					$data ['profile_picture'] = '';
				} 
				
				if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
					$file = 'logo_user';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'logo');
				
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['logo_user'] = $uploadedFile;
				}else{
					$data ['logo_user'] = '';
				} 

					if($data['profile_picture']){
						$update_array['user_pic'] = str_replace($buyerBusinessDirectory,"",$data ['profile_picture']);
					}

					if($data ['logo_user']){
						$update_array['logo'] = str_replace($buyerBusinessDirectory,"",$data ['logo_user']);
					}

					if(isset($update_array) && $update_array)
						User::where ( "id", $this->user_pk )->update($update_array);
				/** 
				 *End : @jagadeesh - 02/05/2016 
				 */
				
				return Redirect ( '/home' )->with ( 'edit_success_message', 'Buyer business details are updated successfully.' );
			} else {
				return Redirect ( 'register/edit/buyer_business' );
			}
		}
	}
	
	/**
	 * updating buyer business details
	 *
	 * @param $id, $data        	
	 */
	public function createEditBusinessBuyer($id, array $data) {
		Log::info ( 'Buyer business details are getting updated by createEditBusinessBuyer action:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CREATE_EDIT_BUYER_BUSINESS", CREATE_EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ['REMOTE_ADDR'];
		 //echo "<pre>";print_r($data);die();
		try {
			BuyerBusinessDetail::where ( "id", $id )->update ( array (
					'user_id' => $this->user_pk,
					'name' => $data ['name'],
					'lkp_business_type_id' => $data ['lkp_business_type_id'],
					'other_business_type' => $data ['other_business_type'],
					'lkp_country_id' => $data ['lkp_country_id'],
					'address' => $data ['address'],
					'lkp_state_id' => $data ['lkp_state_id'],
					'principal_place' => $data ['principal_place'],
					'pincode' => $data ['pincode'],
					'current_turnover' => $data ['current_turnover'],
					'first_year_turnover' => $data ['first_year_turnover'],
					'second_year_turnover' => $data ['second_year_turnover'],
					'third_year_turnover' => $data ['third_year_turnover'],
					'contact_firstname' => $data ['contact_firstname'],
					'contact_lastname' => $data ['contact_lastname'],
					'contact_designation' => $data ['contact_designation'],
					'contact_email' => $data ['contact_email'],
					'contact_mobile' => $data ['contact_mobile'],
					'contact_landline' => $data ['contact_landline'],
					'established_in' => $data ['established_in'],
					'description' => $data ['description_user'],
					'lkp_employee_strength_id' => $data ['employee_strengths'],
					'lkp_industry_id' => $data ['lkp_industry'],
					'lkp_speciality_id' => $data ['lkp_specialities'],
					'gta' => $data ['gta'],
					'service_tax_number' => $data ['service_tax_number'],
					'tin' => $data ['tin'],
					'bankname' => $data ['bankname'],
					'branchname' => $data ['branchname'],
					'in_corporation_file' => $data ['in_corporation_file'],
					'tin_filepath' => $data ['tin_filepath'],
					'sales_tax_filepath' => $data ['sales_tax_filepath'],
					'service_tax_filepath' => $data ['service_tax_filepath'],
					'central_excise_filepath' => $data ['central_excise_filepath'],
					'gta_filepath' => $data ['gta_filepath'],
					'pancard_filepath' => $data ['pancard_filepath'],
					'updated_by' => $this->user_pk,
					'updated_at' => $updatedAt,
					'updated_ip' => $updatedIp 
			) );
			CommonComponent::auditLog ( $id, 'buyer_business_details' );
			$username = $data ['contact_firstname'].' '.$data ['contact_lastname'];
			if(Auth::User()->lkp_role_id == '2')
			{
				Session::put('last_login_role_id','1');
			User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'secondary_role_id' =>'1',
					'pannumber' => $data ['pannumber'],
					'updated_at' => $updatedAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $updatedIp 
			) );
			}
			else{	User::where ( "id", $this->user_pk )->update ( array (
					'username' => $username,
					'pannumber' => $data ['pannumber'],
					'updated_at' => $updatedAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $updatedIp 
			) );}
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			
			return '1';
		} catch ( Exception $ex ) {
			return '0';
		}
	}
	
	/**
	 * Dispalys edit seller(Business) page
	 */
	public function viewEditSellerBusiness() {
		
		Log::info ( 'User has viewed edit seller business page:' . $this->user_pk, 
			array ('c' => '1' ) 
		);
		
		CommonComponent::activityLog ( "DISPLAY_EDIT_SELLER_BUSINESS", DISPLAY_EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
		$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
		$business = \DB::table ( 'lkp_business_types' )->orderBy ( 'id', 'asc' )->lists ( 'business_type_name', 'id' );
		//$services = \DB::table ( 'lkp_services')->where ( 'id', '<', '11' )->lists ( 'service_name', 'id' );
		$services = LkpServices::Where ( 'id', '<', 16 )->orderBy ( 'group_name' )->select ( 'id', 'service_name', 'group_name' )->get ();
		
		$packaging = LkpServices::Where ( 'id', '>', 10 )->where('id','<',16)->orderBy ( 'group_name' )->select ( 'id', 'service_name', 'group_name' )->get ();
		$locality = \DB::table ( 'lkp_localities' )->orderBy ( 'locality_name', 'asc' )->lists ( 'locality_name', 'id' );
		$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );

		$intracity_cities_list = \DB::table ( 'lkp_cities as lc' )
						->join('lkp_ict_locations as ictl','ictl.lkp_city_id','=','lc.id')
						->join('lkp_localities as ictlt','ictlt.id','=','ictl.lkp_locality_id')
						->orderBy ( 'lc.city_name', 'asc' )
						->select ( 'lc.city_name', 'lc.id' )->get();


		for($k=0;$k<count($intracity_cities_list);$k++){
			$intracity_cities[$intracity_cities_list[$k]->id]= $intracity_cities_list[$k]->city_name;
		}
		
		$is_seller_business = DB::table ( 'sellers' )->where ( 'user_id', '=', $this->user_pk )->first ();
	
		if(empty($is_seller_business)){
			$is_seller_business_exist = 0;
		}else{
			$is_seller_business_exist = 1;
		}

		$seller_business = DB::table ( 'users' )->leftJoin ( 'sellers', 'users.id', '=', 'sellers.user_id' )
				/** Start : @jagadeesh - 02/05/2016 
				 *Reg : Getting User uploaded logo and user picture details
				 */
					->select('users.*','sellers.*','users.logo as logo')
				/** 
				 *End : @jagadeesh - 02/05/2016 
				 */
				->where ( 'users.id', '=', $this->user_pk )
				->first ();
		
		$intracity = DB::table ( 'users' )->select ( 'lkp_locality_id as locality_id', 'lc.id as city_id' )->leftJoin ( 'seller_intracity_localities as sip', 'users.id', '=', 'sip.user_id' )->leftJoin ( 'lkp_localities as ll', 'sip.lkp_locality_id', '=', 'll.id' )->leftJoin ( 'lkp_cities as lc', 'll.lkp_city_id', '=', 'lc.id' )->where ( 'users.id', '=', $this->user_pk )->get ();
		
		$packersMovers = DB::table ( 'users' )->select ( 'lkp_city_id as city_id', 'ls.id as state_id' )->leftJoin ( 'seller_pm_cities as spc', 'users.id', '=', 'spc.user_id' )->leftJoin ( 'lkp_cities as lc', 'spc.lkp_city_id', '=', 'lc.id' )->leftJoin ( 'lkp_states as ls', 'lc.lkp_state_id', '=', 'ls.id' )->where ( 'users.id', '=', $this->user_pk )->get ();
		
		$allServices =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path', 'id')->get();
		
		$pmCity_array = array ();
		$pmState_array = array ();
		$intra_locality = array ();
		$intra_city = array ();
		foreach ( $packersMovers as $pm ) {
			
			array_push ( $pmCity_array, $pm->city_id );
			array_push ( $pmState_array, $pm->state_id );
		}
		foreach ( $intracity as $pm ) {
			
			array_push ( $intra_locality, $pm->locality_id );
			array_push ( $intra_city, $pm->city_id );
		}
		$packersMoverscities = \DB::table ( 'lkp_cities' )->whereIn ( 'lkp_state_id', $pmState_array )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		
		//$transport = \DB::table ( 'seller_services as ss' )->select ( 'ss.lkp_service_id as service_id' )->leftJoin ( 'users', 'users.id', '=', 'ss.user_id' )->where ( 'ss.lkp_service_id', '<', 16 )->where ( 'ss.user_id', '=', $this->user_pk )->get ();
		$transport = \DB::table ( 'seller_services as ss' )->select ( 'ss.lkp_service_id as service_id' )->leftJoin ( 'users', 'users.id', '=', 'ss.user_id' )->where ( 'ss.user_id', '=', $this->user_pk )->get ();
		
		$handling = \DB::table ( 'seller_services as ss' )->select ( 'ss.lkp_service_id as service_id' )->leftJoin ( 'users', 'users.id', '=', 'ss.user_id' )->where ( 'ss.lkp_service_id', '>', 10 )->where ( 'ss.user_id', '=', $this->user_pk )->get ();
		
		$transport_array = array ();
		foreach ( $transport as $trans ) {
			
			array_push ( $transport_array, $trans->service_id );
		}
		
		$handling_array = array ();
		foreach ( $handling as $hand ) {
			
			array_push ( $handling_array, $hand->service_id );
		}
		
		// echo "<pre>"; print_r($transport_array);die();
		
		$in_corporation_file = explode ( "/", $seller_business->in_corporation_file );
		$seller_business->in_corporation_file = end ( $in_corporation_file );
		
		$tin = explode ( "/", $seller_business->tin_filepath );
		$seller_business->tin_filepath = end ( $tin );
		
		$gta = explode ( "/", $seller_business->gta_filepath );
		$seller_business->gta_filepath = end ( $gta );
		
		$pancard = explode ( "/", $seller_business->pancard_filepath );
		$seller_business->pancard_filepath = end ( $pancard );
		
		$service_tax = explode ( "/", $seller_business->service_tax_filepath );
		$seller_business->service_tax_filepath = end ( $service_tax );
		
		$central_excise = explode ( "/", $seller_business->central_excise_filepath );
		$seller_business->central_excise_filepath = end ( $central_excise );
		
		$sales_tax = explode ( "/", $seller_business->sales_tax_filepath );
		$seller_business->sales_tax_filepath = end ( $sales_tax );
		
		$logo_file = explode ( "/", $seller_business->logo );
		$seller_business->logo = end ( $logo_file );
		
		$lkp_industry = $this->getIndustries();
		$getSpecialities = $this->getSpecialities();
		$getEmployeeStrengths = $this->getEmployeeStrengths();
		$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016
		
		
		$seller_id = $seller_business->id;



		//echo "<pre>";print_r($seller_business);die();
		return view ( 'auth.edit_seller_business', compact ( 'seller_business' ), array (
				'allServices' => $allServices,
				'stateList' => $stateList,
				'country' => $country,
				'business' => $business,
				'services' => $services,
				'packaging' => $packaging,
				'cities' => $cities,
				'intracity_cities' => $intracity_cities,
				'seller_id' => $seller_id,
				'locality' => $locality,
				'pmCity' => $pmCity_array,
				'pmState' => $pmState_array,
				'intra_locality' => $intra_locality,
				'intra_city' => $intra_city,
				'handling' => $handling_array,
				'transport' => $transport_array,
				'packMovCities' => $packersMoverscities,
				'is_seller_business_exist'=>$is_seller_business_exist,
				'userId' => $this->user_pk,
				'lkp_industry' => $lkp_industry,
				'getSpecialities' => $getSpecialities,
				'getEmployeeStrengths' => $getEmployeeStrengths,
				'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016

		) );
	}
	
	/**
	 *
	 * @param user_id as $id
	 */
	public function editSellerBusiness($id) {
		Log::info ( 'User has submitted edit seller business form by My Profile:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "EDIT_SELLER_BUSINESS", EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			// if selected 'others' option in business type
			// change the other_business_type field value as null
			if ($data ['lkp_business_type_id'] != 8) {
				$data ['other_business_type'] = null;
			}
			
			$seller_business = DB::table ( 'sellers' )->where ( 'user_id', '=', $this->user_pk )->first ();
			
			$sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';
			
			$sellerLogoDirectory = 'uploads/seller/' . $this->user_pk . '/logo/';
			
			if (is_dir ( $sellerBusinessDirectory )) {
				if (is_dir ( $sellerLogoDirectory )) {
				} else {
					mkdir ( $sellerLogoDirectory, 0777, true );
				}
			} else {
				mkdir ( $sellerBusinessDirectory, 0777, true );
				mkdir ( $sellerLogoDirectory, 0777, true );
			}
			// validating logo if posted
			if (isset ( $_FILES ['logo_file'] ) && ! empty ( $_FILES ['logo_file'] ['name'] )) {
				
				$rules = [ 
						'logo_file' => 'mimes:jpg,jpeg,png,bmp,gif' 
				];
				$validation = Validator::make ( $data, $rules );
				
				if ($validation->fails ()) {
					return Redirect::to ( 'register/edit_seller' )->withErrors ( "Only .jpg / jpeg, .png, .bmp, .gif format is allowed for logo image" );
				} else {
					
					$file = 'logo_file';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerLogoDirectory, $file );
					
					$data ['logo_file'] = $uploadedFile;
				}
			}
			
			if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
				$file = 'in_corporation_file';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['in_corporation_file'] = $uploadedFile;
			} else {
				
				$data ['in_corporation_file'] = $seller_business->in_corporation_file;
			}
			if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
				$file = 'tin_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['tin_filepath'] = $uploadedFile;
			} else {
				$data ['tin_filepath'] = $seller_business->tin_filepath;
			}
			if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
				$file = 'gta_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['gta_filepath'] = $uploadedFile;
			} else {
				$data ['gta_filepath'] = $seller_business->gta_filepath;
			}
			if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
				$file = 'pancard_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['pancard_filepath'] = $uploadedFile;
			} else {
				$data ['pancard_filepath'] = $seller_business->pancard_filepath;
			}
			if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
				$file = 'service_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['service_tax_filepath'] = $uploadedFile;
			} else {
				$data ['service_tax_filepath'] = $seller_business->service_tax_filepath;
			}
			if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
				$file = 'central_excise_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['central_excise_filepath'] = $uploadedFile;
			} else {
				$data ['central_excise_filepath'] = $seller_business->central_excise_filepath;
			}
			
			if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
				$file = 'sales_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['sales_tax_filepath'] = $uploadedFile;
			} else {
				$data ['sales_tax_filepath'] = $seller_business->sales_tax_filepath;
			}
			
			$newBuyer = RegisterController::createEditSellerBusiness ( $id, $data );

			$seller_subscription_details = DB::table ( 'sellers' )->where ( 'user_id', '=', $this->user_pk )->select ('subscription_start_date','subscription_end_date')->get();
			$subStartDate =date ( "d-m-Y",  strtotime($seller_subscription_details[0]->subscription_start_date));
			$subEndDate = date ( "d-m-Y", strtotime ($seller_subscription_details[0]->subscription_end_date));

			$curr_date = date('d-m-Y');		
			
			if ($newBuyer == 1) {

				/** Start : @jagadeesh - 02/05/2016 
				 *Reg : Saveing its uploaded logo / user picture details
				 */
				$buyerBusinessDirectory = SELLERUPLOADPATH . $this->user_pk . '/';
				
				if (is_dir ( $buyerBusinessDirectory )) {
				} else {
						
					mkdir ( $buyerBusinessDirectory, 0777, true );
				}

				if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
					$file = 'profile_picture';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'user_pic');
						
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['profile_picture'] = $uploadedFile;
				}else{
					$data ['profile_picture'] = '';
				} 
				
				if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
					$file = 'logo_user';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'logo');
				
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['logo_user'] = $uploadedFile;
				}else{
					$data ['logo_user'] = '';
				} 

					if($data['profile_picture']){
						$update_array['user_pic'] = str_replace($buyerBusinessDirectory,"",$data ['profile_picture']);
					}

					if($data ['logo_user']){
						$update_array['logo'] = str_replace($buyerBusinessDirectory,"",$data ['logo_user']);
					}

					if(isset($update_array) && $update_array)
						User::where ( "id", $this->user_pk )->update($update_array);
				/** 
				 *End : @jagadeesh - 02/05/2016 
				 */
				
				if(Auth::User()->lkp_role_id =='1'){
					if($curr_date >= $subStartDate && $curr_date <= $subEndDate){
						return Redirect ( '/home' )->with ( 'message', 'Seller details are updated successfully.' );
					}else{
						return Redirect ( '/thankyou_seller' )->with ( 'message', 'Seller details are updated successfully.' );
					}
					//return Redirect ( 'thankyou_seller' )->with ( 'message', 'Seller business details are submitted successfully.' );
											
				}else{
				return Redirect ( '/home' )->with ( 'edit_success_message', 'Seller business details are updated successfully.' );
				}
			} else {
				return Redirect ( 'register/edit/seller_business' );
			}
		}
	}
	public function createEditSellerBusiness($id, $data) {
		Log::info ( 'Seller business details are getting updated by createEditSellerBusiness action:' . $this->user_pk, array (
				'c' => '1' 
		) );
		//echo "<pre>";print_r($data);die();
		CommonComponent::activityLog ( "CREATE_EDIT_SELLER_BUSINESS", CREATE_EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL );
		
		$sellerLogo = '';
		if (isset ( $data ['logo_file'] ) && $data ['logo_file'] != '') {
			$sellerLogo = $data ['logo_file'];
		}
		
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ['REMOTE_ADDR'];
		
		try {
			Seller::where ( "id", $id )->update ( array (
					'user_id' => $this->user_pk,
					'name' => $data ['name'],
					'established_in' => $data ['established_in'],
					'lkp_business_type_id' => $data ['lkp_business_type_id'],
					'other_business_type' => $data ['other_business_type'],
					'lkp_country_id' => $data ['lkp_country_id'],
					'address' => $data ['address'],
					'lkp_state_id' => $data ['lkp_state_id'],
					'principal_place' => $data ['principal_place'],
					'pincode' => $data ['pincode'],
					'current_turnover' => $data ['current_turnover'],
					'first_year_turnover' => $data ['first_year_turnover'],
					'second_year_turnover' => $data ['second_year_turnover'],
					'third_year_turnover' => $data ['third_year_turnover'],
					'contact_firstname' => $data ['contact_firstname'],
					'contact_lastname' => $data ['contact_lastname'],
					'contact_designation' => $data ['contact_designation'],
					'contact_email' => $data ['contact_email'],
					'contact_mobile' => $data ['contact_mobile'],
					'contact_landline' => $data ['contact_landline'],
					'description' => $data ['description_user'],
					'lkp_employee_strength_id' => $data ['employee_strengths'],
					'lkp_industry_id' => $data ['lkp_industry'],
					'lkp_speciality_id' => $data ['lkp_specialities'],
					'gta' => $data ['gta'],
					'service_tax_number' => $data ['service_tax_number'],
					'tin' => $data ['tin'],
					'bankname' => $data ['bankname'],
					'branchname' => $data ['branchname'],
					'in_corporation_file' => $data ['in_corporation_file'],
					'tin_filepath' => $data ['tin_filepath'],
					'sales_tax_filepath' => $data ['sales_tax_filepath'],
					'service_tax_filepath' => $data ['service_tax_filepath'],
					'central_excise_filepath' => $data ['central_excise_filepath'],
					'gta_filepath' => $data ['gta_filepath'],
					'pancard_filepath' => $data ['pancard_filepath'],
					'updated_by' => $this->user_pk,
					'updated_at' => $updatedAt,
					'updated_ip' => $updatedIp 
			) );
			CommonComponent::auditLog ( $id, 'sellers' );
			Session::put ( 'company_name', $data ['name'] ); // session for future use
			$services = array ();			
			$intracityArea = array ();
			$pamArea = array ();
			$isSelect_intracity = '';
			$isSelect_pm = '';
			
			// see if value has been posted
		if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {
					$services = $_POST ['services'];
					$seller_services = $services;
					
				}
			if (! empty ( $seller_services )) {
				
				DB::table ( 'seller_services' )->where ( 'user_id', $this->user_pk )->delete ();
			}
			
				// for deleting / inserting pm & intracity areas
				if (in_array ( 3, $seller_services )) {
					$isSelect_intracity = 1;
				}
				if (in_array ( 15, $seller_services )) {
					$isSelect_pm = 1;
				}
				
				foreach ( $seller_services as $service ) {
					
					$seller_services_save = new SellerService ();
					$seller_services_save->user_id = $this->user_pk;
					$seller_services_save->lkp_service_id = $service;
					$seller_services_save->created_by = $this->user_pk;
					$seller_services_save->created_at = $updatedAt;
					$seller_services_save->created_ip = $updatedIp;
					$seller_services_save->save ();
					CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
				}
			
			
			if (isset ( $_POST ['intracity_locality'] ) && (! empty ( $_POST ['intracity_locality'] != '' ))) {
				$intracityArea = $_POST ['intracity_locality'];
				
				DB::table ( 'seller_intracity_localities' )->where ( 'user_id', $this->user_pk )->delete ();
				
				if ($isSelect_intracity == 1) {
					foreach ( $intracityArea as $intracity ) {
						
						$intracityArea_save = new SellerIntracityLocality ();
						$intracityArea_save->user_id = $this->user_pk;
						$intracityArea_save->lkp_locality_id = $intracity;
						$intracityArea_save->created_by = $this->user_pk;
						$intracityArea_save->created_at = $updatedAt;
						$intracityArea_save->created_ip = $updatedIp;
						$intracityArea_save->save ();
						CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
					}
				}
			}
			
			if (isset ( $_POST ['pm_city'] ) && ($_POST ['pm_city'] != '')) {
				$pamArea = $_POST ['pm_city'];
				
				DB::table ( 'seller_pm_cities' )->where ( 'user_id', $this->user_pk )->delete ();
				
				if ($isSelect_pm == 1) {
					
					foreach ( $pamArea as $pam ) {
						
						$pam_save = new SellerPmCity ();
						$pam_save->user_id = $this->user_pk;
						$pam_save->lkp_city_id = $pam;
						$pam_save->created_by = $this->user_pk;
						$pam_save->created_at = $updatedAt;
						$pam_save->created_ip = $updatedIp;
						$pam_save->save ();
						CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
					}
				}
			}
			if(Auth::User()->lkp_role_id == '1'){
				User::where ( "id", $this->user_pk )->update ( array (
				'secondary_role_id'=>'2',
				'updated_at' => $updatedAt,
				'updated_by' => $this->user_pk,
				'updated_ip' => $updatedIp
				) );
			}
			$username = $data ['contact_firstname'].' '.$data ['contact_lastname'];
			if ($sellerLogo != '') {
				User::where ( "id", $this->user_pk )->update ( array (
						'logo' => $sellerLogo,
						'username' => $username,
						'pannumber' => $data ['pannumber'],
						'updated_at' => $updatedAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $updatedIp 
				) );
			} else {
				User::where ( "id", $this->user_pk )->update ( array (
						'username' => $username,
						'pannumber' => $data ['pannumber'],
						'updated_at' => $updatedAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $updatedIp 
				) );
			}
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			return '1';
		} catch ( Exception $ex ) {
			return '0';
		}
	}
	
	/*
	 *
	 * Individual seller Registeration
	 *
	 */
	public function seller() {
		Log::info ( 'Seller has viewed seller individual registration page:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPALY_SELLER_INDIVIDUAL", DISPALY_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		$userRecord = \DB::table ( 'users' )->where ( 'id', '=', $this->user_pk )->first ();
		$is_active = $userRecord->is_active;
		$role_id = $userRecord->lkp_role_id;
		$is_business = $userRecord->is_business;
		
		if ($userRecord->email != null) {
			$user_email = $userRecord ->email;
		} elseif ($userRecord->phone != null) {
			$userRecord->phone;
		}
		
		if ($role_id == '1') {
			return Redirect ( 'register/buyer' );
		} else {
			if ($is_business == IS_BUSINESS) {
				return Redirect ( 'register/seller_business' );
			} else {
				
				if ($is_business == IS_INDIVIDUAL) {
					
					$sellerRecord = \DB::table ( 'seller_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
					
					if (empty ( $sellerRecord )) {
						
						$buyer = array (); // = DB::table ( 'buyer_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
						
						$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
						$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
						// $services = \DB::table ( 'lkp_services' )->where ( 'id', '<', '11' )->lists ( 'service_name','id' );
						LkpServices::Where ( 'id', '<', 11 )->orderBy ( 'group_name' )->select ( 'id', 'service_name', 'group_name' )->get ();
						
						$packaging = \DB::table ( 'lkp_services' )->where ( 'id', '>', '10' )->lists ( 'service_name', 'id' );
						$locality = \DB::table ( 'lkp_localities' )->orderBy ( 'locality_name', 'asc' )->lists ( 'locality_name', 'id' );
						$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );

						$intracity_cities_list = \DB::table ( 'lkp_cities as lc' )
						->join('lkp_ict_locations as ictl','ictl.lkp_city_id','=','lc.id')
						->join('lkp_localities as ictlt','ictlt.id','=','ictl.lkp_locality_id')
						->orderBy ( 'lc.city_name', 'asc' )
						->select ( 'lc.city_name', 'lc.id' )->get();
						
						for($k=0;$k<count($intracity_cities_list);$k++){
							$intracity_cities[$intracity_cities_list[$k]->id]= $intracity_cities_list[$k]->city_name;
						}
						$myservices =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path' , 'id')->get();
						
						$lkp_industry = $this->getIndustries();
						$getSpecialities = $this->getSpecialities();
						$getEmployeeStrengths = $this->getEmployeeStrengths();
						$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016
						//echo "<pre>";print_r($myservices);die();
						return view ( 'auth.seller', array (
								'stateList' => $stateList,
								'country' => $country,
								'services' => $myservices,
								'packaging' => $packaging,
								'cities' => $cities,
								'intracity_cities' => $intracity_cities,
								'buyer' => $buyer,
								'locality' => $locality,
								'user_email' => isset($user_email) ? $user_email : "",
								'lkp_industry' => $lkp_industry,
								'getSpecialities' => $getSpecialities,
								'getEmployeeStrengths' => $getEmployeeStrengths,
								'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016
						) );
					} else {
						if($is_active == '1'){
						return Redirect ( 'home' );
						}else{
							return Redirect ( 'thankyou_seller' );
						}
					}
				} else {
					return Redirect ( 'register/seller_business' );
				}
			}
		}
	}
	
	/*
	 *
	 * Individual seller Registration
	 *
	 */
	public function registerSeller() {		
		Log::info ( 'User has submitted seller individual registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		// CommonComponent::activityLog ( "SELLER_BUSINESS_REGISTRATION", SELLER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			 //echo "<pre>";print_r($data);die();
			$data ['mobile'] = $data ['contact_mobile'];
			$data ['landline'] = $data ['contact_landline'];
			
			//$newBuyer = RegisterController::createBuyer ( $data );
			
			//if ($newBuyer == '1') {
				
				$sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';
				
				if (is_dir ( $sellerBusinessDirectory )) {
				} else {
					mkdir ( $sellerBusinessDirectory, 0777, true );
				}
				
				if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
					$file = 'profile_picture';
					
					$uploadedFile = RegisterController::checkUploadCrop ( $sellerBusinessDirectory, $file );
					$data ['profile_picture'] = $uploadedFile;
				} else {
					$data ['profile_picture'] = '';
				}
				
				if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
					$file = 'logo_user';
						
					$uploadedFile = RegisterController::checkUploadCrop ( $sellerBusinessDirectory, $file );
					$data ['logo_user'] = $uploadedFile;
				} else {
					$data ['logo_user'] = '';
				}
				
				if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
					$file = 'in_corporation_file';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['in_corporation_file'] = $uploadedFile;
				} else {
					$data ['in_corporation_file'] = '';
				}
				
				
				
				
				if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
					$file = 'tin_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['tin_filepath'] = $uploadedFile;
				} else {
					$data ['tin_filepath'] = '';
				}
				if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
					$file = 'gta_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['gta_filepath'] = $uploadedFile;
				} else {
					$data ['gta_filepath'] = '';
				}
				if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
					$file = 'pancard_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['pancard_filepath'] = $uploadedFile;
				} else {
					$data ['pancard_filepath'] = '';
				}
				if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
					$file = 'service_tax_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['service_tax_filepath'] = $uploadedFile;
				} else {
					$data ['service_tax_filepath'] = '';
				}
				if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
					$file = 'central_excise_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['central_excise_filepath'] = $uploadedFile;
				} else {
					$data ['central_excise_filepath'] = '';
				}
				
				if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
					$file = 'sales_tax_filepath';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['sales_tax_filepath'] = $uploadedFile;
				} else {
					$data ['sales_tax_filepath'] = '';
				}
				$data['secondary_role_id'] = '0';
				$newRole = RegisterController::selectRole ( '2' );
				if ($newRole == '1') {
					$newSeller = RegisterController::createSeller ( $data );
				} else {
					return Redirect ( 'register/seller' )->with ( 'error_message', 'Error occured while saving, Please try again after sometime.' );
				}
				
				if ($newSeller == 1) {
					
					return Redirect ( 'thankyou_seller' )->with ( 'message', 'Seller business details are submitted successfully.' );
				} else {
					return Redirect ( 'register/seller' );
				}
			/*} else {
				return Redirect ( 'register/seller' )->with ( 'error_message', 'Error occured while saving, Please try again after sometime.' );
			}*/
		}
	}
	
	/**
	 *
	 * Saving Individual Seller details in database
	 *
	 * @param
	 *        	$data
	 *        	
	 *        	
	 */
	public function createSeller(array $data) {
		Log::info ( 'User has submitted seller individual registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "CREATE_SELLER_INDIVIDUAL", CREATE_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		//echo "<pre>";print_r($data);
		
		
		$sellerUpladFolder = 'uploads/seller/' . $this->user_pk . '/';
		$data ['profile_picture_file'] = str_replace($sellerUpladFolder,"",$data ['profile_picture']);
		$data ['logo_user_file'] = str_replace($sellerUpladFolder,"",$data ['logo_user']);		
		
		$sellerIndividual = new SellerDetail ();
		$services = array ();
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$sellerIndividual->user_id = $this->user_pk;
		$sellerIndividual->firstname = $data ['firstname'];
		$sellerIndividual->lastname = $data ['lastname'];
		$sellerIndividual->landline = $data ['landline'];
		$sellerIndividual->nature_of_business = $data ['nature_of_business'];
		$sellerIndividual->established_in = $data ['established_in'];
		$sellerIndividual->principal_place = $data ['principal_place'];
		$sellerIndividual->address = $data ['address'];
		$sellerIndividual->pincode = $data ['pincode'];
		$sellerIndividual->description = $data ['description_user'];
		$sellerIndividual->current_turnover = $data ['current_turnover'];
		$sellerIndividual->first_year_turnover = $data ['first_year_turnover'];
		$sellerIndividual->second_year_turnover = $data ['second_year_turnover'];
		$sellerIndividual->third_year_turnover = $data ['third_year_turnover'];
		$sellerIndividual->contact_firstname = $data ['contact_firstname'];
		$sellerIndividual->contact_lastname = $data ['contact_lastname'];
		$sellerIndividual->contact_designation = $data ['contact_designation'];
		$sellerIndividual->contact_email = $data ['contact_email'];
		$sellerIndividual->contact_landline = $data ['contact_landline'];
		$sellerIndividual->contact_mobile = $data ['contact_mobile'];
		
		$sellerIndividual->lkp_employee_strength_id = $data ['employee_strengths'];
		$sellerIndividual->lkp_industry_id = $data ['lkp_industry'];
		$sellerIndividual->lkp_speciality_id = $data ['lkp_specialities'];
		$sellerIndividual->joining_year = date('Y');
		
		$sellerIndividual->gta = $data ['gta'];
		$sellerIndividual->tin = $data ['tin'];
		$sellerIndividual->service_tax_number = $data ['service_tax_number'];
		$sellerIndividual->bankname = $data ['bankname'];
		$sellerIndividual->branchname = $data ['branchname'];
		$sellerIndividual->in_corporation_file = $data ['in_corporation_file'];
		$sellerIndividual->tin_filepath = $data ['tin_filepath'];
		$sellerIndividual->gta_filepath = $data ['gta_filepath'];
		$sellerIndividual->pancard_filepath = $data ['pancard_filepath'];
		$sellerIndividual->service_tax_filepath = $data ['service_tax_filepath'];
		$sellerIndividual->central_excise_filepath = $data ['central_excise_filepath'];
		$sellerIndividual->sales_tax_filepath = $data ['sales_tax_filepath'];
		$sellerIndividual->created_at = $createdAt;
		$sellerIndividual->created_ip = $createdIp;
		$sellerIndividual->created_by = $this->user_pk;
		
		try {
			if ($sellerIndividual->save ()) {
				
				CommonComponent::auditLog ( $sellerIndividual->id, 'seller_details' );
				Session::put ( 'company_name', $sellerIndividual->name ); // session for future use
				$intracityArea = array ();
				$pamArea = array ();
				$services = array ();
				
				// see if value has been posted
			if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {
					$services = $_POST ['services'];
					$seller_services = $services;
					
				}
				if (! empty ( $seller_services )) {
					foreach ( $seller_services as $service ) {
						
						$seller_services_save = new SellerService ();
						$seller_services_save->user_id = $this->user_pk;
						$seller_services_save->lkp_service_id = $service;
						$seller_services_save->created_by = $this->user_pk;
						$seller_services_save->created_at = $createdAt;
						$seller_services_save->created_ip = $createdIp;
						$seller_services_save->save ();
						CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
					}
				}
				if (! empty ( $_POST ['intracity_locality'] [0] ) && $_POST ['intracity_locality'] [0] != '') {
					
					$intracityArea = $_POST ['intracity_locality'];
					
					foreach ( $intracityArea as $intracity ) {
						
						$intracityArea_save = new SellerIntracityLocality ();
						$intracityArea_save->user_id = $this->user_pk;
						$intracityArea_save->lkp_locality_id = $intracity;
						$intracityArea_save->created_by = $this->user_pk;
						$intracityArea_save->created_at = $createdAt;
						$intracityArea_save->created_ip = $createdIp;
						
						$intracityArea_save->save ();
						
						CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
					}
				}
				if (! empty ( $_POST ['pm_city'] [0] ) && $_POST ['pm_city'] [0] != '') {
					
					$pamArea = $_POST ['pm_city'];
					foreach ( $pamArea as $pam ) {
						
						$pam_save = new SellerPmCity ();
						$pam_save->user_id = $this->user_pk;
						$pam_save->lkp_city_id = $pam;
						$pam_save->created_by = $this->user_pk;
						$pam_save->created_at = $createdAt;
						$pam_save->created_ip = $createdIp;
						
						$pam_save->save ();
						
						CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
					}
				}
				$username = $sellerIndividual->firstname . " " . $sellerIndividual->lastname;
				
			
				User::where ( "id", $this->user_pk )->update ( array (
						'username' => $username,
						'lkp_role_id' => '2',
						'primary_role_id'=>'2',
						'user_pic'=>$data ['profile_picture_file'],
						'logo'=>$data ['logo_user_file'],
						'pannumber'=>$data ['pannumber'],
						'updated_at' => $createdAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $createdIp 
				) );
				
				CommonComponent::auditLog ( $this->user_pk, 'users' );
				return '1';
			} else {
				return '0';
			}
		} catch ( Exception $ex ) {
		}
	}
	
	/*
	 *
	 * EDIT INDIVIDUAL SELLER
	 *
	 *
	 */
	public function viewEditSeller() {
		Log::info ( 'User has viewed Edit seller individual page from My Profile:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "DISPLAY_EDIT_SELLER_INDIVIDUAL", DISPLAY_EDIT_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
		$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
		$services = \DB::table ( 'lkp_services' )->where ( 'id', '<', '11' )->lists ( 'service_name', 'id' );
		$packaging = \DB::table ( 'lkp_services' )->where ( 'id', '>', '10' )->where ( 'id', '<', '16' )->lists ( 'service_name', 'id' );
		$locality = \DB::table ( 'lkp_localities' )->orderBy ( 'locality_name', 'asc' )->lists ( 'locality_name', 'id' );
		$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		$intracity_cities_list = \DB::table ( 'lkp_cities as lc' )
		->join('lkp_ict_locations as ictl','ictl.lkp_city_id','=','lc.id')
		->join('lkp_localities as ictlt','ictlt.id','=','ictl.lkp_locality_id')
		->orderBy ( 'lc.city_name', 'asc' )
		->select ( 'lc.city_name', 'lc.id' )->get();
		
		for($k=0;$k<count($intracity_cities_list);$k++){
			$intracity_cities[$intracity_cities_list[$k]->id]= $intracity_cities_list[$k]->city_name;
		}
		$sellerRecord = DB::table ( 'seller_details' )
						/** Start : @jagadeesh - 02/05/2016 
						 *Reg : Getting User uploaded logo and user picture details
						 */
							->join('users as u','u.id','=','user_id')
							->select('seller_details.*','u.logo','u.user_pic','u.pannumber')	
						/** 
						 *End : @jagadeesh - 02/05/2016 
						 */
						->where ( 'user_id', '=', $this->user_pk )
						->first ();
		DB::table ( 'users' )->leftJoin ( 'sellers', 'users.id', '=', 'sellers.user_id' )->where ( 'users.id', '=', $this->user_pk )->first ();
		
		$intracity = DB::table ( 'users' )->select ( 'lkp_locality_id as locality_id', 'lc.id as city_id' )->leftJoin ( 'seller_intracity_localities as sip', 'users.id', '=', 'sip.user_id' )->leftJoin ( 'lkp_localities as ll', 'sip.lkp_locality_id', '=', 'll.id' )->leftJoin ( 'lkp_cities as lc', 'll.lkp_city_id', '=', 'lc.id' )->where ( 'users.id', '=', $this->user_pk )->get ();
		
		$packersMovers = DB::table ( 'users' )->select ( 'lkp_city_id as city_id', 'ls.id as state_id' )->leftJoin ( 'seller_pm_cities as spc', 'users.id', '=', 'spc.user_id' )->leftJoin ( 'lkp_cities as lc', 'spc.lkp_city_id', '=', 'lc.id' )->leftJoin ( 'lkp_states as ls', 'lc.lkp_state_id', '=', 'ls.id' )->where ( 'users.id', '=', $this->user_pk )->get ();
		$allServices =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path','id')->get();
		
		
		$seller_id = '';
		$pmCity_array = array ();
		$pmState_array = array ();
		$intra_locality = array ();
		$intra_city = array ();
		foreach ( $packersMovers as $pm ) {
			
			array_push ( $pmCity_array, $pm->city_id );
			array_push ( $pmState_array, $pm->state_id );
		}
		foreach ( $intracity as $pm ) {
			
			array_push ( $intra_locality, $pm->locality_id );
			array_push ( $intra_city, $pm->city_id );
		}
		
		$packersMoverscities = \DB::table ( 'lkp_cities' )->whereIn ( 'lkp_state_id', $pmState_array )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		
		$transport = \DB::table ( 'seller_services as ss' )->select ( 'ss.lkp_service_id as service_id' )->leftJoin ( 'users', 'users.id', '=', 'ss.user_id' )->where ( 'ss.user_id', '=', $this->user_pk )->get ();
				
		$handling = \DB::table ( 'seller_services as ss' )->select ( 'ss.lkp_service_id as service_id' )->leftJoin ( 'users', 'users.id', '=', 'ss.user_id' )->where ( 'ss.lkp_service_id', '>', '10' )->where ( 'ss.user_id', '=', $this->user_pk )->get ();
		
		$transport_array = array ();
		foreach ( $transport as $trans ) {
			
			array_push ( $transport_array, $trans->service_id );
		}
		
		$handling_array = array ();
		foreach ( $handling as $hand ) {
			
			array_push ( $handling_array, $hand->service_id );
		}
		
		// echo "<pre>"; print_r($transport_array);die();
		if (! empty ( $sellerRecord )) {
			$in_corporation_file = explode ( "/", $sellerRecord->in_corporation_file );
			$sellerRecord->in_corporation_file = end ( $in_corporation_file );
			
			$tin = explode ( "/", $sellerRecord->tin_filepath );
			$sellerRecord->tin_filepath = end ( $tin );
			
			$gta = explode ( "/", $sellerRecord->gta_filepath );
			$sellerRecord->gta_filepath = end ( $gta );
			
			$pancard = explode ( "/", $sellerRecord->pancard_filepath );
			$sellerRecord->pancard_filepath = end ( $pancard );
			
			$service_tax = explode ( "/", $sellerRecord->service_tax_filepath );
			$sellerRecord->service_tax_filepath = end ( $service_tax );
			
			$central_excise = explode ( "/", $sellerRecord->central_excise_filepath );
			$sellerRecord->central_excise_filepath = end ( $central_excise );
			
			$sales_tax = explode ( "/", $sellerRecord->sales_tax_filepath );
			$sellerRecord->sales_tax_filepath = end ( $sales_tax );
			
			$seller_id = $sellerRecord->id;
			
			
			$lkp_industry = $this->getIndustries();
			$getSpecialities = $this->getSpecialities();
			$getEmployeeStrengths = $this->getEmployeeStrengths();
			$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016

		}
		
		// echo "<pre>";print_r($packersMoverscities);die();
		return view ( 'auth.edit_seller', compact ( 'sellerRecord' ), array (
				'allServices'=>$allServices,
				'stateList' => $stateList,
				'country' => $country,
				'services' => $services,
				'packaging' => $packaging,
				'cities' => $cities,
				'intracity_cities' => $intracity_cities,
				'seller_id' => $seller_id,
				'locality' => $locality,
				'pmCity' => $pmCity_array,
				'pmState' => $pmState_array,
				'intra_locality' => $intra_locality,
				'intra_city' => $intra_city,
				'handling' => $handling_array,
				'transport' => $transport_array,
				'packMovCities' => $packersMoverscities,
				'userId' => $this->user_pk,
				'lkp_industry' => $lkp_industry,
				'getSpecialities' => $getSpecialities,
				'getEmployeeStrengths' => $getEmployeeStrengths,
				'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016

		) );
	}
	
	/*
	 * EDIT SELLER BUSINESS
	 *
	 *
	 */
	public function editSeller($id) {		
		Log::info ( 'User has submitted Edit seller individual form from My Profile:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "EDIT_SELLER_INDIVIDUAL", EDIT_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			
			$seller_individual = DB::table ( 'seller_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
			
			$sellerIndividualDirectory = 'uploads/seller/' . $this->user_pk . '/';
			$sellerLogoDirectory = 'uploads/seller/' . $this->user_pk . '/logo/';
			
			if (is_dir ( $sellerIndividualDirectory )) {
				if (is_dir ( $sellerLogoDirectory )) {
				} else {
					mkdir ( $sellerLogoDirectory, 0777, true );
				}
			} else {
				mkdir ( $sellerIndividualDirectory, 0777, true );
				mkdir ( $sellerLogoDirectory, 0777, true );
			}
			
			if (isset ( $_FILES ['logo_file'] ) && ! empty ( $_FILES ['logo_file'] ['name'] )) {
				
				$rules = [ 
						'logo_file' => 'mimes:jpg,jpeg,png,bmp,gif' 
				];
				$validation = Validator::make ( $data, $rules );
				
				if ($validation->fails ()) {
					return Redirect::to ( 'register/edit_seller' )->withErrors ( "Only .jpg / jpeg, .png, .bmp, .gif format is allowed for logo image" );
				} else {
					
					$file = 'logo_file';
					
					$uploadedFile = RegisterController::checkUpload ( $sellerLogoDirectory, $file );
					// echo $uploadedFile;die();
					$data ['logo_file'] = $uploadedFile;
				}
			}
			
			if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
				$file = 'in_corporation_file';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['in_corporation_file'] = $uploadedFile;
			} else {
				$data ['in_corporation_file'] = $seller_individual->in_corporation_file;
			}
			if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
				$file = 'tin_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['tin_filepath'] = $uploadedFile;
			} else {
				$data ['tin_filepath'] = $seller_individual->tin_filepath;
			}
			if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
				$file = 'gta_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['gta_filepath'] = $uploadedFile;
			} else {
				$data ['gta_filepath'] = $seller_individual->gta_filepath;
			}
			if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
				$file = 'pancard_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['pancard_filepath'] = $uploadedFile;
			} else {
				$data ['pancard_filepath'] = $seller_individual->pancard_filepath;
			}
			if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
				$file = 'service_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['service_tax_filepath'] = $uploadedFile;
			} else {
				$data ['service_tax_filepath'] = $seller_individual->service_tax_filepath;
			}
			if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
				$file = 'central_excise_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['central_excise_filepath'] = $uploadedFile;
			} else {
				$data ['central_excise_filepath'] = $seller_individual->central_excise_filepath;
			}
			
			if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
				$file = 'sales_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerIndividualDirectory, $file );
				$data ['sales_tax_filepath'] = $uploadedFile;
			} else {
				$data ['sales_tax_filepath'] = $seller_individual->sales_tax_filepath;
			}
			
			$newBuyer = RegisterController::createEditSellerIndividual ( $id, $data );

			$seller_subscription_details = DB::table ( 'seller_details' )->where ( 'user_id', '=', $this->user_pk )->select ('subscription_start_date','subscription_end_date')->get();
			$subStartDate =date ( "d-m-Y",  strtotime($seller_subscription_details[0]->subscription_start_date));
			$subEndDate = date ( "d-m-Y", strtotime ($seller_subscription_details[0]->subscription_end_date));

			$curr_date = date('d-m-Y');
			//if()
			if ($newBuyer == 1) {
				/** Start : @jagadeesh - 02/05/2016 
				 *Reg : Saveing its uploaded logo / user picture details
				 */
				$buyerBusinessDirectory = SELLERUPLOADPATH . $this->user_pk . '/';
				
				if (is_dir ( $buyerBusinessDirectory )) {
				} else {
						
					mkdir ( $buyerBusinessDirectory, 0777, true );
				}

				if (isset ( $_FILES ['profile_picture'] ) && ! empty ( $_FILES ['profile_picture'] ['name'] )) {
					$file = 'profile_picture';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'user_pic');
						
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['profile_picture'] = $uploadedFile;
				}else{
					$data ['profile_picture'] = '';
				} 
				
				if (isset ( $_FILES ['logo_user'] ) && ! empty ( $_FILES ['logo_user'] ['name'] )) {
					$file = 'logo_user';
					// Remove Previous uploaded file
					$remove_files = CommonComponent::RemoveProfilePreviousUploadFiles($this->user_pk,$buyerBusinessDirectory,'logo');
				
					$uploadedFile = RegisterController::checkUploadCrop ( $buyerBusinessDirectory, $file );
					$data ['logo_user'] = $uploadedFile;
				}else{
					$data ['logo_user'] = '';
				} 

					if($data['profile_picture']){
						$update_array['user_pic'] = str_replace($buyerBusinessDirectory,"",$data ['profile_picture']);
					}

					if($data ['logo_user']){
						$update_array['logo'] = str_replace($buyerBusinessDirectory,"",$data ['logo_user']);
					}

					if(isset($update_array) && $update_array)
						User::where ( "id", $this->user_pk )->update($update_array);
				/** 
				 *End : @jagadeesh - 02/05/2016 
				 */

				if(Auth::User()->lkp_role_id == '1'){
					if($curr_date >= $subStartDate && $curr_date <= $subEndDate){
						return Redirect ( '/home' )->with ( 'edit_success_message', 'Seller details are updated successfully.' );
					}else{
						return Redirect ( '/thankyou_seller' )->with ( 'edit_success_message', 'Seller details are updated successfully.' );
					}
				}else{
				return Redirect ( '/home' )->with ( 'edit_success_message', 'Seller details are updated successfully.' );
				}
			} else {
				return Redirect ( 'register/edit_seller' )->with ( 'message', 'Problem occured while updating Details. Please try after sometime.' );
			}
		}
	}
	/*
	 *
	 * Submitting edit seller individual details to various tables
	 *
	 */
	public function createEditSellerIndividual($id, $data) {
		Log::info ( 'Seller individual details are getting updated by createEditSellerIndividual action:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		$sellerLogo = '';
		if (isset ( $data ['logo_file'] ) && $data ['logo_file'] != '') {
			$sellerLogo = $data ['logo_file'];
		}
		
		CommonComponent::activityLog ( "CREATE_EDIT_SELLER_INDIVIDUAL", CREATE_EDIT_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ['REMOTE_ADDR'];
		
		try {
			SellerDetail::where ( "id", $id )->update ( array (
					'user_id' => $this->user_pk,
					'firstname' => $data ['firstname'],
					'lastname' => $data ['lastname'],
					'address' => $data ['address'],
					'pincode' => $data ['pincode'],
					'landline' => $data ['landline'],
					'nature_of_business' => $data ['nature_of_business'],
					'established_in' => $data ['established_in'],
					'description' => $data ['description_user'],
					'principal_place' => $data ['principal_place'],
					'current_turnover' => $data ['current_turnover'],
					'first_year_turnover' => $data ['first_year_turnover'],
					'second_year_turnover' => $data ['second_year_turnover'],
					'third_year_turnover' => $data ['third_year_turnover'],
					'contact_firstname' => $data ['contact_firstname'],
					'contact_lastname' => $data ['contact_lastname'],
					'contact_designation' => $data ['contact_designation'],
					'contact_email' => $data ['contact_email'],
					'contact_mobile' => $data ['contact_mobile'],
					'contact_landline' => $data ['contact_landline'],
					'lkp_employee_strength_id' => $data ['employee_strengths'],
					'lkp_industry_id' => $data ['lkp_industry'],
					'lkp_speciality_id' => $data ['lkp_specialities'],
					'gta' => $data ['gta'],
					'service_tax_number' => $data ['service_tax_number'],
					'tin' => $data ['tin'],
					'bankname' => $data ['bankname'],
					'branchname' => $data ['branchname'],
					'in_corporation_file' => $data ['in_corporation_file'],
					'tin_filepath' => $data ['tin_filepath'],
					'sales_tax_filepath' => $data ['sales_tax_filepath'],
					'service_tax_filepath' => $data ['service_tax_filepath'],
					'central_excise_filepath' => $data ['central_excise_filepath'],
					'gta_filepath' => $data ['gta_filepath'],
					'pancard_filepath' => $data ['pancard_filepath'],
					'updated_by' => $this->user_pk,
					'updated_at' => $updatedAt,
					'updated_ip' => $updatedIp 
			) );
			CommonComponent::auditLog ( $id, 'seller_details' );
			Session::put ( 'company_name', $data ['firstname'] . $data ['firstname'] ); // session for future use
			$services = array ();			
			$intracityArea = array ();
			$pamArea = array ();
			$isSelect_intracity = '';
			$isSelect_pm = '';
			$seller_services = array ();
			
			// see if value has been posted
		if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {			
					$services = $_POST ['services'];
					$seller_services = $services;					
				}
				
			if (!empty ( $seller_services )) {
				
				DB::table ( 'seller_services' )->where ( 'user_id', $this->user_pk )->delete ();
			}
			
				// for deleting / inserting pm & intracity areas
				if (in_array ( 3, $seller_services )) {
					$isSelect_intracity = 1;
				}
				if (in_array ( 15, $seller_services )) {
					$isSelect_pm = 1;
				}
				
				foreach ( $seller_services as $service ) {
					
					$seller_services_save = new SellerService ();
					$seller_services_save->user_id = $this->user_pk;
					$seller_services_save->lkp_service_id = $service;
					$seller_services_save->created_by = $this->user_pk;
					$seller_services_save->created_at = $updatedAt;
					$seller_services_save->created_ip = $updatedIp;
					$seller_services_save->save ();
					CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
				}
			
			
			if (isset ( $_POST ['intracity_locality'] ) && (! empty ( $_POST ['intracity_locality'] != '' ))) {
				$intracityArea = $_POST ['intracity_locality'];
				
				DB::table ( 'seller_intracity_localities' )->where ( 'user_id', $this->user_pk )->delete ();
				
				if ($isSelect_intracity == 1) {
					foreach ( $intracityArea as $intracity ) {
						if($intracity!='Default'){
							$intracityArea_save = new SellerIntracityLocality ();
							$intracityArea_save->user_id = $this->user_pk;
							$intracityArea_save->lkp_locality_id = $intracity;
							$intracityArea_save->created_by = $this->user_pk;
							$intracityArea_save->created_at = $updatedAt;
							$intracityArea_save->created_ip = $updatedIp;
							$intracityArea_save->save ();
							CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
						}	
					}
				}
			}
			
			if (isset ( $_POST ['pm_city'] ) && ($_POST ['pm_city'] != '')) {
				$pamArea = $_POST ['pm_city'];
				
				DB::table ( 'seller_pm_cities' )->where ( 'user_id', $this->user_pk )->delete ();
				
				if ($isSelect_pm == 1) {
					
					foreach ( $pamArea as $pam ) {
						
						$pam_save = new SellerPmCity ();
						$pam_save->user_id = $this->user_pk;
						$pam_save->lkp_city_id = $pam;
						$pam_save->created_by = $this->user_pk;
						$pam_save->created_at = $updatedAt;
						$pam_save->created_ip = $updatedIp;
						$pam_save->save ();
						CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
					}
				}
			}
			$username = $data ['firstname'] . " " . $data ['lastname'];
			if ($sellerLogo != '') {
				User::where ( "id", $this->user_pk )->update ( array (
						'logo' => $sellerLogo,
						'username' => $username,
						'pannumber' => $data ['pannumber'],
						'updated_at' => $updatedAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $updatedIp 
				) );
			} else {
				User::where ( "id", $this->user_pk )->update ( array (
						'username' => $username,
						'pannumber' => $data ['pannumber'],
						'updated_at' => $updatedAt,
						'updated_by' => $this->user_pk,
						'updated_ip' => $updatedIp 
				) );
			}
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			return '1';
		} catch ( Exception $ex ) {
			
			return '0';
		}
	}
	
	/**
	 * SOCIAL LOGIN
	 *
	 * /
	 */
	public function facebook_redirect() {
		Log::info ( 'Anonymous User is redirected Facebook:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		// echo $_GET['key'];exit;
		if (isset ( $_GET ['key'] ))
			return Socialize::with ( 'facebook' )->redirect ( $_GET ['key'] );
		else
			return Socialize::with ( 'facebook' )->redirect ();
	}
	
	// to get authenticate user data
	public function facebook() {
		Log::info ( 'Anonymous User is trying to register with Facebook:' . $this->randomId, array (
				'c' => '1' 
		) );
		if (isset ( $_REQUEST ['error'] ) && $_REQUEST ['error'] == "access_denied") {
			return Redirect ( '/' )->with ( 'message', 'Facebook is not allow you to access data if you were click deny, Please try again by doing allow.' );
		}
		// print_r($_SERVER);exit;
		$user = Socialize::with ( 'facebook' )->user ();
		$emailExist = User::where ( 'email', $user->email )->get ();
		if (count ( $emailExist ) > 0) {
			if (strstr ( CURRENT_URL, "login" )) {
				// echo $_GET['keystr'];exit;
				if ($emailExist [0]->is_facebook == 1 && $emailExist [0]->fb_identifier != "") {
					if (Auth::loginUsingId ( $emailExist [0]->id )) {
						return redirect ( '/home' );
					}
				} else {
					return Redirect ( '/auth/login' )->with ( 'message', 'please login as normal user.' );
				}
			} else
				return Redirect ( '/' )->with ( 'message', 'Email already exist.' );
		} else
			return view ( 'auth.select', array (
					'email' => $user->email,
					'identifier' => $user->id,
					'provider' => 'facebook',
					'username' => $user->name 
			) );
		
		// print_r($user);die;
	}
	public function linkedin_redirect() {
		Log::info ( 'Anonymous User is redirected to linkedIn:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		// return Socialize::with('linkedin')->redirect();
		if (isset ( $_GET ['key'] ))
			return Socialize::with ( 'linkedin' )->redirect ( $_GET ['key'] );
		else
			return Socialize::with ( 'linkedin' )->redirect ();
	}
	
	// to get authenticate user data
	public function linkedin() {
		Log::info ( 'Anonymous User is trying to register with LinkedIn:' . $this->randomId, array (
				'c' => '1' 
		) );
		if (isset ( $_REQUEST ['error'] ) && $_REQUEST ['error'] == "access_denied") {
			return Redirect ( '/' )->with ( 'message', 'LinkedIn is not allow you to access data if you were click deny, Please try again by doing allow.' );
		}
		// $user = Socialize::with('linkedin')->user();
		$user = Socialize::with ( 'linkedin' )->user ();
		$emailExist = User::where ( 'email', $user->email )->get ();
		if (count ( $emailExist ) > 0) {
			if (strstr ( CURRENT_URL, "login" )) {
				// echo $_GET['keystr'];exit;
				if ($emailExist [0]->is_linkedin == 1 && $emailExist [0]->linkedin_identifier != "") {
					// echo "---";exit;
					if (Auth::loginUsingId ( $emailExist [0]->id )) {
						return redirect ( '/home' );
					}
				} else {
					return Redirect ( '/auth/login' )->with ( 'message', 'please login as normal user.' );
				}
			} else
				return Redirect ( '/' )->with ( 'message', 'Email already exist.' );
		} else
			return view ( 'auth.select', array (
					'email' => $user->email,
					'identifier' => $user->id,
					'provider' => 'linkedin',
					'username' => $user->name 
			) );
	}
	public function google_redirect() {
		Log::info ( 'Anonymous User is redirected to Google+:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		// return Socialize::with('google')->redirect();
		if (isset ( $_GET ['key'] ))
			return Socialize::with ( 'google' )->redirect ( $_GET ['key'] );
		else
			return Socialize::with ( 'google' )->redirect ();
	}
	
	// to get authenticate user data
	public function google() {
		Log::info ( 'Anonymous User is trying to register with Google:' . $this->randomId, array (
				'c' => '1' 
		) );
		if (isset ( $_REQUEST ['error'] ) && $_REQUEST ['error'] == "access_denied") {
			return Redirect ( '/' )->with ( 'message', 'Gmail is not allow you to access data if you were click deny, Please try again by doing allow.' );
		}
		// $user = Socialize::with('google')->user();
		$user = Socialize::with ( 'google' )->user ();
		
		$emailExist = User::where ( 'email', $user->email )->get ();
		if (count ( $emailExist ) > 0) {
			if (strstr ( CURRENT_URL, "login" )) {
				// echo $_GET['keystr'];exit;
				if ($emailExist [0]->is_google == 1 && $emailExist [0]->google_identifier != "") {
					if (Auth::loginUsingId ( $emailExist [0]->id )) {
						return redirect ( '/home' );
					}
				} else {
					return Redirect ( '/auth/login' )->with ( 'message', 'please login as normal user.' );
				}
			} else
				return Redirect ( '/' )->with ( 'message', 'Email already exist.' );
		} else
			return view ( 'auth.select', array (
					'email' => $user->email,
					'identifier' => $user->id,
					'provider' => 'google',
					'username' => $user->name 
			) );
	}
	public function socialRegister() {
		Log::info ( 'Anonymous User is now actually going to registered with userDetails by socialRegister action:' . $this->randomId, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL );
		if (isset ( $_POST ['user_email'] )) {
			CommonComponent::activityLog ( "USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL );
			
			$data = Input::only ( [ 
					'user_email',
					'password',
					'is_business',
					'username',
					'identifier',
					'provider' 
			] );
			
			$newUser = RegisterController::create ( $data );
			
			if ($newUser == 1) {
				
				return Redirect ( 'register/buyer' )->with ( 'message', 'Registered successfully.' );
			} elseif ($newUser == 0) {
				$otp = '';
				return Redirect ( 'auth.register', array (
						'otp' => $otp 
				) )->withInput ();
			} else {
				
				return view ( 'auth.register', array (
						'otp' => $newUser 
				) );
			}
		}
	}

	/**
	* Change Password Page.
	* function to change password 
	* @param  int  $id
	* @return Response
	*/
    public function changeUserPassword(Request $request)
    {
    	if ( !empty($_POST) ){
			//defining validation messages
			$messages = [
			'old_password' => 'The old password field is required!',
			'password' => 'The password field is required!',
			'password_confirmation' => 'The confirm password field is required!'
			];
			//defining validation rules
			$rules = ['old_password' => 'required',
					  'password' => 'required',
					  'password_confirmation' => 'required'
			];
			//validating form request
			$this->validate($request, $rules, $messages);
			//Verify the old password and match the new and confirm passwords logic to redirect if fail	
			/* Verifying the password with the DB value */
			$term = $request->input('old_password');
			$status = '';	
			$user = DB::table('users')
			->where(['users.id' => Auth::id()])
			->select('users.id', 'users.password') 
			->get();			
			$status = '';
			if (Hash::check($term, $user[0]->password))
			{
				$status = "yes";				
			} else {			
				return redirect('changepassword')
				->with('error', 'Old Password is incorrect,please enter correct password..!');	
			}			
					
			if($request->input('password') != $request->input('password_confirmation')){
				return redirect('changepassword')
				->with('error', 'New Password and Confirm Password are not matching..!');
			}

			//Update the password 
			if($status == "yes"){
			$user_password =  bcrypt($request->input('password'));

			$updatedAt = date( 'Y-m-d H:i:s' );
			$updatedIp = $_SERVER['REMOTE_ADDR'];
			$updatedBy = Auth::id();
			User::where(["id" => Auth::id()])
			->update(array(
			'password' => $user_password,
			'updated_at' => $updatedAt,
			'updated_ip' => $updatedIp,
			'updated_by' => $updatedBy));
			}

			return redirect('changepassword')
			->with('message', 'Your password is changed successfully');
		}
    	return view('users.changepassword');
    	
    }
	
	//USER ROLE TOGGLING
	
	public function checkToggleRole(){
		//check if user is in seller OR buyer
		
		if(Auth::User()->lkp_role_id == SELLER)
		{
		if(Auth::User()->is_business == '0'){
			$table= 'buyer_details';
		
		}
		else{
			$table = 'buyer_business_details';
		}
		
		
		if(Auth::User()->secondary_role_id=='0')
		{	
			return 'pop_up';
		}
		else{ 
			Session::put('last_login_role_id','1');
			return 'no_pop_up';
		}
		}
		elseif(Auth::User()->lkp_role_id == BUYER){

			if(Auth::User()->secondary_role_id=='0')
			{
				return 'pop_up';
			}
			else{
				return 'no_pop_up';}
			
		}
		
		
	}
	
	public function toggleUserRole(){

		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		//For Seller(if toggles the role)		
		if(Auth::User()->lkp_role_id == '2'){
		
			if(Auth::User()->is_business == '1')
			{
				$dataTable='sellers';
			}else{
				$dataTable='seller_details';
			}

			$sellerDetails = DB::table($dataTable)->where('user_id', Auth::User()->id)->first();

			//save seller details as buyer details
			if(Auth::User()->is_business == '1'){
			
				$buyerDetails = DB::table('buyer_business_details')
					->where('user_id', Auth::User()->id)->first();

				if(empty($buyerDetails)){
					
					Log::info ('Creat_buyer_business action is triggered by User:' . $this->user_pk,
					 	array ('c' => '1') 
					);
			
					CommonComponent::activityLog ( "CREATE_BUSINESS_BUYER", CREATE_BUSINESS_BUYER, 
						0, HTTP_REFERRER, CURRENT_URL 
					);
			
					$buyerBusiness = new BuyerBusinessDetail();	
					$buyerBusiness->user_id = $this->user_pk;
					$buyerBusiness->name = $sellerDetails->name;
					$buyerBusiness->lkp_business_type_id = $sellerDetails->lkp_business_type_id;
					$buyerBusiness->lkp_country_id = $sellerDetails->lkp_country_id;

					if ($buyerBusiness->lkp_business_type_id == 8) {
						$buyerBusiness->other_business_type = $sellerDetails->other_business_type;
					}

					$buyerBusiness->lkp_state_id = $sellerDetails->lkp_state_id;
					$buyerBusiness->principal_place = $sellerDetails->principal_place;
					$buyerBusiness->address = $sellerDetails->address;
					$buyerBusiness->pincode = $sellerDetails->pincode;
					$buyerBusiness->current_turnover = $sellerDetails->current_turnover;
					$buyerBusiness->first_year_turnover = $sellerDetails->first_year_turnover;
					$buyerBusiness->second_year_turnover = $sellerDetails->second_year_turnover;
					$buyerBusiness->third_year_turnover = $sellerDetails->third_year_turnover;
					$buyerBusiness->contact_firstname = $sellerDetails->contact_firstname;
					$buyerBusiness->contact_lastname = $sellerDetails->contact_lastname;
					$buyerBusiness->contact_designation = $sellerDetails->contact_designation;
					$buyerBusiness->contact_email = $sellerDetails->contact_email;
					$buyerBusiness->contact_mobile = $sellerDetails->contact_mobile;
					$buyerBusiness->contact_landline = $sellerDetails->contact_landline;
					$buyerBusiness->gta = $sellerDetails->gta;
					$buyerBusiness->tin = $sellerDetails->tin;
					$buyerBusiness->service_tax_number = $sellerDetails->service_tax_number;
					$buyerBusiness->bankname = $sellerDetails->bankname;
					$buyerBusiness->branchname = $sellerDetails->branchname;
					$buyerBusiness->in_corporation_file = $sellerDetails->in_corporation_file;
					$buyerBusiness->tin_filepath = $sellerDetails->tin_filepath;
					$buyerBusiness->gta_filepath = $sellerDetails->gta_filepath;
					$buyerBusiness->pancard_filepath = $sellerDetails->pancard_filepath;
					$buyerBusiness->service_tax_filepath = $sellerDetails->service_tax_filepath;
					$buyerBusiness->central_excise_filepath = $sellerDetails->central_excise_filepath;
					$buyerBusiness->sales_tax_filepath = $sellerDetails->sales_tax_filepath;
					$buyerBusiness->created_at = $createdAt;
					$buyerBusiness->created_ip = $createdIp;
					$buyerBusiness->created_by = $this->user_pk;
			
					if ($buyerBusiness->save ()) {
						Session::put('last_login_role_id','1');
							
						CommonComponent::auditLog ( $this->user_pk, 'buyer_business_details' );
						Session::put ( 'company_name', $buyerBusiness->name );
						$username = $buyerBusiness->name;
						User::where ( "id", $this->user_pk )->update ( array (
							'username' => $username,
							'secondary_role_id' => '1',
							'updated_at' => $createdAt,
							'updated_by' => $this->user_pk,
							'updated_ip' => $createdIp
						) );
							
						Session::put ( 'buyerName', $username );
						CommonComponent::auditLog ( $this->user_pk, 'users' );
						return json_encode(['business'=>'1','success'=>'1']);

					} else {
						return json_encode(['business'=>'1','success'=>'0']);
					}
				}
				else{
							
					
					if(
					User::where ( "id", $this->user_pk )->update ( array (
					'secondary_role_id' => '1',
					'updated_at' => $createdAt,
					'updated_by' => $this->user_pk,
					'updated_ip' => $createdIp
					) )){
						Session::put('last_login_role_id','1');
					return json_encode(['business'=>'1','success'=>'1']);
					}	
					
				}

			}
			else{
				
				Log::info ( 'Saving Individual Seller details in buyer table as confirmed by User:' . $this->user_pk, array ( 'c' => '1' ) 
				);
			
				CommonComponent::activityLog ( "CREATE_BUYER", CREATE_BUYER, 0, HTTP_REFERRER, CURRENT_URL );
			
				$buyerDetails = new BuyerDetail ();
				$createdAt = date ( 'Y-m-d H:i:s' );
				$createdIp = $_SERVER ["REMOTE_ADDR"];
			
				$buyerDetails->user_id = $this->user_pk;
				$buyerDetails->firstname = $sellerDetails->firstname;
				$buyerDetails->lastname = $sellerDetails->lastname;
				$buyerDetails->mobile = $sellerDetails->contact_mobile;
				$buyerDetails->landline = $sellerDetails->landline;
				$buyerDetails->contact_email = $sellerDetails->contact_email;
				$buyerDetails->address = $sellerDetails->address;
				$buyerDetails->pincode = $sellerDetails->pincode;
				$buyerDetails->principal_place = $sellerDetails->principal_place; // Selelr toggle buyer principal place
				$buyerDetails->created_at = $createdAt;
				$buyerDetails->created_ip = $createdIp;
				$buyerDetails->created_by = $this->user_pk;
			
				if ($buyerDetails->save ()) {
					Session::put('last_login_role_id','1');
					CommonComponent::auditLog ( $buyerDetails->id, 'buyer_details' );
					
					$username = $buyerDetails->firstname . " " . $buyerDetails->lastname;
					User::where ( "id", $this->user_pk )->update ( array (
							'username' => $username,
							'secondary_role_id' => '1',
							'updated_at' => $createdAt,
							'updated_by' => $this->user_pk,
							'updated_ip' => $createdIp 
					) );
					CommonComponent::auditLog ( $this->user_pk, 'users' );
					
					Session::put ( 'buyerName', $username );
					
					return json_encode(['business'=>'0','success'=>'1']);
				} else {
					return json_encode(['business'=>'0','success'=>'0']);
				}

			}

		} //Procedure if Buyer toggles the role	
		else
		{			
		
			if (Auth::User ()->secondary_role_id == '0') {
				
				if (Auth::User ()->is_business == '1') {
					
					$is_seller_business = DB::table ( 'sellers' )->where ( 'user_id', '=', $this->user_pk )->first ();

					if (empty ( $is_seller_business )) {
						return json_encode ( [ 
								'business' => '1',
								'redirect' => '/register/edit/seller_business' 
						] );
					} else {
						if (Auth::User ()->is_buyer_paid == 0) {
							return json_encode ( [ 
									'business' => 'not_paid',
									'redirect' => '/thankyou_seller' 
							] );
						} else {
							
							return json_encode ( [ 
									'business' => '1',
									'redirect' => '/home' 
							] );
						}
					}

				} else {	

					$is_seller = DB::table ( 'seller_details' )->where ( 'user_id', '=', $this->user_pk )->first ();
					if (empty ( $is_seller )) {
						return json_encode ( [ 
								'business' => '0',
								'redirect' => '/register/buyer_switch_seller' 
						] );
					} else {
						if (Auth::User ()->is_buyer_paid == 0) {
							return json_encode ( [ 
									'business' => 'not_paid',
									'redirect' => '/thankyou_seller' 
							] );
						} else {
							Session::put('last_login_role_id','2');
							return json_encode ( [ 
									'business' => '1',
									'redirect' => '/home' 
							] );
						}
					}
				}

			} else {
				Session::put('last_login_role_id','2');
				return json_encode ( [ 
						'business' => '1',
						'redirect' => '/home' 
				] );
			}
		}
	
	}

	public function switchBuyer(){
			
		if(Input::all()){
			
			$data = Input::all();

			Log::info ( 'Create individual buyer function triggered by User:' . $this->user_pk, 
				array ( 'c' => '1' ) 
			);
			
			CommonComponent::activityLog ( "CREATE_BUYER", CREATE_BUYER, 0, HTTP_REFERRER, CURRENT_URL 
			);
			
			$buyerDetails = new BuyerDetail();
			
			$createdAt = date ( 'Y-m-d H:i:s' );
			$createdIp = $_SERVER ["REMOTE_ADDR"];
			
			$buyerDetails->user_id = $this->user_pk;
			$buyerDetails->firstname = $data ['firstname'];
			$buyerDetails->lastname = $data ['lastname'];
			$buyerDetails->mobile = $data ['mobile'];
			$buyerDetails->landline = $data ['landline'];
			$buyerDetails->contact_email = $data ['contact_email'];
			$buyerDetails->address = $data ['address'];
			$buyerDetails->pincode = $data ['pincode'];
			$buyerDetails->principal_place  = $data ['principal_place'];;
			$buyerDetails->created_at = $createdAt;
			$buyerDetails->created_ip = $createdIp;
			$buyerDetails->created_by = $this->user_pk;
			
			if ($buyerDetails->save ()) {
				Session::put('last_login_role_id','1');
				CommonComponent::auditLog ( $buyerDetails->id, 'buyer_details' );
					
				$username = $buyerDetails->firstname . " " . $buyerDetails->lastname;
				User::where ( "id", $this->user_pk )->update ( array (
				'username' => $username,
				'updated_at' => $createdAt,
				'updated_by' => $this->user_pk,
				'updated_ip' => $createdIp
				) );
				CommonComponent::auditLog ( $this->user_pk, 'users' );
					
				Session::put ( 'buyerName', $username );
					
				return Redirect ( '/home' )->with ( 'edit_success_message', 'Buyer details are saved successfully.' );
			} else {
				return Redirect ( '/switch_buyer' )->with ( 'error_message', 'Error occured while saving.' );
			}
			
		}

		//@Raman - Getting
		/*$userRecord = DB::table ('users')->where ('id', '=', $this->user_pk )->value('is_business');
		dd($userRecord);*/

		return view ( 'auth.switch_buyer');
		
	}

	public function getBuyerData(){
		if(Auth::User()->is_business == '1'){
			return json_encode(['business'=>'1','redirect'=>'/register/edit/buyer_business']);
	}
	else{
		return json_encode(['business'=>'0','redirect'=>'/switch_buyer']);
		
	}
	}
	public function buyerToggleSellerBusiness(){
	
		Log::info ( 'User submitted Seller business registration form:' . $this->user_pk, array (
				'c' => '1' 
		) );
		
		CommonComponent::activityLog ( "SELLER_BUSINESS_REGISTRATION", SELLER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			$data['secondary_role_id']='2';
			$sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';
			
			if (is_dir ( $sellerBusinessDirectory )) {
			} else {
				mkdir ( $sellerBusinessDirectory, 0777, true );
			}
			
			if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
				$file = 'in_corporation_file';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['in_corporation_file'] = $uploadedFile;
			} else {
				$data ['in_corporation_file'] = '';
			}
			if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
				$file = 'tin_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['tin_filepath'] = $uploadedFile;
			} else {
				$data ['tin_filepath'] = '';
			}
			if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
				$file = 'gta_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['gta_filepath'] = $uploadedFile;
			} else {
				$data ['gta_filepath'] = '';
			}
			if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
				$file = 'pancard_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['pancard_filepath'] = $uploadedFile;
			} else {
				$data ['pancard_filepath'] = '';
			}
			if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
				$file = 'service_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['service_tax_filepath'] = $uploadedFile;
			} else {
				$data ['service_tax_filepath'] = '';
			}
			if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
				$file = 'central_excise_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['central_excise_filepath'] = $uploadedFile;
			} else {
				$data ['central_excise_filepath'] = '';
			}
			
			if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
				$file = 'sales_tax_filepath';
				
				$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
				$data ['sales_tax_filepath'] = $uploadedFile;
			} else {
				$data ['sales_tax_filepath'] = '';
			}
		
			$newRole = RegisterController::selectRole ( '2' );
			if ($newRole == '1') {
				$newBuyer = RegisterController::createSellerBusiness ( $data );
			} else {
				return Redirect ( 'register/seller_business' );
			}
			
			if ($newBuyer == 1) {
				
				return Redirect ( 'thankyou_seller' )->with ( 'message', 'Seller business details are submitted successfully.' );
			} else {
				return Redirect ( 'register/seller_business' );
			}
		}
	
	}
	public function switchRoles(){
		
		if(isset($_POST['switchTo']) && $_POST['switchTo']!=''){
			Session::put('last_login_role_id',$_POST['switchTo']);
			if(Auth::User()->secondary_role_id !=0)			
			 {			
			
			return '1';			
                        }else {return '0'  ;}
                        }else {return '0'  ;}
	}
	
	public function getPrincipalPlace(){
		$pin = Input::get('prop_pinid');
		$getPrincipalPlace = DB::table('lkp_ptl_pincodes as lpp')
		->where('lpp.pincode', '=', $pin)
		->select('lpp.districtname','lpp.id')
		->first();
		if(isset($getPrincipalPlace->districtname)):
			return $getPrincipalPlace->districtname;
		else:
			return '';
		endif;
	}
	
	public function getPincodeDetails(){
		$pin = Input::get('prop_pinid');
		$getPrincipalPlace = DB::table('lkp_ptl_pincodes as lpp')
		->where('lpp.pincode', '=', $pin)
		->select('lpp.districtname','lpp.id','lpp.divisionname','lpp.statename','lpp.postoffice_name')
		->first();
		return Response::json($getPrincipalPlace);
	}

	 public function validateUserEmail(){
  		
  		 $email = Input::get('business_emailId');
            
          $validator = DB::table('sellers')->where('contact_email',$email)->get();

          if(!empty($validator)){
          	
          	return 'true';

          }
          else{

          	return 'false';
          
          }

          return $validator;
     }

      public function validatePancard(){
  		
  		 $pannumber = Input::get('business_pan');
            
          $validator = DB::table('sellers')->where('pannumber',$pannumber)->get();

          if(!empty($validator)){
          	
          	return 'true';

          }
          else{

          	return 'false';
          
          }

          return $validator;
     }

    public function getSectorTypes($id){

		$sectors = DB::table('sector_type')->where('industry_type',$id)->get();
		$str  = '<option value=""></option>';
		foreach($sectors as $sector) {
			$str.='<option value="'.$sector->sector_id.'">'.$sector->sector_type.'</option>';
		}
		echo $str;
    
   }
     
	
	public function buyerSwitchSeller(){

		Log::info ( 'Seller has viewed seller individual registration page:' . $this->user_pk, 
			array (	'c' => '1') 
		);
		
		CommonComponent::activityLog ( "DISPALY_SELLER_INDIVIDUAL", DISPALY_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
		
		$userRecord = \DB::table ( 'users' )->where ( 'id', '=', $this->user_pk )->first ();
		
		if ($userRecord->email != null) {
			$user_email = $userRecord ->email;
		} elseif ($userRecord->phone != null) {
			$user_phone = $userRecord->phone;
		}			
		
			
		// Getting Buyer details	
		$buyer = DB::table ( 'buyer_details' )
			->where ( 'user_id', '=', $this->user_pk )
			->first();

		$stateList = \DB::table ( 'lkp_states' )->orderBy ( 'state_name', 'asc' )->lists ( 'state_name', 'id' );
		$country = \DB::table ( 'lkp_countries' )->orderBy ( 'country_name', 'asc' )->lists ( 'country_name', 'id' );
	
		$packaging = \DB::table ( 'lkp_services' )->where ( 'id', '>', '10' )->lists ( 'service_name', 'id' );
		$locality = \DB::table ( 'lkp_localities' )->orderBy ( 'locality_name', 'asc' )->lists ( 'locality_name', 'id' );
		$cities = \DB::table ( 'lkp_cities' )->orderBy ( 'city_name', 'asc' )->lists ( 'city_name', 'id' );
		$myservices =  \DB::table ( 'lkp_services' )->select ( 'service_name','group_name','service_crumb_name','service_image_path' , 'id')->get();

		$lkp_industry = $this->getIndustries();
		$getSpecialities = $this->getSpecialities();
		$getEmployeeStrengths = $this->getEmployeeStrengths();
		
		$getYearofEstablished = CommonComponent::getYearofEstablished(); // @jagadeesh-29042016
		
		return view ( 'auth.buyer_to_seller', array (
				'stateList' => $stateList,
				'country' => $country,
				'services' => $myservices,
				'packaging' => $packaging,
				'cities' => $cities,
				'buyer' => $buyer,
				'userRecord' => $userRecord,
				'locality' => $locality,
				'user_email' => $user_email,
				'lkp_industry' => $lkp_industry,
				'getSpecialities' => $getSpecialities,
				'getEmployeeStrengths' => $getEmployeeStrengths,
			    'getYearofEstablished'=> $getYearofEstablished // @jagadeesh-29042016
		) );
			
	}


	public function switchSeller(){

		Log::info ( 'User has submitted seller individual registration form:' . $this->user_pk, array (
		'c' => '1'
				) );
		
		// CommonComponent::activityLog ( "SELLER_BUSINESS_REGISTRATION", SELLER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL );
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			
			$data ['mobile'] = $data ['contact_mobile'];
			$data ['landline'] = $data ['contact_landline'];
			
// 			$newBuyer = RegisterController::createBuyer ( $data );
				
// 			if ($newBuyer == '1') {
		
				$sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';
		
				if (is_dir ( $sellerBusinessDirectory )) {
				} else {
					mkdir ( $sellerBusinessDirectory, 0777, true );
				}
		
				if (isset ( $_FILES ['in_corporation_file'] ) && ! empty ( $_FILES ['in_corporation_file'] ['name'] )) {
					$file = 'in_corporation_file';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['in_corporation_file'] = $uploadedFile;
				} else {
					$data ['in_corporation_file'] = '';
				}
				if (isset ( $_FILES ['tin_filepath'] ) && ! empty ( $_FILES ['tin_filepath'] ['name'] )) {
					$file = 'tin_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['tin_filepath'] = $uploadedFile;
				} else {
					$data ['tin_filepath'] = '';
				}
				if (isset ( $_FILES ['gta_filepath'] ) && ! empty ( $_FILES ['gta_filepath'] ['name'] )) {
					$file = 'gta_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['gta_filepath'] = $uploadedFile;
				} else {
					$data ['gta_filepath'] = '';
				}
				if (isset ( $_FILES ['pancard_filepath'] ) && ! empty ( $_FILES ['pancard_filepath'] ['name'] )) {
					$file = 'pancard_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['pancard_filepath'] = $uploadedFile;
				} else {
					$data ['pancard_filepath'] = '';
				}
				if (isset ( $_FILES ['service_tax_filepath'] ) && ! empty ( $_FILES ['service_tax_filepath'] ['name'] )) {
					$file = 'service_tax_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['service_tax_filepath'] = $uploadedFile;
				} else {
					$data ['service_tax_filepath'] = '';
				}
				if (isset ( $_FILES ['central_excise_filepath'] ) && ! empty ( $_FILES ['central_excise_filepath'] ['name'] )) {
					$file = 'central_excise_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['central_excise_filepath'] = $uploadedFile;
				} else {
					$data ['central_excise_filepath'] = '';
				}
		
				if (isset ( $_FILES ['sales_tax_filepath'] ) && ! empty ( $_FILES ['sales_tax_filepath'] ['name'] )) {
					$file = 'sales_tax_filepath';
						
					$uploadedFile = RegisterController::checkUpload ( $sellerBusinessDirectory, $file );
					$data ['sales_tax_filepath'] = $uploadedFile;
				} else {
					$data ['sales_tax_filepath'] = '';
				}
		
				
				$newSeller = RegisterController::switchCreateSeller ( $data );
		
				if ($newSeller == 1) {
						
					return Redirect ( 'thankyou_seller' )->with ( 'message', 'Seller business details are submitted successfully.' );
				} else {
					return Redirect ( 'register/buyer_switch_seller' );
				}
// 			} else {
// 				return Redirect ( 'register/buyer_switch_seller' )->with ( 'error_message', 'Error occured while saving, Please try again after sometime.' );
// 			}
		}
	}
	

	public function switchCreateSeller($data){

	Log::info ( 'User has submitted seller individual registration form:' . $this->user_pk, array (
	'c' => '1'
			) );
	
	CommonComponent::activityLog ( "CREATE_SELLER_INDIVIDUAL", CREATE_SELLER_INDIVIDUAL, 0, HTTP_REFERRER, CURRENT_URL );
	
	$sellerIndividual = new SellerDetail ();
	$services = array ();
	$createdAt = date ( 'Y-m-d H:i:s' );
	$createdIp = $_SERVER ["REMOTE_ADDR"];
	
	$sellerIndividual->user_id = $this->user_pk;
	$sellerIndividual->firstname = $data ['firstname'];
	$sellerIndividual->lastname = $data ['lastname'];
	$sellerIndividual->landline = $data ['landline'];
	$sellerIndividual->nature_of_business = $data ['nature_of_business'];
	$sellerIndividual->established_in = $data ['established_in'];
	$sellerIndividual->principal_place = $data ['principal_place'];
	$sellerIndividual->address = $data ['address'];
	$sellerIndividual->pincode = $data ['pincode'];
	$sellerIndividual->lkp_employee_strength_id = $data ['employee_strengths'];
	$sellerIndividual->lkp_industry_id = $data ['lkp_industry'];
	$sellerIndividual->lkp_speciality_id = $data ['lkp_specialities'];
	$sellerIndividual->principal_place_pincode = $data ['principal_place_pincode'];
	$sellerIndividual->current_turnover = $data ['current_turnover'];
	$sellerIndividual->first_year_turnover = $data ['first_year_turnover'];
	$sellerIndividual->second_year_turnover = $data ['second_year_turnover'];
	$sellerIndividual->third_year_turnover = $data ['third_year_turnover'];
	$sellerIndividual->contact_firstname = $data ['contact_firstname'];
	$sellerIndividual->contact_lastname = $data ['contact_lastname'];
	$sellerIndividual->contact_designation = $data ['contact_designation'];
	$sellerIndividual->contact_email = $data ['contact_email'];
	$sellerIndividual->contact_landline = $data ['contact_landline'];
	$sellerIndividual->contact_mobile = $data ['contact_mobile'];
	$sellerIndividual->gta = $data ['gta'];
	$sellerIndividual->tin = $data ['tin'];
	$sellerIndividual->service_tax_number = $data ['service_tax_number'];
	$sellerIndividual->bankname = $data ['bankname'];
	$sellerIndividual->branchname = $data ['branchname'];
	$sellerIndividual->in_corporation_file = $data ['in_corporation_file'];
	$sellerIndividual->tin_filepath = $data ['tin_filepath'];
	$sellerIndividual->gta_filepath = $data ['gta_filepath'];
	$sellerIndividual->pancard_filepath = $data ['pancard_filepath'];
	$sellerIndividual->service_tax_filepath = $data ['service_tax_filepath'];
	$sellerIndividual->central_excise_filepath = $data ['central_excise_filepath'];
	$sellerIndividual->sales_tax_filepath = $data ['sales_tax_filepath'];
	$sellerIndividual->created_at = $createdAt;
	$sellerIndividual->created_ip = $createdIp;
	$sellerIndividual->created_by = $this->user_pk;
	
	try {
		if ($sellerIndividual->save ()) {
	
			CommonComponent::auditLog ( $sellerIndividual->id, 'seller_details' );
			Session::put ( 'company_name', $sellerIndividual->name ); // session for future use
			$intracityArea = array ();
			$pamArea = array ();
			$services = array ();			
			// see if value has been posted
			if (isset ( $_POST ['services'] ) && (!empty ( $_POST ['services'] ))) {
				$services = $_POST ['services'];
				$seller_services = $services;
					
			}
			if (! empty ( $seller_services )) {
				foreach ( $seller_services as $service ) {
	
					$seller_services_save = new SellerService ();
					$seller_services_save->user_id = $this->user_pk;
					$seller_services_save->lkp_service_id = $service;
					$seller_services_save->created_by = $this->user_pk;
					$seller_services_save->created_at = $createdAt;
					$seller_services_save->created_ip = $createdIp;
					$seller_services_save->save ();
					CommonComponent::auditLog ( $seller_services_save->id, 'seller_services' );
				}
			}
			if (! empty ( $_POST ['intracity_locality'] [0] ) && $_POST ['intracity_locality'] [0] != '') {
					
				$intracityArea = $_POST ['intracity_locality'];
					
				foreach ( $intracityArea as $intracity ) {
	
					$intracityArea_save = new SellerIntracityLocality ();
					$intracityArea_save->user_id = $this->user_pk;
					$intracityArea_save->lkp_locality_id = $intracity;
					$intracityArea_save->created_by = $this->user_pk;
					$intracityArea_save->created_at = $createdAt;
					$intracityArea_save->created_ip = $createdIp;
	
					$intracityArea_save->save ();
	
					CommonComponent::auditLog ( $intracityArea_save->id, 'seller_intracity_localities' );
				}
			}
			if (! empty ( $_POST ['pm_city'] [0] ) && $_POST ['pm_city'] [0] != '') {
					
				$pamArea = $_POST ['pm_city'];
				foreach ( $pamArea as $pam ) {
	
					$pam_save = new SellerPmCity ();
					$pam_save->user_id = $this->user_pk;
					$pam_save->lkp_city_id = $pam;
					$pam_save->created_by = $this->user_pk;
					$pam_save->created_at = $createdAt;
					$pam_save->created_ip = $createdIp;
	
					$pam_save->save ();
	
					CommonComponent::auditLog ( $pam_save->id, 'seller_pm_cities' );
				}
			}
			$username = $sellerIndividual->firstname . " " . $sellerIndividual->lastname;
	
				
			User::where ( "id", $this->user_pk )->update ( array (
			'username' => $username,
			'pannumber' => $data ['pannumber'],
			'updated_at' => $createdAt,
			'updated_by' => $this->user_pk,
			'updated_ip' => $createdIp
			) );
	
			CommonComponent::auditLog ( $this->user_pk, 'users' );
			return '1';
		} else {
			return '0';
		}
	} catch ( Exception $ex ) {
	}
	
	}
	
        
        /**
	 * application t&c page.
	 * Displays t&c page
	 *
	 * @return Response
	 */
	public function termsAndConditions() {
		
            try{
                    return view ( 'home.termsandconditions');
            }catch (Exception $e) {

            }
		
	}
        /**
	 * application privacy policy page.
	 * Displays privacy policy page
	 *
	 * @return Response
	 */
	public function privacyPolicy() {
		
		try{
			return view ( 'home.privacypolicy');
		}catch (Exception $e) {

		}
		
	}

	public function createOtp(){
		try{
			if (! empty ( Input::all () )) {
				//CommonComponent::sendSMS(array($phone),REGISTRATION_OTP_SMS,$msg_params);
				$mobile_number = Input::get('phone');
				$otp = rand(1000,9999);
				
				$user="Logistiks"; //your username
				$password= "$"."Marketplacelogi"; //your password
				$mobilenumbers= $mobile_number; //enter Mobile numbers comma seperated
				$message = "Dear Logitiks customer, you OTP is ". $otp .". This is for one time use only and valid for 30 mins after receiving. Thank you. Team Logistiks"; //enter Your Message
				$senderid="LSTIKS"; //Your senderid
				$messagetype="N"; //Type Of Your Message
				$DReports="Y"; //Delivery Reports
				$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
				$message = urlencode($message);
				$ch = curl_init();
				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt ($ch, CURLOPT_POSTFIELDS,
				"User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
				$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$curlresponse = curl_exec($ch); // execute
				
				Session::put ( 'register_phone', $mobile_number );
				Session::put ( 'six_otp', $otp );
				$results = array();
				$results['status'] = "success";
				$results['otp'] = $otp;
				return Response::json($results);
			}
		}catch (Exception $e) {

		}
	}

	public function validateotp()
	{
		
		try {
			$results = array();
			if (!empty (Input::all())) {
				$otp = Input::get('otp');
				if ($otp == Session::get('six_otp')) {
					$results['status'] = "success";
				} else {
					$results['status'] = "failed";
				}
			}
			return Response::json($results);

		} catch (Exception $e) {

		}
	}
	
	public function cancellationPolicy(){

      return view('cancellationPolicy');

  }
}