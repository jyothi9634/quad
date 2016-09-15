<?php

namespace App\Components\Rail;

use DB;
use App\Models\BuyerQuoteItemView;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Http\Requests;
use Input;
use Config;
use File;
use Session;
use Redirect;
use Log;
use App\Components\SellerComponent;

use App\Models\User;
use App\Models\BuyerQuoteSellersQuotesPrices;
use App\Models\CartItem;
use App\Models\PtlBuyerQuoteItemView;
use App\BuyerQuoteItems;
use App\Models\FtlSearchTerm;

class RailBuyerGetQuoteBooknowComponent {

    /**
    * Buyer counter offer Page
    * Method to retrieve buyer quote requests data
    *
    * @param int $buyerQuoteItemId
    * @return array
    */
	public static function updateBuyerQuoteDetailsViews($buyerQuoteId) {
		try {
			Log::info ('Get update buyer quote details view: ' . Auth::id (), array ('c' => '2'));
            
            $buyerCounterDetails  = DB::table('rail_buyer_quote_items as bqi')
						->where('bqi.id','=',$buyerQuoteId)
						->select('bqi.id', 'bqi.created_by')
						->get();
            if(!empty($buyerCounterDetails) && $buyerCounterDetails[0]->created_by != Auth::user()->id) {
                $viewCount = DB::table ( 'rail_buyer_quote_item_views as bqiv' )
                ->where ( 'bqiv.user_id', '=', Auth::user ()->id )
                ->where ( 'bqiv.buyer_quote_item_id', '=', $buyerQuoteId )
                ->select ( 'bqiv.id', 'bqiv.view_counts' )->get ();

                $createdAt = date ( 'Y-m-d H:i:s' );
                $createdIp = $_SERVER ['REMOTE_ADDR'];

                if (count($viewCount) == 0) {
                    $viewCountInsert = new PtlBuyerQuoteItemView ();
                    $viewCountInsert->user_id = Auth::user ()->id;
                    $viewCountInsert->buyer_quote_item_id = $buyerQuoteId;
                    $viewCountInsert->view_counts = 1;
                    $viewCountInsert->created_at = $createdAt;
                    $viewCountInsert->created_by = Auth::user ()->id;
                    $viewCountInsert->created_ip = $createdIp;
                    $viewCountInsert->save ();
                    $countview = 1;
                } else {
                    $countview = $viewCount [0]->view_counts + 1;
                    DB::table ( 'rail_buyer_quote_item_views as bqiv' )->where ( 'bqiv.user_id', '=', Auth::user ()->id )->where ( 'bqiv.buyer_quote_item_id', '=', $buyerQuoteId )->update ( array (
                            'bqiv.view_counts' => $countview,
                            'bqiv.updated_at' => $createdAt
                    ) );
                }
            } else {
				$countview = DB::table('rail_buyer_quote_item_views as bqiv')
								->where('bqiv.created_by','=',Auth::user()->id)
								->where('bqiv.buyer_quote_item_id','=',$buyerQuoteId)
								->select('bqiv.id','bqiv.view_counts')
								->get();
				if(!isset($countview[0]->view_counts)) {
                    $countview = 0;
                } else {
                    $countview = $countview[0]->view_counts;
                }
			}
			return $countview;
		} catch ( Exception $exc ) {
			// echo $exc->getTraceAsString();
			// TODO:: Log the error somewhere
		}
	}

	
}
