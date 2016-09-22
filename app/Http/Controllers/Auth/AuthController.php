<?php

namespace App\Http\Controllers\Auth;

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
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuyerDetail;
use App\Models\BuyerBusinessDetail;
use App\Models\Seller;
use App\Models\SellerIntracityLocality;
use App\Models\SellerPmCity;
use App\Models\SellerService;
use App\Models\UserOtp;
use Socialize;

class User extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'is_business'
    ];

}

class AuthController extends Controller {
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
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public $user_pk;

    public function __construct() {
        $this->middleware('guest', [
            'except' => 'getLogout'
        ]);

        $this->user_pk = Session::get('user_id');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data        	
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'phone' => 'max:20|unique:users',
                    'email' => 'email|max:100|unique:users',
                    'password' => 'required|confirmed|min:4'
                ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data        	
     * @return User
     */
    protected function create(array $data) {
        CommonComponent::activityLog("USER_CREATE", USER_CREATE, 0, HTTP_REFERRER, CURRENT_URL);

        $users = new User ();
        $userEmail = $data ['user_email'];

        if (is_numeric($userEmail)) {
            $coloumn = 'phone';
        } elseif (strpos($userEmail, '@')) {
            $coloumn = 'email';
        }
        
        if ($data['provider'] == "facebook") {
            $users->fb_identifier = $data['identifier'];
            $users->is_facebook = '1';
        } elseif ($data['provider'] == "linkedin") {
            $users->linkedin_identifier = $data['identifier'];
            $users->is_linkedin = '1';
        } elseif ($data['provider'] == "google") {
            $users->google_identifier = $data['identifier'];
            $users->is_google = '1';
        }
        if (isset($data['username'])) {
            $users->username = $data['username'];
        }

        $createdAt = date('Y-m-d H:i:s');
        $createdIp = $_SERVER ["REMOTE_ADDR"];

        $users->$coloumn = $userEmail;
        $users->password = bcrypt($data ['password']);
        $users->lkp_role_id = '1';
        $users->is_business = $data ['is_business'];
        $users->created_at = $createdAt;
        $users->created_ip = $createdIp;

        if ($users->save()) {
            // Maintaining a log of data for buyer quotes
            CommonComponent::auditLog($users->id, 'users');

            $lastInsertedId = $users->id;
            Session::put('user_id', $users->id);
            Session::put('phone', $users->email);
            Session::put('email', $users->email);

            if ($coloumn == 'phone') {
                $otp = rand(100, 999) . "" . time();
                // insert otp in user_otps table and
                // reload the register page with otp alert
                DB::table('user_otps')->insert(array(
                    'user_id' => $lastInsertedId,
                    'otp' => $otp,
                    'validity' => date("Y-m-d H:i:s", strtotime("+7 days")),
                    'is_active' => '1',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $lastInsertedId,
                    'created_ip' => $_SERVER ["REMOTE_ADDR"]
                ));
                CommonComponent::auditLog($this->user_pk, 'user_otps');

                return $otp;
            }


            return '1';
        } else {
            return '0';
        }
    }

    /**
     * Registration.
     */
    protected function register() {
        if (isset($_POST ['submitRegister'])) {
            CommonComponent::activityLog("USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL);

            $data = Input::only([
                        'user_email',
                        'password',
                        'is_business'
                    ]);
            $newUser = AuthController::create($data);

            if ($newUser == 1) {

                return Redirect('register/buyer')->with('message', 'Registered successfully.');
            } elseif ($newUser == 0) {
                $otp = '';
                return Redirect('auth.register', array(
                            'otp' => $otp
                        ))->withInput();
            } else {

                return view('auth.register', array(
                    'otp' => $newUser
                        ));
            }
        }
        CommonComponent::activityLog("USER_REGISTERATION_DISPLAY", USER_REGISTERATION_DISPLAY, 0, HTTP_REFERRER, CURRENT_URL);

        $otp = '';
        return view('auth.register', array(
            'otp' => $otp
                ));
    }

    protected function checkUnique() {
        CommonComponent::activityLog("USER_UNIQUE", USER_UNIQUE, 0, HTTP_REFERRER, CURRENT_URL);

        if (isset($_POST ['optionValue']) && $_POST ['optionValue'] != '') {
            $optionValue = $_POST ['optionValue'];
        }

        if (isset($_POST ['user_email']) && $_POST ['user_email'] != '') {

            $userEmail = $_POST ['user_email'];

            if (strpos($userEmail, '@')) {
                $emailExist = User::where('email', $userEmail)->get();

                if (count($emailExist) > 0) {

                    echo "Email already exist";
                } else {
                    echo "";
                }
            }
            if ($optionValue == 1) {

                if (is_numeric($userEmail)) {

                    $phoneExist = User::where('phone', $userEmail)->get();

                    if (count($phoneExist) > 0) {

                        echo "Mobile number already exist";
                    } else {
                        echo "";
                    }
                }
            }
        }
    }

    /**
     * Validate the given OTP
     */
    public function validateotp() {
        CommonComponent::activityLog("VALIDATE_OTP", VALIDATE_OTP, 0, HTTP_REFERRER, CURRENT_URL);

        $inputOtp = '';
        if (!empty(Input::all())) {

            $data = Input::only('otp');
            $inputOtp = $data ['otp'];
        }
        $otpRecord = UserOtp::where('user_id', '=', $this->user_pk)->first();
        $savedOtp = $otpRecord->otp;

        if ($inputOtp == $savedOtp) {

            return Redirect('register/buyer')->with('message', 'Registered successfully.');
        } else {
            $deleteOtp = DB::table('user_otps')->where('user_id', $this->user_pk)->delete();

            $deleteUser = DB::table('users')->where('id', $this->user_pk)->delete();

            if ($deleteUser == 1 && $deleteOtp == 1) {
                Session::flush(); // unset $_SESSION variable for the run-time
                // destroy session data in storage
                return Redirect('/register')->with('message', 'Incorrect OTP');
            } else {
                Session::flush(); // unset $_SESSION variable for the run-time
                // destroy session data in storage
                return Redirect('/register')->with('message', 'Incorrect OTP');
            }
        }
    }

    /**
     * Display Individual Buyer Details form
     */
    public function buyer() {
        CommonComponent::activityLog("DISPALY_BUYER", DISPALY_BUYER, 0, HTTP_REFERRER, CURRENT_URL);

        $userRecord = User::where('id', '=', $this->user_pk)->first();
        $is_buyer = $userRecord ['is_business'];
        if ($is_buyer == 0) {
            $buyerRecord = BuyerDetail::where('user_id', '=', $this->user_pk)->first();

            if (empty($buyerRecord)) {

                return view('auth.buyer');
            } else {
                return Redirect('register/select_user');
            }
        } elseif ($is_buyer == 1) {

            return Redirect('register/buyer_business');
        }
    }

    /**
     * Buyer Registration
     *
     * @param $data array        	
     */
    public function createBuyer(array $data) {
        CommonComponent::activityLog("CREATE_BUYER", CREATE_BUYER, 0, HTTP_REFERRER, CURRENT_URL);

        $buyerDetails = new BuyerDetail ();

        $createdAt = date('Y-m-d H:i:s');
        $createdIp = $_SERVER ["REMOTE_ADDR"];

        $buyerDetails->user_id = Session::get('user_id');
        $buyerDetails->firstname = $data ['firstname'];
        $buyerDetails->lastname = $data ['lastname'];
        $buyerDetails->mobile = $data ['mobile'];
        $buyerDetails->landline = $data ['landline'];
        $buyerDetails->contact_email = $data ['contact_email'];
        $buyerDetails->address = $data ['address'];
        $buyerDetails->pincode = $data ['pincode'];
        $buyerDetails->created_at = $createdAt;
        $buyerDetails->created_ip = $createdIp;
        $buyerDetails->created_by = Session::get('user_id');

        if ($buyerDetails->save()) {
            CommonComponent::auditLog($buyerDetails->id, 'buyer_details');

            $username = $buyerDetails->firstname . " " . $buyerDetails->lastname;
            User::where("id", $this->user_pk)->update(array(
                'username' => $username
            ));
            CommonComponent::auditLog($this->user_pk, 'users');

            Session::put('firstname', $buyerDetails->firstname);
            Session::put('lastname', $buyerDetails->lastname);

            return '1';
        } else {
            return '0';
        }
    }

    public function registerBuyer() {
        CommonComponent::activityLog("BUYER_REGISTRATION", BUYER_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL);

        $data = Input::all();

        $newBuyer = AuthController::createBuyer($data);

        if ($newBuyer == 1) {

            return Redirect('register/select_user')->with('message', 'Buyer details submitted successfully.');
        } else {
            return Redirect('register/buyer');
        }
    }

    /**
     * Edit profile for individual buyer
     *
     * @param $id(User id)        	
     *
     */
    public function viewEditBuyer() {
        CommonComponent::activityLog("DISPLAY_BUYER", DISPLAY_BUYER, 0, HTTP_REFERRER, CURRENT_URL);

        $buyer_details = DB::table('buyer_details')->where('user_id', '=', $this->user_pk)->first();

        $buyer_id = $buyer_details->id;
        return view('auth.edit_buyer', compact('buyer_details'), array(
            'buyer_id' => $buyer_id
                ));
    }

    public function editBuyer($id, Request $request) {
        CommonComponent::activityLog("EDIT_BUYER", EDIT_BUYER, 0, HTTP_REFERRER, CURRENT_URL);

        if (!empty(Input::all())) {

            $updatedAt = date('Y-m-d H:i:s');
            $updatedIp = $_SERVER ['REMOTE_ADDR'];
            try {
                BuyerDetail::where("id", $id)->update(array(
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'mobile' => $request->mobile,
                    'landline' => $request->landline,
                    'contact_email' => $request->contact_email,
                    'address' => $request->address,
                    'pincode' => $request->pincode,
                    'updated_by' => $this->user_pk,
                    'updated_at' => $updatedAt,
                    'updated_ip' => $updatedIp
                ));
                //log
                CommonComponent::auditLog($id, 'buyer_details');
                $username = $buyerDetails->firstname . " " . $buyerDetails->lastname;

                User::where("id", $this->user_pk)->update(array(
                    'username' => $username,
                    'updated_date' => $updatedAt,
                    'updated_by' => $this->user_pk,
                    'updated_ip' => $updatedIp
                ));
                CommonComponent::auditLog($this->user_pk, 'users');
                return redirect('/home');
            } catch (Exception $ex) {
                
            }
        }
    }

    /**
     * Corporate(Business) buyer registration page
     */
    public function buyerBusiness() {
        CommonComponent::activityLog("DISPLAY_BUYER_BUSINESS", DISPLAY_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $userRecord = User::where('id', '=', $this->user_pk)->first();
        $is_buyer = $userRecord ['is_business'];

        if ($is_buyer == 0) {
            return view('auth.buyer');
        } elseif ($is_buyer == 1) {
            $businessBuyerRecord = BuyerBusinessDetail::where('user_id', '=', $this->user_pk)->first();
            if (empty($businessBuyerRecord)) {

                $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
                $country = \DB::table('lkp_countries')->orderBy('country_name', 'asc')->lists('country_name', 'id');
                $business = \DB::table('lkp_business_types')->orderBy('id', 'asc')->lists('business_type_name', 'id');

                return view('auth.buyer_business', array(
                    'state' => $state,
                    'country' => $country,
                    'business' => $business
                        ));
            } else {
                return view('auth.select_user');
            }
        }
    }

    /**
     * Corporate(Business) buyer registration
     */
    public function registerBusinessBuyer() {
        CommonComponent::activityLog("BUYER_BUSINESS_REGISTRATION", BUYER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL);

        $data = Input::all();
        $buyerBusinessDirectory = 'uploads/buyer/' . $this->user_pk . '/';
        if (is_dir($buyerBusinessDirectory)) {
            
        } else {
            mkdir($buyerBusinessDirectory, 0777, true);
        }
        if (isset($_FILES ['in_corporation_file']) && !empty($_FILES ['in_corporation_file'] ['name'])) {
            $file = 'in_corporation_file';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['in_corporation_file'] = $uploadedFile;
        } else {
            $data ['in_corporation_file'] = '';
        }
        if (isset($_FILES ['tin_filepath']) && !empty($_FILES ['tin_filepath'] ['name'])) {
            $file = 'tin_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['tin_filepath'] = $uploadedFile;
        } else {
            $data ['tin_filepath'] = '';
        }
        if (isset($_FILES ['gta_filepath']) && !empty($_FILES ['gta_filepath'] ['name'])) {
            $file = 'gta_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['gta_filepath'] = $uploadedFile;
        } else {
            $data ['gta_filepath'] = '';
        }
        if (isset($_FILES ['pancard_filepath']) && !empty($_FILES ['pancard_filepath'] ['name'])) {
            $file = 'pancard_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['pancard_filepath'] = $uploadedFile;
        } else {
            $data ['pancard_filepath'] = '';
        }
        if (isset($_FILES ['service_tax_filepath']) && !empty($_FILES ['service_tax_filepath'] ['name'])) {
            $file = 'service_tax_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['service_tax_filepath'] = $uploadedFile;
        } else {
            $data ['service_tax_filepath'] = '';
        }
        if (isset($_FILES ['central_excise_filepath']) && !empty($_FILES ['central_excise_filepath'] ['name'])) {
            $file = 'central_excise_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['central_excise_filepath'] = $uploadedFile;
        } else {
            $data ['central_excise_filepath'] = '';
        }

        if (isset($_FILES ['sales_tax_filepath']) && !empty($_FILES ['sales_tax_filepath'] ['name'])) {
            $file = 'sales_tax_filepath';

            $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
            $data ['sales_tax_filepath'] = $uploadedFile;
        } else {
            $data ['sales_tax_filepath'] = '';
        }

        $newBuyer = AuthController::createBusinessBuyer($data);

        if ($newBuyer == 1) {

            return Redirect('register/select_user')->with('message', 'Buyer business details are submitted successfully.');
        } else {
            return Redirect('register/buyer_business');
        }
    }

    /**
     * file upload function for
     * Corporate(Business)
     * buyer registration
     *
     * @param $directory(filepath) &
     *        	$file(filename)
     * @return filepath on success;
     *        
     */
    public function checkUpload($directory, $file) {
        CommonComponent::activityLog("CHECK_UPLOADS", CHECK_UPLOADS, 0, HTTP_REFERRER, CURRENT_URL);

        // for generating the random string to append with image name
        // Chars
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";

        // Parametres
        $string_length = 6;
        $random_string = '';

        // Gererating a random string of length of 10
        for ($i = 1; $i <= $string_length; $i ++) {
            $rand_number = rand(0, 59);
            $random_string .= $chars [$rand_number];
        }

        try {
            
            $fileName = $_FILES [$file] ['name'];
            $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (!is_array($fileName)) {

                $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                $uniqueFileName1 = $random_string . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;

                if (move_uploaded_file($_FILES [$file] ['tmp_name'], $directory . $uniqueFileName1)) {
                    $file_path = $directory . $uniqueFileName1;
                    return $file_path;
                }
            }
        } catch (Exception $ex) {
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
        CommonComponent::activityLog("CREATE_BUSINESS_BUYER", CREATE_BUSINESS_BUYER, 0, HTTP_REFERRER, CURRENT_URL);

        $buyerBusiness = new BuyerBusinessDetail ();

        $createdAt = date('Y-m-d H:i:s');
        $createdIp = $_SERVER ["REMOTE_ADDR"];

        $buyerBusiness->user_id = $this->user_pk;
        $buyerBusiness->name = $data ['name'];
        $buyerBusiness->lkp_business_type_id = $data ['lkp_business_type_id'];
        $buyerBusiness->lkp_country_id = $data ['lkp_country_id'];
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
        $buyerBusiness->contact_landline = $data ['contact_landline'];
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

        if ($buyerBusiness->save()) {
            CommonComponent::auditLog($this->user_pk, 'buyer_business_details');
            Session::put('company_name', $buyerBusiness->name);

            return '1';
        } else {
            return '0';
        }
    }

    /**
     * display selection screen
     */
    public function selectUser() {
        CommonComponent::activityLog("DISPLAY_SELECT_ROLE", DISPLAY_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL);

        $userRecord = User::where('id', '=', $this->user_pk)->first();
        $is_mailed = $userRecord ['mail_sent'];
        $is_business = $userRecord ['is_business'];

        if ($is_mailed == 1) {
            if ($is_business == 1) {
                return redirect('register/seller_business');
            } elseif ($is_mailed == 0) {
                return redirect('auth/login');
            }
        } elseif ($is_mailed == 0) {
            return view('auth.select_user');
        }
    }

    /**
     * Choosing role will redirect the users accordingly
     * & will save the appropriate details about users in user table
     */
    public function selectRole() {
        CommonComponent::activityLog("USER_SELECT_ROLE", USER_SELECT_ROLE, 0, HTTP_REFERRER, CURRENT_URL);

        if (isset($_POST ['user_role']) && $_POST ['user_role'] != '') {
            // for generating the random string
            // Chars
            $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";

            // Parametres
            $string_length = 10;
            $random_string = '';

            // Gererating a random string of length of 10
            for ($i = 1; $i <= $string_length; $i ++) {
                $rand_number = rand(0, 59);
                $random_string .= $chars [$rand_number];
            }

            // getting role_id posted by user
            $roleId = $_POST ['user_role'];

            $stored_uid = $this->user_pk;

            DB::table('users')->where('id', $this->user_pk)->update(array(
                'lkp_role_id' => $roleId,
                'mail_sent' => 1,
                'activation_key' => $random_string
            ));
            CommonComponent::auditLog($this->user_pk, 'users');
            $email_id = DB::table('users')->select('email');

            if ($roleId == 1) {
                $user = 'Buyer';
                $redirect = '/thankyou';
                $message = '';
                $message .= '<html><body>Hi</br>';
                $message .= '<p></p>';
                $message .= '<p>Please click the link below to activate your acount </p>';
                $message .= '<a>' . url() . '/user_activation?key=' . $random_string . '&u_id=' . $stored_uid . '&role_id=' . $roleId . '</a>';
                $message .= '<p>You will be able to login after completing the activation process</p>';
                $message .= '<p>Regards, <br/>Logistiks Team .</p>';
                $message .= '</body></html>';
                $header = '';
                $header .= "MIME-Version: 1.0 \r\n";
                $header .= "Content-type: text/html; charset=iso-8859-1 \r\n";
                $header .= "From: info@logistiks.com";
            } else {
                $user = 'Seller';
                $message = '';
                $redirect = 'seller_business';
            }

            $msg_array = array(
                'message' => $message,
                'redirect' => $redirect
            );
            
            echo json_encode($msg_array);
            die();

            
        }
    }

    /**
     * Selects state list when user selects country
     */
    public function getState() {
        CommonComponent::activityLog("GET_STATE", GET_STATE, 0, HTTP_REFERRER, CURRENT_URL);

        if (isset($_POST ["country_id"]) && $_POST ["country_id"] != '') {
            $stateList = \DB::table('lkp_states')->where('lkp_country_id', $_POST ["country_id"])->orderBy('state_name', 'asc')->lists('state_name', 'id');

            $str = '<option value = "">Select State</option>';
            foreach ($stateList as $k => $v) {

                $str .= '<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }

    /**
     * Selects locality list when user selects city
     */
    public function getIntraLocality() {
        CommonComponent::activityLog("GET_INTRACITY_LOCALITY", GET_INTRACITY_LOCALITY, 0, HTTP_REFERRER, CURRENT_URL);

        if (isset($_POST ["cities"]) && $_POST ["cities"] != '') {
            $cityId = array();
            $cities = $_POST ["cities"];
            $cityId = explode(",", $cities);

            $stateList = DB::table('lkp_localities')->whereIn('lkp_city_id', $cityId)->lists('locality_name', 'id');
            
            $str = '<option value = "">Select City</option>';
            foreach ($stateList as $k => $v) {

                $str .= '<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }

    /**
     * Selects locality list when user selects city
     */
    public function getPaMCity() {
        CommonComponent::activityLog("GET_PM_CITY", GET_PM_CITY, 0, HTTP_REFERRER, CURRENT_URL);

        if (isset($_POST ["stateList"]) && $_POST ["stateList"] != '') {
            $stateId = array();
            $states = $_POST ["stateList"];
            $stateId = explode(",", $states); // making string as array

            $cityList = DB::table('lkp_cities')->whereIn('lkp_state_id', $stateId)->lists('city_name', 'id');
           
            $str = '<option value = "">Select City</option>';
            foreach ($cityList as $k => $v) {

                $str .= '<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }

    /**
     * Render the seller business page with database drop downs
     */
    public function sellerBusiness() {
        CommonComponent::activityLog("DISPALY_SELLER_BUSINESS", DISPALY_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $userRecord = User::where('id', '=', $this->user_pk)->first();
        $role_id = $userRecord ['lkp_role_id'];

        if ($role_id == 1) {
            return view('auth.buyer');
        } elseif ($role_id == 2) {
            $sellerRecord = Seller::where('user_id', '=', $this->user_pk)->first();
            if (empty($sellerRecord)) {

                $stateList = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
                $country = \DB::table('lkp_countries')->orderBy('country_name', 'asc')->lists('country_name', 'id');
                $business = \DB::table('lkp_business_types')->orderBy('id', 'asc')->lists('business_type_name', 'id');
                $services = \DB::table('lkp_services')->where('id', '<', '10')->lists('service_name', 'id');
                $packaging = \DB::table('lkp_services')->where('id', '>', '9')->lists('service_name', 'id');
                $locality = \DB::table('lkp_localities')->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
                $cities = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

                return view('auth.seller_business', array(
                    'stateList' => $stateList,
                    'country' => $country,
                    'business' => $business,
                    'services' => $services,
                    'packaging' => $packaging,
                    'cities' => $cities,
                    'locality' => $locality
                        ));
            } else {
                return view('thankyou');
            }
        }
    }

    public function registerSellerBusiness() {
        CommonComponent::activityLog("SELLER_BUSINESS_REGISTRATION", SELLER_BUSINESS_REGISTRATION, 0, HTTP_REFERRER, CURRENT_URL);

        if (!empty(Input::all())) {
            $data = Input::all();

            $sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';

            if (is_dir($sellerBusinessDirectory)) {
                
            } else {
                mkdir($sellerBusinessDirectory, 0777, true);
            }

            if (isset($_FILES ['in_corporation_file']) && !empty($_FILES ['in_corporation_file'] ['name'])) {
                $file = 'in_corporation_file';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['in_corporation_file'] = $uploadedFile;
            } else {
                $data ['in_corporation_file'] = '';
            }
            if (isset($_FILES ['tin_filepath']) && !empty($_FILES ['tin_filepath'] ['name'])) {
                $file = 'tin_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['tin_filepath'] = $uploadedFile;
            } else {
                $data ['tin_filepath'] = '';
            }
            if (isset($_FILES ['gta_filepath']) && !empty($_FILES ['gta_filepath'] ['name'])) {
                $file = 'gta_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['gta_filepath'] = $uploadedFile;
            } else {
                $data ['gta_filepath'] = '';
            }
            if (isset($_FILES ['pancard_filepath']) && !empty($_FILES ['pancard_filepath'] ['name'])) {
                $file = 'pancard_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['pancard_filepath'] = $uploadedFile;
            } else {
                $data ['pancard_filepath'] = '';
            }
            if (isset($_FILES ['service_tax_filepath']) && !empty($_FILES ['service_tax_filepath'] ['name'])) {
                $file = 'service_tax_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['service_tax_filepath'] = $uploadedFile;
            } else {
                $data ['service_tax_filepath'] = '';
            }
            if (isset($_FILES ['central_excise_filepath']) && !empty($_FILES ['central_excise_filepath'] ['name'])) {
                $file = 'central_excise_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['central_excise_filepath'] = $uploadedFile;
            } else {
                $data ['central_excise_filepath'] = '';
            }

            if (isset($_FILES ['sales_tax_filepath']) && !empty($_FILES ['sales_tax_filepath'] ['name'])) {
                $file = 'sales_tax_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['sales_tax_filepath'] = $uploadedFile;
            } else {
                $data ['sales_tax_filepath'] = '';
            }


            $newBuyer = AuthController::createSellerBusiness($data);

            if ($newBuyer == 1) {

                return Redirect('thankyou_seller')->with('message', 'Seller business details are submitted successfully.');
            } else {
                return Redirect('register/seller_business');
            }
        }
    }

    /**
     *
     * Saving Seller business details in database
     *
     * @param $data
     *        	
     *        	
     */
    public function createSellerBusiness(array $data) {
        CommonComponent::activityLog("CREATE_SELLER_BUSINESS", CREATE_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $sellerBusiness = new Seller ();
        $services = array();
        $createdAt = date('Y-m-d H:i:s');
        $createdIp = $_SERVER ["REMOTE_ADDR"];

        $sellerBusiness->user_id = $this->user_pk;
        $sellerBusiness->name = $data ['name'];
        $sellerBusiness->lkp_business_type_id = $data ['lkp_business_type_id'];
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
        // echo "<pre>";print_r($data);die();
        if ($sellerBusiness->save()) {
            CommonComponent::auditLog($sellerBusiness->id, 'seller_details');
            Session::put('company_name', $sellerBusiness->name); // session for future use
            $services = $data ['services'];
            $packaging = $data ['packaging'];
            $intracityArea = $data ['intracity_locality'];
            $pamArea = $data ['pm_city'];

            // see if value has been posted
            if (!empty($data ['services']) && !empty($data ['packaging'])) {
                $seller_services = array_merge($services, $packaging);
            } elseif (empty($data ['services'])) {

                $seller_services = $packaging;
            } elseif (empty($data ['packaging'])) {

                $seller_services = $services;
            }

            foreach ($seller_services as $service) {

                $seller_services_save = new SellerService ();
                $seller_services_save->user_id = $this->user_pk;
                $seller_services_save->lkp_service_id = $service;
                $seller_services_save->created_by = $this->user_pk;
                $seller_services_save->created_at = $createdAt;
                $seller_services_save->created_ip = $createdIp;
                $seller_services_save->save();
                CommonComponent::auditLog($seller_services_save->id, 'seller_services');
            }

            foreach ($intracityArea as $intracity) {

                $intracityArea_save = new SellerIntracityLocality ();
                $intracityArea_save->user_id = $this->user_pk;
                $intracityArea_save->lkp_locality_id = $intracity;
                $intracityArea_save->created_by = $this->user_pk;
                $intracityArea_save->created_at = $createdAt;
                $intracityArea_save->created_ip = $createdIp;

                $intracityArea_save->save();
                CommonComponent::auditLog($intracityArea_save->id, 'seller_intracity_localities');
            }
            foreach ($pamArea as $pam) {

                $pam_save = new SellerPmCity ();
                $pam_save->user_id = $this->user_pk;
                $pam_save->lkp_city_id = $pam;
                $pam_save->created_by = $this->user_pk;
                $pam_save->created_at = $createdAt;
                $pam_save->created_ip = $createdIp;

                $pam_save->save();
                CommonComponent::auditLog($pam_save->id, 'seller_pm_cities');
            }
            return '1';
        } else {
            return '0';
        }
    }

    public function viewEditBuyerBusiness() {
        CommonComponent::activityLog("DIAPLAY_EDIT_BUYER_BUSINESS", DIAPLAY_EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
        $country = \DB::table('lkp_countries')->orderBy('country_name', 'asc')->lists('country_name', 'id');
        $business = \DB::table('lkp_business_types')->orderBy('id', 'asc')->lists('business_type_name', 'id');

        $buyer_business = DB::table('buyer_business_details')->where('user_id', '=', $this->user_pk)->first();
       
        $in_corporation_file = explode("/", $buyer_business->in_corporation_file);
        $buyer_business->in_corporation_file = end($in_corporation_file);

        $tin = explode("/", $buyer_business->tin_filepath);
        $buyer_business->tin_filepath = end($tin);

        $gta = explode("/", $buyer_business->gta_filepath);
        $buyer_business->gta_filepath = end($gta);

        $pancard = explode("/", $buyer_business->pancard_filepath);
        $buyer_business->pancard_filepath = end($pancard);

        $service_tax = explode("/", $buyer_business->service_tax_filepath);
        $buyer_business->service_tax_filepath = end($service_tax);

        $central_excise = explode("/", $buyer_business->central_excise_filepath);
        $buyer_business->central_excise_filepath = end($central_excise);

        $sales_tax = explode("/", $buyer_business->sales_tax_filepath);
        $buyer_business->sales_tax_filepath = end($sales_tax);

        $buyer_id = $buyer_business->id;

        return view('auth.edit_buyer_business', compact('buyer_business'), array(
            'state' => $state,
            'country' => $country,
            'business' => $business,
            'buyer_id' => $buyer_id
                ));
    }

    public function editBuyerBusiness($id) {
        CommonComponent::activityLog("EDIT_BUYER_BUSINESS", EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        if (!empty(Input::all())) {

            $data = Input::all();
            $buyerBusinessDirectory = 'uploads/buyer/' . $this->user_pk . '/';
            if (is_dir($buyerBusinessDirectory)) {
                
            } else {
                mkdir($buyerBusinessDirectory, 0777, true);
            }
            if (isset($_FILES ['in_corporation_file']) && !empty($_FILES ['in_corporation_file'] ['name'])) {
                $file = 'in_corporation_file';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['in_corporation_file'] = $uploadedFile;
            } else {
                $data ['in_corporation_file'] = '';
            }
            if (isset($_FILES ['tin_filepath']) && !empty($_FILES ['tin_filepath'] ['name'])) {
                $file = 'tin_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['tin_filepath'] = $uploadedFile;
            } else {
                $data ['tin_filepath'] = '';
            }
            if (isset($_FILES ['gta_filepath']) && !empty($_FILES ['gta_filepath'] ['name'])) {
                $file = 'gta_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['gta_filepath'] = $uploadedFile;
            } else {
                $data ['gta_filepath'] = '';
            }
            if (isset($_FILES ['pancard_filepath']) && !empty($_FILES ['pancard_filepath'] ['name'])) {
                $file = 'pancard_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['pancard_filepath'] = $uploadedFile;
            } else {
                $data ['pancard_filepath'] = '';
            }
            if (isset($_FILES ['service_tax_filepath']) && !empty($_FILES ['service_tax_filepath'] ['name'])) {
                $file = 'service_tax_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['service_tax_filepath'] = $uploadedFile;
            } else {
                $data ['service_tax_filepath'] = '';
            }
            if (isset($_FILES ['central_excise_filepath']) && !empty($_FILES ['central_excise_filepath'] ['name'])) {
                $file = 'central_excise_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['central_excise_filepath'] = $uploadedFile;
            } else {
                $data ['central_excise_filepath'] = '';
            }

            if (isset($_FILES ['sales_tax_filepath']) && !empty($_FILES ['sales_tax_filepath'] ['name'])) {
                $file = 'sales_tax_filepath';

                $uploadedFile = AuthController::checkUpload($buyerBusinessDirectory, $file);
                $data ['sales_tax_filepath'] = $uploadedFile;
            } else {
                $data ['sales_tax_filepath'] = '';
            }

            $newBuyer = AuthController::createEditBusinessBuyer($id, $data);

            if ($newBuyer == 1) {

                return Redirect('/home')->with('message', 'Buyer business details are submitted successfully.');
            } else {
                return Redirect('register/edit/buyer_business');
            }
        }
    }

    /**
     * updating buyer business details
     *
     * @param $id, $data        	
     */
    public function createEditBusinessBuyer($id, array $data) {
        CommonComponent::activityLog("CREATE_EDIT_BUYER_BUSINESS", CREATE_EDIT_BUYER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $updatedAt = date('Y-m-d H:i:s');
        $updatedIp = $_SERVER ['REMOTE_ADDR'];

        try {
            BuyerBusinessDetail::where("id", $id)->update(array(
                'user_id' => $this->user_pk,
                'name' => $data ['name'],
                'lkp_business_type_id' => $data ['lkp_business_type_id'],
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
            ));
            CommonComponent::auditLog($id, 'buyer_business_details');
            $username = $buyerDetails->firstname . " " . $buyerDetails->lastname;
            Users::where("id", $this->user_pk)->update(array(
                'username' => $username,
                'updated_date' => $updatedAt,
                'updated_by' => $this->user_pk,
                'updated_ip' => $updatedIp
            ));
            CommonComponent::auditLog($this->user_pk, 'users');

            return '1';
        } catch (Exception $ex) {
            return '0';
        }
    }

    /**
     * Dispalys edit seller(Business) page
     */
    public function viewEditSellerBusiness() {
        CommonComponent::activityLog("DISPLAY_EDIT_SELLER_BUSINESS", DISPLAY_EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        $stateList = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
        $country = \DB::table('lkp_countries')->orderBy('country_name', 'asc')->lists('country_name', 'id');
        $business = \DB::table('lkp_business_types')->orderBy('id', 'asc')->lists('business_type_name', 'id');
        $services = \DB::table('lkp_services')->where('id', '<', '10')->lists('service_name', 'id');
        $packaging = \DB::table('lkp_services')->where('id', '>', '9')->lists('service_name', 'id');
        $locality = \DB::table('lkp_localities')->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
        $cities = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

        $seller_business = DB::table('seller_details')->where('user_id', '=', $this->user_pk)->first();

        $seller_business = DB::table('users')->leftJoin('seller_details', 'users.id', '=', 'sellers.user_id')->where('users.id', '=', $this->user_pk)->first();

        $intracity = DB::table('users')->select('lkp_locality_id as locality_id', 'lc.id as city_id')->leftJoin('seller_intracity_localities as sip', 'users.id', '=', 'sip.user_id')->leftJoin('lkp_localities as ll', 'sip.lkp_locality_id', '=', 'll.id')->leftJoin('lkp_cities as lc', 'll.lkp_city_id', '=', 'lc.id')->where('users.id', '=', $this->user_pk)->get();

        $packersMovers = DB::table('users')->select('lkp_city_id as city_id', 'ls.id as state_id')->leftJoin('seller_pm_cities as spc', 'users.id', '=', 'spc.user_id')->leftJoin('lkp_cities as lc', 'spc.lkp_city_id', '=', 'lc.id')->leftJoin('lkp_states as ls', 'lc.lkp_state_id', '=', 'ls.id')->where('users.id', '=', $this->user_pk)->get();

        $pmCity_array = array();
        $pmState_array = array();
        $intra_locality = array();
        $intra_city = array();
        foreach ($packersMovers as $pm) {

            array_push($pmCity_array, $pm->city_id);
            array_push($pmState_array, $pm->state_id);
        }
        foreach ($intracity as $pm) {

            array_push($intra_locality, $pm->locality_id);
            array_push($intra_city, $pm->city_id);
        }

        $transport = \DB::table('seller_services as ss')->select('ss.lkp_service_id as service_id')->leftJoin('users', 'users.id', '=', 'ss.user_id')->where('ss.lkp_service_id', '<', '10')->where('ss.user_id', '=', $this->user_pk)->get();

        $handling = \DB::table('seller_services as ss')->select('ss.lkp_service_id as service_id')->leftJoin('users', 'users.id', '=', 'ss.user_id')->where('ss.lkp_service_id', '>', '10')->where('ss.user_id', '=', $this->user_pk)->get();

        $transport_array = array();
        foreach ($transport as $trans) {

            array_push($transport_array, $trans->service_id);
        }

        $handling_array = array();
        foreach ($handling as $hand) {

            array_push($handling_array, $hand->service_id);
        }

        $in_corporation_file = explode("/", $seller_business->in_corporation_file);
        $seller_business->in_corporation_file = end($in_corporation_file);

        $tin = explode("/", $seller_business->tin_filepath);
        $seller_business->tin_filepath = end($tin);

        $gta = explode("/", $seller_business->gta_filepath);
        $seller_business->gta_filepath = end($gta);

        $pancard = explode("/", $seller_business->pancard_filepath);
        $seller_business->pancard_filepath = end($pancard);

        $service_tax = explode("/", $seller_business->service_tax_filepath);
        $seller_business->service_tax_filepath = end($service_tax);

        $central_excise = explode("/", $seller_business->central_excise_filepath);
        $seller_business->central_excise_filepath = end($central_excise);

        $sales_tax = explode("/", $seller_business->sales_tax_filepath);
        $seller_business->sales_tax_filepath = end($sales_tax);

        $seller_id = $seller_business->id;

        return view('auth.edit_seller_business', compact('seller_business'), array(
            'stateList' => $stateList,
            'country' => $country,
            'business' => $business,
            'services' => $services,
            'packaging' => $packaging,
            'cities' => $cities,
            'seller_id' => $seller_id,
            'locality' => $locality,
            'pmCity' => $pmCity_array,
            'pmState' => $pmState_array,
            'intra_locality' => $intra_locality,
            'intra_city' => $intra_city,
            'handling' => $handling_array,
            'transport' => $transport_array
                ));
    }

    /**
     */
    public function editSellerBusiness($id) {
        CommonComponent::activityLog("EDIT_SELLER_BUSINESS", EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);

        if (!empty(Input::all())) {
            $data = Input::all();
            echo "<pre>";
            print_r($data);
            die();
            $sellerBusinessDirectory = 'uploads/seller/' . $this->user_pk . '/';

            if (is_dir($sellerBusinessDirectory)) {
                
            } else {
                mkdir($sellerBusinessDirectory, 0777, true);
            }

            if (isset($_FILES ['in_corporation_file']) && !empty($_FILES ['in_corporation_file'] ['name'])) {
                $file = 'in_corporation_file';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['in_corporation_file'] = $uploadedFile;
            } else {
                $data ['in_corporation_file'] = '';
            }
            if (isset($_FILES ['tin_filepath']) && !empty($_FILES ['tin_filepath'] ['name'])) {
                $file = 'tin_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['tin_filepath'] = $uploadedFile;
            } else {
                $data ['tin_filepath'] = '';
            }
            if (isset($_FILES ['gta_filepath']) && !empty($_FILES ['gta_filepath'] ['name'])) {
                $file = 'gta_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['gta_filepath'] = $uploadedFile;
            } else {
                $data ['gta_filepath'] = '';
            }
            if (isset($_FILES ['pancard_filepath']) && !empty($_FILES ['pancard_filepath'] ['name'])) {
                $file = 'pancard_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['pancard_filepath'] = $uploadedFile;
            } else {
                $data ['pancard_filepath'] = '';
            }
            if (isset($_FILES ['service_tax_filepath']) && !empty($_FILES ['service_tax_filepath'] ['name'])) {
                $file = 'service_tax_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['service_tax_filepath'] = $uploadedFile;
            } else {
                $data ['service_tax_filepath'] = '';
            }
            if (isset($_FILES ['central_excise_filepath']) && !empty($_FILES ['central_excise_filepath'] ['name'])) {
                $file = 'central_excise_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['central_excise_filepath'] = $uploadedFile;
            } else {
                $data ['central_excise_filepath'] = '';
            }

            if (isset($_FILES ['sales_tax_filepath']) && !empty($_FILES ['sales_tax_filepath'] ['name'])) {
                $file = 'sales_tax_filepath';

                $uploadedFile = AuthController::checkUpload($sellerBusinessDirectory, $file);
                $data ['sales_tax_filepath'] = $uploadedFile;
            } else {
                $data ['sales_tax_filepath'] = '';
            }

            $newBuyer = AuthController::createEditSellerBusiness($id, $data);

            if ($newBuyer == 1) {

                return Redirect('/home')->with('message', 'Seller business details are submitted successfully.');
            } else {
                return Redirect('register/edit/seller_business');
            }
        }
    }

    public function createEditSellerBusiness($id, $data) {
        CommonComponent::activityLog("CREATE_EDIT_SELLER_BUSINESS", CREATE_EDIT_SELLER_BUSINESS, 0, HTTP_REFERRER, CURRENT_URL);
        ;
        $updatedAt = date('Y-m-d H:i:s');
        $updatedIp = $_SERVER ['REMOTE_ADDR'];

        try {
            Seller::where("id", $id)->update(array(
                'user_id' => $this->user_pk,
                'name' => $data ['name'],
                'lkp_business_type_id' => $data ['lkp_business_type_id'],
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
            ));
            CommonComponent::auditLog($id, 'seller_details');
            Session::put('company_name', $data ['name']); // session for future use
            $services = $data ['services'];
            $packaging = $data ['packaging'];
            $intracityArea = $data ['intracity_locality'];
            $pamArea = $data ['pm_city'];

            // see if value has been posted
            if (!empty($data ['services']) && !empty($data ['packaging'])) {
                $seller_services = array_merge($services, $packaging);
            } elseif (empty($data ['services'])) {

                $seller_services = $packaging;
            } elseif (empty($data ['packaging'])) {

                $seller_services = $services;
            }

            $servicesDeleted = DB::table('seller_services')->where('user_id', $this->user_pk)->delete();

            if ($servicesDeleted > 1) {
                // for deleting / inserting pm & intracity areas
                if (in_array(3, $seller_services)) {
                    $isSelect_intracity = 1;
                }
                if (in_array(15, $seller_services)) {
                    $isSelect_pm = 1;
                }

                foreach ($seller_services as $service) {

                    $seller_services_save = new SellerService ();
                    $seller_services_save->user_id = $this->user_pk;
                    $seller_services_save->lkp_service_id = $service;
                    $seller_services_save->created_by = $this->user_pk;
                    $seller_services_save->created_at = $updatedAt;
                    $seller_services_save->created_ip = $updatedIp;
                    $seller_services_save->save();
                    CommonComponent::auditLog($seller_services_save->id, 'seller_services');
                }
            }

            if (!empty($intracityArea)) {
                $intracityDeleted = DB::table('seller_intracity_localities')->where('user_id', $this->user_pk)->delete();
                if ($intracityDeleted > 1) {

                    if ($isSelect_intracity == 1) {
                        foreach ($intracityArea as $intracity) {

                            $intracityArea_save = new SellerIntracityLocality ();
                            $intracityArea_save->user_id = $this->user_pk;
                            $intracityArea_save->lkp_locality_id = $intracity;
                            $intracityArea_save->created_by = $this->user_pk;
                            $intracityArea_save->created_at = $updatedAt;
                            $intracityArea_save->created_ip = $updatedIp;
                            $intracityArea_save->save();
                            CommonComponent::auditLog($intracityArea_save->id, 'seller_intracity_localities');
                        }
                    }
                }
            }

            if (!empty($pamArea)) {
                $pmDeleted = DB::table('seller_pm_cities')->where('user_id', $this->user_pk)->delete();

                if ($pmDeleted > 1) {
                    if ($isSelect_pm == 1) {

                        foreach ($pamArea as $pam) {

                            $pam_save = new SellerPmCity ();
                            $pam_save->user_id = $this->user_pk;
                            $pam_save->lkp_city_id = $pam;
                            $pam_save->created_by = $this->user_pk;
                            $pam_save->created_at = $updatedAt;
                            $pam_save->created_ip = $updatedIp;
                            $pam_save->save();
                            CommonComponent::auditLog($pam_save->id, 'seller_pm_cities');
                        }
                    }
                }
            }
            return '1';
        } catch (Exception $ex) {
            return '0';
        }
    }

    public function facebook_redirect() {
        
        if (isset($_GET['key']))
            return Socialize::with('facebook')->redirect($_GET['key']);
        else
            return Socialize::with('facebook')->redirect();
    }

    // to get authenticate user data
    public function facebook() {
        
        $user = Socialize::with('facebook')->user();
        $emailExist = User::where('email', $user->email)->get();
        if (count($emailExist) > 0) {
            if (strstr(CURRENT_URL, "login")) {
                
                if ($emailExist[0]->is_facebook == 1 && $emailExist[0]->fb_identifier != "") {
                    
                    if (Auth::loginUsingId($emailExist[0]->id)) {
                        return redirect('/home');
                    }
                } else {
                    return Redirect('/auth/login')->with('message', 'please login as normal user.');
                }
            } else
                return Redirect('/')->with('message', 'Email already exist.');
        } else
            return view('auth.select', array(
                'email' => $user->email,
                'identifier' => $user->id,
                'provider' => 'facebook',
                'username' => $user->name,
            ));

    }

    public function linkedin_redirect() {
        return Socialize::with('linkedin')->redirect();
    }

    // to get authenticate user data
    public function linkedin() {
        $user = Socialize::with('linkedin')->user();

        print_r($user);
        die;
    }

    public function google_redirect() {
        return Socialize::with('google')->redirect();
    }

    // to get authenticate user data
    public function google() {
        $user = Socialize::with('google')->user();

        print_r($user);
        die;
    }

    public function socialRegister() {
        CommonComponent::activityLog("USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL);
        if (isset($_POST ['submitRegister'])) {
            CommonComponent::activityLog("USER_REGISTER", USER_REGISTER, 0, HTTP_REFERRER, CURRENT_URL);
            
            $data = Input::only([
                        'user_email',
                        'password',
                        'is_business',
                        'username',
                        'identifier',
                        'provider',
            ]);

            $newUser = AuthController::create($data);

            if ($newUser == 1) {

                return Redirect('register/buyer')->with('message', 'Registered successfully.');
            } elseif ($newUser == 0) {
                $otp = '';
                return Redirect('auth.register', array(
                            'otp' => $otp
                        ))->withInput();
            } else {

                return view('auth.register', array(
                    'otp' => $newUser
                ));
            }
        }
    }

}
