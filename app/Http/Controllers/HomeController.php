<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use App\Components\MessagesComponent;
use App\Components\ResizeImage;
use App\Models\User;

use DB;
use Input;
use Config;
use File;
use Session;
use Illuminate\Http\Request;
use Redirect;
/* use Zofe\Rapyd\Facades\DataGrid; */
class HomeController extends Controller {
	
	/*
	 * |--------------------------------------------------------------------------
	 * | Home Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller renders your application's "dashboard" for users that
	 * | are authenticated. Of course, you are free to change or remove the
	 * | controller as you wish. It is just here to get your app started!
	 * |
	 */
	
	/**
	 * Create a new controller instance.
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
	 * application home page.
	 * Displays some user specific screen
	 *
	 * @return Response
	 */
	public function index($service = null) {
		
        if (isset ( $service ) && $service != '' && $service != '0'){
			if($service == 'ftl'){

				Session::put('service_id',ROAD_FTL);
			}else if($service == 'ptl'){
				Session::put('service_id',ROAD_PTL);
			}else if($service == 'intracity'){
				Session::put('service_id',ROAD_INTRACITY);
			}else if($service == 'thaul'){
				Session::put('service_id',ROAD_TRUCK_HAUL);
			}else if($service == 'tlease'){
				Session::put('service_id',ROAD_TRUCK_LEASE);
			}else if($service == 'airinter'){
				Session::put('service_id',AIR_INTERNATIONAL);
			}else if($service == 'ocean'){
				Session::put('service_id',OCEAN);
			}else if($service == 'multimodel'){
				Session::put('service_id',MULTIMODEL);
			}else if($service == 'handling'){
				Session::put('service_id',HANDLING_SERVICES);
			}else if($service == 'equipment'){
				Session::put('service_id',EQUIPMENT_LEASE);
			}else if($service == 'packaging'){
				Session::put('service_id',PACKAGING_SERVICES);
			}else if($service == 'warehouse'){
				Session::put('service_id',WAREHOUSE);
			}else if($service == 'thirdparty'){
				Session::put('service_id',THIRD_PARTY_LOGISTICS);
			}else if($service == 'packers'){
				Session::put('service_id',PACKERS_MOVERS);
			}else{
				if (Session::get('service_id') == '' || Session::get('service_id') == '0'){
					Session::put('service_id','0');
				}
			}			
		}
		if (isset ( Auth::User ()->id )) {
			$userId = Auth::User ()->id;
			$roleId = Auth::User ()->lkp_role_id;
			// Check the user is indiviudual or business and based on their roles ,redirect to respective pages
			if (Auth::User ()->is_business == IS_BUSINESS) {
				if ($roleId == BUYER) {
					$buyerBusinessDetails = DB::table ( 'buyer_business_details' )->where ( 'buyer_business_details.user_id', $userId )->select ( 'buyer_business_details.id' )->get ();
					if (count ( $buyerBusinessDetails ) == 0) {
						return Redirect ( 'register/buyer_business' )->with ( 'message', 'Please fill the below details...' );
					}
				} elseif ($roleId == SELLER) {
					$sellerDetails = DB::table ( 'sellers' )->where ( 'sellers.user_id', $userId )->select ( 'sellers.id' )->get ();
					if (count ( $sellerDetails ) == 0) {
						return Redirect ( 'register/seller_business' )->with ( 'message', 'Please fill the below details...' );
					}
					else{
						if(Auth::User ()->is_active == 0){
							return Redirect ( 'thankyou_seller' )->with ( 'message', 'Please finish the payment procedure' );
						}
					}
				}
			} elseif (Auth::User ()->is_business == IS_INDIVIDUAL) {
				if ($roleId == BUYER) {
					$buyerDetails = DB::table ( 'buyer_details' )->where ( 'buyer_details.user_id', $userId )->select ( 'buyer_details.id' )->get ();
					
					if (count ( $buyerDetails ) == 0) {
						return Redirect ( 'register/buyer' )->with ( 'message', 'Please fill the below details...' );
					}
				} elseif ($roleId == SELLER) {
					$sellerDetails = DB::table ( 'seller_details' )->where ( 'seller_details.user_id', $userId )->select ( 'seller_details.id' )->get ();
					if (count ( $sellerDetails ) == 0) {
						return Redirect ( 'register/seller' )->with ( 'message', 'Please fill the below details...' );
					}
					else{
						if(Auth::User ()->is_active == 0){
							return Redirect ( 'thankyou_seller' )->with ( 'message', 'Please finish the payment procedure' );
						}
					}
				}
			}
			
			if ($roleId == 2) {
				$lastLogin = Auth::User ()->last_login;
				if ($lastLogin == '') {
					$firstPopup = '1';
				} else {
					$firstPopup = '';
				}
			} else {
				$firstPopup = '';
			}
			//echo date ( "Y-m-d H:i:s" )."===========ghfg========".$this->user_pk;exit;
			User::where ( "id", $this->user_pk )->update ( array (
					'last_login' => date ( "Y-m-d H:i:s" ),
					'last_ip' => $_SERVER ["REMOTE_ADDR"] 
			) );
			Session::put('message','');
            //$allMessages = MessagesComponent::listMessages($service, Auth::User()->id);
            if(isset($_POST)){
            	$allMessages = MessagesComponent::listMessages($_POST);
            }else{
            	$allMessages = MessagesComponent::listMessages();
            }            
            $allMessageTypes = MessagesComponent::getMessageTypes();
            $allServices = CommonComponent::getAllServices();
			return view ( 'home.index', array (
					'activePopup' => $firstPopup,
					'userId' => $userId,
                    'allMessages' => $allMessages,
                    'allMessageTypes' => $allMessageTypes,
                    'allServices' => $allServices
                
			) );
		} else {
			
			return Redirect ( '/register' );
		}
	}
	
	/**
	 * Upload logo function
	 */
	public function uploadLogo() {
		$imageName = $_FILES ['file'] ['name'];
		// don't forget last slash in path
		$filePath = '/uploads/seller/' . $this->user_pk . '/logo/';
		// image name
		$path = $imageName;
		
		// get the extension for the uploaded file and store it in a variable
		$ext = pathinfo ( $path, PATHINFO_EXTENSION );
		
		$supported_image = array (
				'gif',
				'jpg',
				'jpeg',
				'png' 
		);
		
		if (! in_array ( $ext, $supported_image )) {
			
			echo "Image formats: gif, png, jpeg,png are allowed only";
		} else {
			
			// random key generation
			$length = rand ( 5, 10 );
			$chars = array_merge ( range ( 'a', 'z' ) );
			shuffle ( $chars );
			$randomString = implode ( array_slice ( $chars, 0, $length ) );
			
			$modified_image_name = $this->user_pk . '_logo_' . $randomString;
			
			if (! is_dir ( getcwd () . $filePath )) {
				
				mkdir ( getcwd () . $filePath, 0777, true );
			}
			
			// modifying the image name and uploading it to the server image directory
			$fileName = $modified_image_name . '.' . $ext; // renaming uploaded file
			$dbFileName = 'uploads/seller/' . $this->user_pk . '/logo/'.$this->user_pk.'_logo_' . $randomString;
			$dbSavedFile = $dbFileName . '.' . $ext;
			// move the uploaded file into required directory
			move_uploaded_file ( $_FILES ["file"] ["tmp_name"], getcwd () . $filePath . $fileName );
			
			// calling the resize function to get the desired resolution for the uploaded image
			$resize = new ResizeImage ( getcwd () . $filePath . $fileName . '' );
			
			// defining the height and width for the image file
			
			$resize->resizeTo ( 200, 100, 'exact' );
			
			// after resize again saving the image into the directory
			$resize->saveImage ( getcwd () . $filePath . $fileName . '' );
			
			// changing name so that folder and dB both have same imagename
			// updating the random string to the database for the given email id
			User::where ( "id", $this->user_pk )->update ( array (
					'logo' => $dbSavedFile 
			) );
			echo "Logo uploaded successfully";
		}
	}
	
	/**
	 * ASK ME LATER FUNCTIONALITY FOR SELLER Ist TIME POPUP
	 */
	public function askmeLater() {
		if (isset ( $_POST ['time'] ) && $_POST ['time'] != '') {
			
			if(User::where ( "id", $this->user_pk )->update ( array ('remind_me' => date ( 'Y-m-d', strtotime ( "+30 days" ) )))){
				return '1';
			}else{return '0';}
		}
	}
	
	/**
	 * Redirect User to Edit Profile
	 */
	public function myProfile() {
		$user_type = Auth::User ()->is_business;
		$roleId = Auth::User ()->lkp_role_id;
		$secondaryRole = Session::get('last_login_role_id');
		
		if($secondaryRole == 0){
			$secondaryRole = $roleId;
		}
		else{
			$secondaryRole = Session::get('last_login_role_id');
		}
		if ($user_type == IS_INDIVIDUAL) {
			
			if ($secondaryRole == BUYER) {
				return Redirect ( 'edit/buyer' );
			} elseif ($secondaryRole == SELLER) {
				return Redirect ( 'register/edit_seller' );
			}
		} 

		elseif ($user_type == IS_BUSINESS) {
			
			if ($secondaryRole == BUYER) {
				return Redirect ( 'register/edit/buyer_business' );
			} elseif ($secondaryRole == SELLER) {
				return Redirect ( 'register/edit/seller_business' );
			}
		}
	}

	/**
	 * Redirect User to Edit Profile
	 */
	/**
     * Get Post Buyer Counter Offer Page
     * Get details of buyer counter offer 
     * @param int $buyerQuoteItemId
     * @return type
     */
    public function getMessageDetails($messageId=null,$ordid=null,$term=0) {
        //Log::info('Get posted buyer counter offer: ' . Auth::id(), array('c' => '1'));
        try {
            
            
            $messageDetails = MessagesComponent::getPerticularMessageDetails($messageId,$ordid,$term);
            $messageDetailsCount = MessagesComponent::getPerticularMessageDetailsCount($messageId,$ordid,$term);
            $actualmessageDetails=MessagesComponent::getActualMessageDetails($messageId,$ordid,$term);
            return view ( 'message.message_details', array (
                                    'messageDetails' => $messageDetails,
                                    'actualmessageDetails' => $actualmessageDetails,
                                    'messageDetailsCount' => $messageDetailsCount,
                                    'user_id'=>$this->user_pk,
                    ));
        } catch (Exception $e) {
            
        }
    }
    
    public function setMessageDetails() {
       
        try {
            $serviceId = Session::get('service_id');
            
            if(Input::get('save_as_draft')) {
                $is_draft = 1; //if login then use this method
            } elseif(Input::get('send_message')) {
                $is_draft = 0;
            } else {
                $is_draft = '';
            }
            $inputForMessage = Input::all();
            $inputForMessage['is_draft'] = $is_draft;
            
            //Loading respective service data grid
            $serviceId = Session::get('service_id');
            $services   =   array(ROAD_FTL,ROAD_PTL,RAIL,AIR_DOMESTIC,AIR_INTERNATIONAL,OCEAN,RELOCATION_DOMESTIC,COURIER,RELOCATION_INTERNATIONAL,ROAD_TRUCK_HAUL,ROAD_TRUCK_LEASE,RELOCATION_PET_MOVE,RELOCATION_OFFICE_MOVE,RELOCATION_GLOBAL_MOBILITY);
            if(empty($serviceId) || $serviceId == 0) {
                MessagesComponent::setSendMessageDetails($inputForMessage,$_FILES['message_attachment']);
                
            } elseif (in_array($serviceId ,$services ) ){
                if(!empty($inputForMessage['buyer_quote_item'])) {
                    $messageType = POSTQUOTEMESSAGETYPE;
                } elseif(!empty($inputForMessage['buyer_quote_item_leads'])) {
                    $messageType = LEADSMESSAGETYPE;
                } elseif(!empty($inputForMessage['order_id_for_model'])) {
                    $messageType = ORDERMESSAGETYPE;
                } elseif(!empty($inputForMessage['contract_id_for_model'])) {
                    $messageType = CONTRACTMESSAGETYPE;
                } elseif(!empty($inputForMessage['order_id_for_model_seller'])) {
                    $messageType = ORDERMESSAGETYPE;
                } elseif(!empty($inputForMessage['buyer_quote_item_seller'])) {
                    $messageType = POSTENQURYMESSAGETYPE;
                } elseif(!empty($inputForMessage['buyer_quote_item_seller_leads'])) {
                    $messageType = LEADSMESSAGETYPE;
                } elseif(!empty($inputForMessage['buyer_quote_item_for_search_seller'])) {
                    $messageType = POSTENQURYMESSAGETYPE;
                } elseif(!empty($inputForMessage['buyer_quote_item_for_search'])) {
                    $messageType = POSTMESSAGETYPE;
                } else {
                    $messageType = GENERALMESSAGETYPE;
                }
                if(!empty($_FILES['message_attachment']))
                    MessagesComponent::setSendMessageDetails($inputForMessage,$_FILES['message_attachment'],$messageType);
                else
                    MessagesComponent::setSendMessageDetails($inputForMessage,'',$messageType);
            }
            if(strpos($_SERVER['HTTP_REFERER'],'byersearchresults')===false && strpos($_SERVER['HTTP_REFERER'],'buyerordersearch')===false && strpos($_SERVER['HTTP_REFERER'],'sellerorderSearch')===false && strpos($_SERVER['HTTP_REFERER'],'sellerlist')===false && strpos($_SERVER['HTTP_REFERER'],'buyersearchresults')===false && strpos($_SERVER['HTTP_REFERER'],'termsellersearchresults')===false)
            return Redirect::back()->with('success','Message sent successfully');
            else
                echo true;
        } catch (Exception $e) {
            
        }
    }
    
    public function getNameList() {
		///Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
           
            $term = Input::get('search');
            $displayListArray = array();
            $nameListArray = array();
            $allNameList = MessagesComponent::getUserNameListAsPerCondition($term);
            $cnt = count($allNameList);
            if($cnt>0){
                for($i=0; $i<$cnt; $i++ ){
                    $nameListArray["value"] = $allNameList[$i]->id;
                    $nameListArray["text"] = $allNameList[$i]->username." ".$allNameList[$i]->id;
                    array_push($displayListArray, $nameListArray);
                }
            }else{
                $nameListArray["value"] = "";
                $nameListArray["text"] = "No results Found";
                array_push($displayListArray, $nameListArray);
            }
            return $displayListArray;
		}
	 	catch (Exception $e) {
			
		}
	}
        
        /**
	 * Messages page.
	 * Displays some user specific Messages screen
	 *
	 * @return Response
	 */
	public function messages($service = null) {
            try {
		if (isset ( Auth::User ()->id )) {
			$userId = Auth::User ()->id;
			$roleId = Auth::User ()->lkp_role_id;
			if ($roleId == 2) {
				$lastLogin = Auth::User ()->last_login;
				if ($lastLogin == '') {
					$firstPopup = '1';
				} else {
					$firstPopup = '';
				}
			} else {
				$firstPopup = '';
			}
			
                   
                    if(isset($_GET)){
                        $allMessages = MessagesComponent::listMessages($_GET);
                    }else{
                        $allMessages = MessagesComponent::listMessages();
                    }            
                    $allMessageTypes = MessagesComponent::getMessageTypes();
                    $allServices = CommonComponent::getAllServices();
                    return view ( 'message.message_list', array (
                                    'activePopup' => $firstPopup,
                                    'userId' => $userId,
                                    'grid' => $allMessages['grid'],
                                    'allMessageTypes' => $allMessageTypes,
                                    'allServices' => $allServices

                                ) );
		} else {
			
			return Redirect ( '/home' );
		}
            }catch (Exception $e) {
			
		}
	}
        
        /**
	 * Messages page.
	 * Displays some user specific Messages screen
	 *
	 * @return Response
	 */
	public function sentMessages($service = null) {
            try {
		if (isset ( Auth::User ()->id )) {
			$userId = Auth::User ()->id;
			$roleId = Auth::User ()->lkp_role_id;
			if ($roleId == 2) {
				$lastLogin = Auth::User ()->last_login;
				if ($lastLogin == '') {
					$firstPopup = '1';
				} else {
					$firstPopup = '';
				}
			} else {
				$firstPopup = '';
			}
			
                    //$allMessages = MessagesComponent::listMessages($service, Auth::User()->id);
                    if(isset($_GET)){
                        $allMessages = MessagesComponent::listSentMessages($_GET);
                    }else{
                        $allMessages = MessagesComponent::listSentMessages();
                    }            
                    $allMessageTypes = MessagesComponent::getMessageTypes();
                    $allServices = CommonComponent::getAllServices();
                    return view ( 'message.sent_message_list', array (
                                    'activePopup' => $firstPopup,
                                    'userId' => $userId,
                                    'grid' => $allMessages['grid'],
                                    'allMessageTypes' => $allMessageTypes,
                                    'allServices' => $allServices

                                ) );
		} else {
			
			return Redirect ( '/home' );
		}
            }catch (Exception $e) {
			
		}
	}
        
        /**
	 * Seller list initial page
	 *
	 * 
	 * @return Response
	 */
	public function getfiledownload($id) {
            $res = DB::table('user_messages as um')->leftjoin('user_message_uploads as umu', 'umu.user_message_id', '=', 'um.id')
                    ->where('um.id','=',$id)
                    ->select('umu.filepath')->first();
            $path   =   $res->filepath;
          
            try{    
                $file = $path;
                header('Content-type: application/pdf');
                return response()->download($file);

            }catch (Exception $e) {

            }
            
		
	}
        /**
	 * Get username
	 *
	 * 
	 * @return Response
	 */
        public function getusername() {
		///Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
                    $res=   CommonComponent::getBuyerName($_REQUEST['userid']);
                    return $res;
		}
	 	catch (Exception $e) {
			
		}
	}
        /**
	 * Get Order No
	 *
	 * 
	 * @return Response
	 */
        public function getOrderno() {
		///Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
                    $res=   CommonComponent::getOrderno($_REQUEST['orderid']);
                    return $res;
		}
	 	catch (Exception $e) {
			
		}
	}
        /**
	 * Get contract No
	 *
	 * 
	 * @return Response
	 */
        public function getContractno() {
		///Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
		try {
                    $res=   CommonComponent::getContractno($_REQUEST['contractid']);
                    return $res;
		}
	 	catch (Exception $e) {
			
		}
	}
        
        
}