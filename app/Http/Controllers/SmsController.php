<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Components\CommonComponent;
use App\Components\SellerOrderComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\Rail\RailBuyerGetQuoteBooknowComponent;
use App\Components\AirDomestic\AirDomesticBuyerGetQuoteBooknowComponent;
use App\Components\ResizeImage;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderTrackingDetail;
use App\Models\PickupVehicleDetail;
use App\Models\OrderInvoice;
use App\Models\SellerOrderInvoice;
use App\Models\LkpServiceCharges;
use App\Models\OrderReceipt;
use App\Components\MessagesComponent;
use DB;
use Input;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Log;
use App\Components\SellerComponent;
use App\Components\Ftl\FtlBuyerOrderComponent;
use App\Components\Intracity\IntracityBuyerOrderComponent;
use App\Components\Relocation\RelocationBuyerComponent;

class SmsController extends Controller {

    /**
     * Create a new Orders controller instance.
     *
     * @return void
     */
    public $user_pk;

    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
        if (isset(Auth::User()->id)) {

            $this->user_pk = Auth::User()->id;
        }
    }
    /**
     * Display a listing of the Orders.
     *
     * @return \Illuminate\Http\Response
     */
	public function send1($message=null){
		//Please Enter Your Details
		$user="dentalproducts"; //your username
		$password="sulotchana@1"; //your password
		$mobilenumbers="9700280383"; //enter Mobile numbers comma seperated
		$message = "test message from logistiks"; //enter Your Message
		$senderid="Logistiks"; //Your senderid
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
		//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
		// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
		$curlresponse = curl_exec($ch); // execute
		if(curl_errno($ch))
		echo 'curl error : '. curl_error($ch);
		if (empty($ret)) {
		// some kind of an error happened
		die(curl_error($ch));
		curl_close($ch); // close cURL handler
		} else {
		curl_getinfo($ch);
		curl_close($ch); // close cURL handler
		//echo "";
		echo $curlresponse; //echo "Message Sent Succesfully" ;
		}	
	}

	public function send(){
		
		//$mobiles = 9700280383,9246610156;
		$mobiles = array('9866343939','3885314144');
		$params = array(
				'username' => 'Jagadeeshp1'
			);
		$responce = CommonComponent::sendSMS($mobiles,1,$params);
		print_r($responce);
		exit;
	}

	public function smsstatus(){
		echo CommonComponent::smsStatus(2);
	}
}	
