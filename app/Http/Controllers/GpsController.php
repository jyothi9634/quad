<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Components\CommonComponent;
use DB;
use Input;
use Config;
use File;
use Session;

class GpsController extends Controller {

    /**
     * GPS Vehicle Registration.
     */
	public function register(){
		$reqParams = array(
			"DIMEI" => "123456789012152",
			"SIMEI"  =>"12345678901234567810",
			"REGNO" => "AP 20 TT 123456",
			"O" => "Airtel",
			"M" => "1234567890",
			"VNAME" => "TATA",
			"DATE" => date("Y-m-d"),
			"TYPE" => ''
		);
		$transID = CommonComponent::gpsRegistration($reqParams);
		echo $transID;
	}

    /**
     * GPS Vehicle Track.
     */
	public function track(){
		$reqParams = array(
			"OPTION" => "RECENT",
			"REGNO" => "Amaze_konark",
			"DATE"=>"RECENT"
		);
		$trackDetails = CommonComponent::gpsTrack($reqParams);
		echo $trackDetails;
	}

    /**
     * GPS Vehicle Track History.
     */
	public function trackHistory(){
		$reqParams = array(
			"OPTION" => "HISTORY",
			"REGNO" => "Amaze_konark",
			"DATE" => "RECENT" // RECENT OR Required date (yyyy-MM-dd)
		);

		$trackDetails = CommonComponent::gpsTrackHistory($reqParams);
		echo $trackDetails;
	}


    /**
     * GPS Vehicle Registration.
     */
	public function store(){
		$reqParams = array(
			"DIMEI" => "123456789012152",
			"SIMEI"  =>"12345678901234567810",
			"REGNO" => "AP 20 TT 123456",
			"O" => "Airtel",
			"M" => "1234567890",
			"VNAME" => "TATA",
			"DATE" => date("Y-m-d"),
			"TYPE" => ''
		);
		echo "<pre>";
		print_r($reqParams);
		exit;
		$transID = CommonComponent::gpsRegistration($reqParams);
		echo $transID;
	}


	/*
	*	GPS Schedule
	*/
	public function schedule(){

	}

}	
