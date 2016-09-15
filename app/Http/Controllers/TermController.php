<?php
namespace App\Http\Controllers;

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
use Response;
use Log;

/* Term controller switch cases.
 * 
 */
class TermController extends Controller {
	
	public function CreateTermPost() {
		try {
			if(Session::get ( 'service_id' ) != ''){
				$serviceId = Session::get ( 'service_id' );
			}
			if(!empty(Input::all()))  {
				$allRequestdata=Input::all();
			}
			/*Switch cases for term post creation
			 * 
			 */
			switch($serviceId){
				case ROAD_FTL       :
					 TermBuyerComponent::TermBuyerCreateQuote($serviceId, $allRequestdata);
				break;
			}

		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}
	
}