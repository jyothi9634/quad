<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Components\CommonComponent;

class ScheduleController extends Controller {

    /**
     * Create a new Orders controller instance.
     *
     * @return void
     */
    public $user_pk;

    public function __construct() {

    }
    /**
     * Sms Status Update every 15 mints.
     *
     * @return \Illuminate\Http\Response
     */
	public function storeSmsStatus(){
		CommonComponent::storeSmsStatus();
	}
}	
