<?php

namespace App\Components\RelocationInt\AirInt;

use App\Components\Matching\SellerMatchingComponent;
use App\Models\RelocationBuyerQuoteSellersQuotesPrice;
use App\Models\RelocationSellerPost;
use App\Models\RelocationSellerPostItem;
use App\Models\RelocationSellerSelectedBuyer;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Models\User;
use App\Models\PtlSearchTerm;
use App\Components\SellerComponent;
use App\Components\Search\SellerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;
use App\Models\PtlPincodexsector;
use App\Models\RelocationpetSellerPost;
use App\Models\RelocationpetSellerPostItem;
use App\Models\RelocationpetSellerSelectedBuyer;
use App\Models\RelocationpetBuyerQuoteSellersQuotesPrice;
use App\Components\MessagesComponent;

class RelocationAirSellerComponent {
	
	
	
    /**
     * Seller search buyer posts
     * @author Shriram
     * @param mixed $request
     * @param int $serviceId
     * @return 
     */   
    public static function getRelocationInternationSellerSearchResults($request, $serviceId) {

        try {	
           $buyerNames = array ();
            $inputparams = $request->all();
            $Query_buyers_for_sellers = SellerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request);
            $Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
            
            Session::put('seller_searchrequest_relocationint_type',1);
            Session::put('seller_searchrequest_relint_air', $request->all());

            session()->put([
                'searchMod' => [
                    'from_location_relocation'  => $request->from_location,
                    'from_location_id_relocation' => $request->from_location_id,
                    'to_location_relocation'    => $request->to_location,
                    'to_location_id_relocation' => $request->to_location_id,
                    'valid_from_relocation'     => $request->valid_from,
                    'valid_to_relocation'       => $request->valid_to,
                ]
            ]);
            
            if(isset($_REQUEST['seller_district_id']))
                session()->push('searchMod.seller_district_id_relocation', 
                    $_REQUEST['seller_district_id']
                );
            
            if (empty ( $Query_buyers_for_sellers_filter )) {
                //CommonComponent::searchTermsSendMail ();
                Session::put('layered_filter', '');
                Session::put('layered_filter_payments', '');
                Session::put('show_layered_filter','');
                Session::put('layered_filter_loadtype', '');
            }	

           if(!isset($_REQUEST['filter_set'])){ 
            // Below script for filter data getting from queries --for filters
            foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {				
                if (! isset ( $from_locations [$seller_post_item->from_location_id] )) {
                    $from_locations [$seller_post_item->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->from_location_id )->pluck ( 'city_name' );
                }
                if (! isset ( $to_locations [$seller_post_item->to_location_id] )) {
                    $to_locations [$seller_post_item->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $seller_post_item->to_location_id )->pluck ( 'city_name' );
                }                    
                if (! isset ( $buyerNames [$seller_post_item->buyer_id] )) {
                        $buyerNames[$seller_post_item->buyer_id] = $seller_post_item->username;
                        Session::put('layered_filter', $buyerNames);
                }			
             }
           }
            
            //echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;
            
            //$result = $Query_buyers_for_sellers->get ();
            
            $gridBuyer = \DataGrid::source( $Query_buyers_for_sellers );
            $gridBuyer->add('username', 'Buyer Name', true )->attributes(array("class" => "col-md-4 padding-left-none"));
            $gridBuyer->add('dispatch_date', 'Dispatch Date', true )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add('delivery_date', 'Delivery Date', true )->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('volume', 'Volume', true )->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('grid_actions', 'Grid Actions', true)->attributes(array("class" => "col-md-2 padding-none"))->style("display:none");
            $gridBuyer->add('empty_div', 'Empty', true)->attributes(array("class" => ""))->style("display:none");
            $gridBuyer->add('dom_row', 'Dom Actions', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_submit', 'Dom Action 1', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_details', 'Dom Action 2', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            
            $gridBuyer->row( function($row) {
                
                //dd($row);
                $id = $row->data->id;
                $buyer_id = $row->data->buyer_id;
                $transaction_id = $row->data->transaction_id;
                    
                $sellercomponent = new RelocationAirSellerComponent();
                $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User()->id, $id);
                $enquiry = $sellercomponent::getBuyerpostById($id);
                $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
				
                // Buyer Business name
                $row->cells[0]->attributes(array('class' => 'col-md-4 padding-left-none'))
                        ->value( ucfirst($row->data->username) );
                
                // Dispatch date
                $row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value(CommonComponent::checkAndGetDate($row->data->dispatch_date) );
				// Dispatch date
				$row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'));
				if($row->data->delivery_date != ""){
					$row->cells[2]->value(CommonComponent::checkAndGetDate($row->data->delivery_date) );
				}else{
					$row->cells[2]->value("NA");
				}


				// Dispatch date
				$volume = $sellercomponent::getAirBuyerPostCartonsCFT($id);
				$row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))
					->value($volume);

                // Action Button
                $row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none'))
                        ->value("<button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>".$submitedquotetext."</button>");
                
                // Empty Div
                $row->cells[5]->attributes(array('class' => 'clearfix'))
                        ->value('');

                // Details & Message Div       
                $row->cells[6]->attributes(array('class' => 'pull-right text-right'))
                	->value = '<div class="info-links">	
                        <span data-buyersearchlistid="'.$buyer_id.'_'.$id.'" id="'.$id.'" class="show-data-link detailsslide underline_link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</span>
                        <a href="#" data-userid="'.$buyer_id.'" data-buyer-transaction="'.$transaction_id.'" class="new_message" data-buyerquoteitemidforseller="'.$id.'"><i class="fa fa-envelope-o"></i></a>
                    	</div>';
               
                
                $buyerpostdata = array();
                $buyerpostdata['from_location_id'] = $enquiry->from_location_id;
                $buyerpostdata['to_location_id'] = $enquiry->to_location_id;
                $buyerpostdata['valid_from'] = $enquiry->dispatch_date;
                $buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
                $buyerpostdata['nquiry_volume'] = $volume;
               
                $SubmitquotePartial = view('relocationint.sellers.seller_search_submit_quote')->with([
                		'submittedquote' => $submittedquote,
                		'enquiry'=>$enquiry,
                		'id' => $id,
                		'is_search' => 1,
                		'search_params' => $buyerpostdata,
                		'international_type' => $enquiry->lkp_international_type_id,
						'lkp_international_type_id' => $enquiry->lkp_international_type_id
                ])->render();


                $row->cells[7]->attributes(array('class' => 'col-md-12 submit-data-div padding-none padding-top'))->value = $SubmitquotePartial;

				$postCartons = $sellercomponent::getAirBuyerPostCartons($id);
				if(count($postCartons) > 0){
					$row->cells[8]->attributes(array('class' => 'col-md-12 show-data-div padding-none padding-top'));
					$row->cells[8]->value = '
						<div class="table-div table-style1 margin-top">
							<div class="table-heading inner-block-bg">
								<div class="col-md-8 padding-left-none">Carton Type</div>
								<div class="col-md-4 padding-left-none">Nos</div>
							</div>
							<div class="table-data">';
								foreach($postCartons as $postCarton){
									$row->cells[8]->value .= '<div class="table-row inner-block-bg">
										<div class="col-md-8 padding-left-none">'.$postCarton->carton_type.'('.$postCarton->carton_description.')</div>
										<div class="col-md-4 padding-left-none">'.$postCarton->number_of_cartons.'</div>
									</div>';
								}
								
					$row->cells[8]->value .= '</div>
						</div>';
				}else{
					$row->cells[8]->attributes(array('class' => 'col-md-12 show-data-div padding-none padding-top'))->value = '<div class="table-div table-style1 margin-top">No Records Found</div>';
				}


              
                $row->attributes(array("class" => ""));
                
            } );
		
            $gridBuyer->orderBy ( 'id', 'desc' );
            $gridBuyer->paginate ( 5 );
            
                $result = array ();
                $result ['gridBuyer'] = $gridBuyer;
                //$result ['filter'] = $filter;
                return $result;
		
            } catch ( Exception $exc ) {}
	}

	public static function getAirBuyerPostCartons($postId){
		$postCartons = DB::table('relocationint_buyer_post_air_cartons as rbac')
						->leftjoin ( 'lkp_air_carton_types as lact', 'lact.id', '=', 'rbac.lkp_air_carton_type_id' )
						->select ( 'lact.carton_type','lact.carton_description','lact.weight','rbac.number_of_cartons')->where('rbac.buyer_post_id', $postId)->get();
		return $postCartons;
	}

    public static function getCFTfromweight($weight){
        return number_format(($weight * 3000)/1728,2);
    }

	public static function getAirBuyerPostCartonsCFT($postId){
		$postCartons = RelocationAirSellerComponent::getAirBuyerPostCartons($postId);
		if(count($postCartons) > 0){
			$weight = 0;
			foreach($postCartons as $postCarton){
				$weight += $postCarton->number_of_cartons * $postCarton->weight;
			}
			$cft = RelocationAirSellerComponent::getCFTfromweight($weight);
			return $cft." CFT";
		}
		return "0 CFT";
	}

	/**
	 * @param $id
	 */
	public static function SellerPostDetails($id){
		Session::put('seller_post_item', $id);
		$postinfo = array();
		$postDetails = DB::table('relocationpet_seller_posts')->select ( '*')->where('id', $id)->get();
		$postItemDetails = DB::table('relocationpet_seller_post_items')->select ( '*')->where('seller_post_id', $id)->get();
		//echo "<pre>";print_R($postDetails);print_R($postItemDetails);die;
		$postinfo['seller_post'] = $postDetails;
		$postinfo['seller_post_items'] = $postItemDetails;
		return $postinfo;
	}

	public static function getPrivateBuyers($id,$lkp_access_id){
		if($lkp_access_id == 2 || $lkp_access_id == 3){
			$privatebuyers  = DB::table('relocationpet_seller_selected_buyers as rssb')
				->leftjoin ( 'relocationpet_seller_posts as rsp', 'rsp.id', '=', 'rssb.seller_post_id' )
				->leftjoin('users','users.id','=','rssb.buyer_id')
				->leftjoin('buyer_details','buyer_details.user_id','=','users.id')
				->where('rssb.created_by',Auth::user()->id)
				->where('rssb.seller_post_id',$id)
				->select('users.username')
				->get();
			return $privatebuyers;
		}else{
			return array();
		}
	}
	
	public static function getBuyerpostById($postId)
	{
	
		$Query_buyers_for_sellers = DB::table('relocationint_buyer_posts as rbq');
		$Query_buyers_for_sellers->join('lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id');
		$Query_buyers_for_sellers->join('lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id');
		$Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
		$Query_buyers_for_sellers->leftjoin('relocationint_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id');
		$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);
		$Query_buyers_for_sellers->where('rbq.id', $postId);
		$Query_buyers_for_sellers->select('rbq.*', 'us.username', 'cf.city_name as fromcity', 'ct.city_name as tocity');
		$Query_buyers_for_sellers->groupBy('rbq.id');
		$results = $Query_buyers_for_sellers->get();
		return $Query_buyers_for_sellers->first();
		
		
	
	}
	public static function getSellerSubmittedQuote($seller_id, $buyerquoteId, $sellerPostId = 0)
	{
		$Query_buyers_for_sellers = DB::table('relocationint_buyer_quote_sellers_quotes_prices as sqbqp')
		->where("sqbqp.seller_id", $seller_id)
		->where("sqbqp.total_price", '>', 0)
		->where("sqbqp.buyer_quote_id", $buyerquoteId);
		if ($sellerPostId != 0) {
			//$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
		}
		$data = $Query_buyers_for_sellers->get();
		return $data;
	}
	
}
