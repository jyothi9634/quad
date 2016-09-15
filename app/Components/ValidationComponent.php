<?php namespace App\Components;

use Auth;
use DB;
use Input;
use Session;
use DateTime;
use Log;

class ValidationComponent{
        /** returns vlidation classes  **/
	public static function getGmSellerClass($gmServiceTypeID){
            try{
                if(isset($gmServiceTypeID)){
                    switch ($gmServiceTypeID) {

			case 1 :
				return "clsGMSRatepDay";
                        case 2 :
				return "clsGMSRatepDay";   
                        case 3 :
				return "clsGMSRatepRent"; 
                        case 4 :
				return "clsGMSRatepPerson";   
                        case 5 :
				return "clsGMSRatepPerson";   
                        case 6 :
				return "clsGMSRatepDay";   
                        case 7 :
				return "clsGMSRateFlat";    
				
                    }
                }
            } catch (Exception $ex) {

            }
        }
            
}