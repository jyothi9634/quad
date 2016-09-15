<?php

namespace App\Components\RelocationPet;

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
use App\Components\Search\BuyerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;
use App\Models\PtlPincodexsector;
use App\Components\MessagesComponent;
use App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent;
use App\Components\RelocationPet\RelocationPetSellerComponent;

class RelocationPetBuyerComponent {
	
	/**
	* Relocation pet move posts list grid with filter options
	* @author Shriram
	* @return Grid, Filter
	*/
	public static function getRelocationPetmoveList($service_id, $post_status ='') {
		
		// Required Query
		$query = \App\Models\RelocationPetBuyerPost::from('relocationpet_buyer_posts as rpetm')
			->select( DB::raw("
				rpetm.*, lkp_c1.city_name as from_location_name, lkp_c2.city_name as to_location_name,
				lkp_ps.post_status, lkp_qc.quote_access, lkp_ptype.pet_type, lkp_ctype.cage_type
			"))
			->leftJoin('lkp_cities as lkp_c1', 'lkp_c1.id', '=', 'rpetm.from_location_id')
			->leftJoin('lkp_cities as lkp_c2', 'lkp_c2.id', '=', 'rpetm.to_location_id')
			->leftJoin('lkp_post_statuses as lkp_ps', 'lkp_ps.id', '=', 'rpetm.lkp_post_status_id')
			->leftJoin('lkp_quote_accesses as lkp_qc', 'lkp_qc.id', '=', 'rpetm.lkp_quote_access_id')
			->leftJoin('lkp_pet_types as lkp_ptype', 'lkp_ptype.id', '=', 'rpetm.lkp_pet_type_id')
			->leftJoin('lkp_breed_types as lkp_btype', 'lkp_btype.id', '=', 'rpetm.lkp_breed_type_id')
			->leftJoin('lkp_cage_types as lkp_ctype', 'lkp_ctype.id', '=', 'rpetm.lkp_cage_type_id')
			->where([
				'rpetm.lkp_service_id' => $service_id,
				'rpetm.buyer_id' => Auth::user()->id
			])
			->whereNotIn('rpetm.lkp_post_status_id', [6,7,8]);
		
		// Checking request 
		if (isset ( $post_status ) && $post_status != '') {
			if($post_status == 0)
				$query->whereIn ( 'rpetm.lkp_post_status_id', array(1,2,3,4,5));
			else
				$query->where ( 'rpetm.lkp_post_status_id', '=', $post_status );
		}

		// Functionality to build filters in the page starts
		// Default values for filter downs
		$from_locations[''] = "From Location";
		$to_locations[''] = "To Location";
		$pet_types[''] = "Pet Type";
		$cage_types[''] = "Cage Type";

		// Getting From Locations based on result set
		$resultSet = $query->get();
		foreach($resultSet as $r):
			$from_locations[$r->from_location_id] 	= $r->from_location_name;
			$to_locations[$r->to_location_id] 		= $r->to_location_name;
			$pet_types[$r->lkp_pet_type_id] 		= $r->pet_type;
			$cage_types[$r->lkp_cage_type_id] 		= $r->cage_type;
		endforeach;

		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		$pet_types = CommonComponent::orderArray($pet_types);
		$cage_types = CommonComponent::orderArray($cage_types);


		// Filters Start
		$filter = \DataFilter::source($query);
		
		// From locations dropdown
		$filter->add('from_location_id', '', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('rpetm.from_location_id', $value):$query;
   		});

		// To locations dropdown
		$filter->add('to_location_id', '', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('rpetm.to_location_id', $value):$query;
   		});

		// Pet types
		$filter->add('pet_type', '', 'select')->options($pet_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('rpetm.lkp_pet_type_id', $value):$query;
   		});

		// Cage types
		$filter->add('cage_type', '', 'select')->options($cage_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()")->scope( function ($query, $value) {
        	 return ($value!=0)? $query->where('rpetm.lkp_cage_type_id', $value):$query;
   		});

		// From date
		$filter->add('from_date', 'From Date', 'text')->attr(["class" => "dateRange", "onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	
        	if(!empty($value)):
        		$dispatch_date = date('Y-m-d', strtotime($value));
        		return $query->whereDate('rpetm.dispatch_date', '=', $dispatch_date);
        	else:
        		return $query;
        	endif;
   		});

		// delivery_date 
		$filter->add('to_date', 'To Date', 'text')->attr(["class" => "dateRange","onchange"=>"this.form.submit()"])->scope( function ($query, $value) {
        	if(!empty($value)):
        		$delivery_date = date('Y-m-d', strtotime($value));
        		return $query->whereDate('rpetm.delivery_date', '=', $delivery_date);
        	else:
        		return $query;
        	endif;
   		});
		$filter->build();

		$grid = \DataGrid::source($query);

		// Grid Heading
		$grid->add('dispatch_date','Dispatch Date', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('from_location_name','From', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('to_location_name','To', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('cage_type','Cage Type', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
	   	$grid->add('pet_type','Pet Type', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
		$grid->add('post_status','Status', true)->attributes(array("class" => 'col-md-2 padding-left-none')); 
		$grid->add('grid_actions', 'Grid Actions')->style("display:none");
		$grid->add('addtional_row', 'Row Actions')->style("display:none");

		//row closure
		$grid->row( function($row) {

			// Dispatch Date
			$row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->dispatch_date.'</a>');

			// From Location 
            $row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->from_location_name.'</a>');

            // To Location
            $row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->to_location_name.'</a>');

            // Cage Type
            $row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->cage_type.'</a>');

            // Pet Type
            $row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->pet_type.'</a>');

            // Post Status
            $row->cells[5]->attributes(array('class' => 'col-md-1 padding-left-none'))->value('<a href="'.url('getbuyercounteroffer/'.$row->data->id).'">'.$row->data->post_status.'</a>');

            // Checking post deleted already or not
            $row->cells[6]->attributes(array('class' => 'col-md-1 padding-left-none text-right'));

            // Private post edit button 
            if($row->data->lkp_post_status_id == OPEN):
                if($row->data->lkp_quote_access_id == 2):
            		$row->cells[6]->value = '<a href="'.url('editrelocationbuyerquote/'.$row->data->id).'"><i class="fa fa-edit" title="Edit"></i></a> &nbsp;';
            	endif;

                $row->cells[6]->value .= '<a href="" data-target="#cancelbuyerpostmodal" data-toggle="modal" onclick="setcancelbuyerpostid('.$row->data->id.')"><i class="fa fa-trash" title="Delete"></i></a>';
            else:
                $row->cells[6]->value = '';
            endif;

            // Message Count           
            $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$row->data->id);

            // Quotes Count 
            $quoteCount = self::getBuyerPetmoveQuoteCount($row->data->id, RELOCATION_PET_MOVE);

            // Total post view count
            $viewcount = PtlBuyerGetQuoteBooknowComponent::updateBuyerQuoteDetailsViews($row->data->id,'relocationpet_buyer_post_views');

            $row->cells[7]->value = '
			<div class="clearfix"></div>
            	<div class="pull-left">
					<div class="info-links">												
						<a href="'.url('getbuyercounteroffer/'.$row->data->id.'?type=messages').'"><i class="fa fa-envelope-o"></i> Messages<span class="badge">'.count($msg_count['result']).'</span></a>
						<a href="'.url('getbuyercounteroffer/'.$row->data->id.'?type=quotes').'"><i class="fa fa-file-text-o"></i> Quotes<span class="badge">'.$quoteCount.'</span></a>
						<a href="#"><i class="fa fa-line-chart"></i> Market Analytics<span class="badge">0</span></a>
						<a href="#"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">0</span></a>												
					</div>
				</div>
				<div class="pull-right text-right">
					<div class="info-links">
						<a><span class="views red"><i class="fa fa-eye" title="Views"></i> '.$viewcount.' </span></a>
					</div>
				</div>';

			$row->attributes(array("class" => ""));
		});
		
	    $grid->orderBy('rpetm.id','desc'); //default orderby
   		$grid->paginate(5); //pagination*/

		//Functionality to build filters in the page ends
		return ['grid' => $grid, 'filter' => $filter];
	}
    
    /**
	* Relocation pet move posts list grid with filter options
	* @author Shriram
	* @return count
	*/
	public static function getBuyerPetmoveQuoteCount($buyer_post_id, $service_id){
		$rows = \App\Models\RelocationpetBuyerQuoteSellersQuotesPrice::selectRaw("count(*) as totRows")
			->where('total_price', '!=', '0.00')
			->where([
				'buyer_id' => Auth::id(),
				'buyer_quote_id' => $buyer_post_id,
				'lkp_service_id' => $service_id
			])->first();
		return $rows->totRows;
	}

    /**
	* Buyer pet move post Market leads list component
	* @author Srinivas and date : 12th May, 2016
	* @return Response
	*/
	public static function getRelocationPetBuyerMarketLeadsList(){

		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$Query = DB::table ( 'relocationpet_seller_posts as rsp' );
		$Query->leftjoin ( 'relocationpet_seller_post_items as rspi', 'rspi.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'relocationpet_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id' );
                $Query->join ( 'lkp_payment_modes as paymode', 'rsp.lkp_payment_mode_id', '=', 'paymode.id' );                
		$Query->join ( 'lkp_pet_types as lkpt', 'rspi.lkp_pet_type_id', '=', 'lkpt.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		$Query->join ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		$Query->where( 'rsp.lkp_post_status_id', 2);
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		$Query->where('rspi.is_private', 0);
		if (isset ( $_POST ['status_id'] ) && $_POST ['status_id'] != '') {
			$Query->where ( 'rsp.lkp_post_status_id', '=', $_POST ['status_id'] );
		}
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'rsp.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'rsp.to_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}
		
		
		$Query->groupBy('rsp.id');
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','lkpt.pet_type','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity', 'paymode.payment_mode as paymentmode','rspi.id as sellerpostItemId')->get ();
		foreach ( $postResults as $post ) {
				
			if (! isset ( $from_locations [$post->from_location_id] )) {
				$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
			}
			if (! isset ( $to_locations [$post->to_location_id] )) {
				$to_locations [$post->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->to_location_id )->pluck ( 'city_name' );
			}
		
				
		}
		$from_locations = CommonComponent::orderArray($from_locations);
		$to_locations = CommonComponent::orderArray($to_locations);
		
		$grid = DataGrid::source ( $Query );
                //echo "<pre>"; dd($postResults); die;		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Name', 'username' )->attributes(array("class" => "col-md-4 padding-left-none"));
		$grid->add ( 'fromCity', 'From', 'fromCity' )->style ( "display:none" );
		$grid->add ( 'toCity', 'To', 'toCity' )->style ( "display:none" );
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
		$grid->add ( 'pet_type', 'pet type', 'pet_type' )->style ( "display:none" );
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->style ( "display:none" );
		$grid->add ( 'to_location_id', 'To', 'to_location_id' )->style ( "display:none" );
                $grid->add ( 'terms_conditions', 'terms_conditions', 'terms_conditions' )->style ( "display:none" );
                $grid->add ( 'tracking', 'tracking', 'tracking' )->style ( "display:none" );
		$grid->add ( 'paymentmode', 'paymentmode', 'paymentmode' )->style ( "display:none" );
                $grid->add ( 'cancellation_charge_price', 'cancellation charges', 'cancellation_charge_price' )->style ( "display:none" );
                $grid->add ( 'docket_charge_price', 'docket charges', 'docket_charge_price' )->style ( "display:none" );
                $grid->add ( 'transaction_id', 'transation Id', 'transaction_id' )->style ( "display:none" );
                $grid->add ( 'seller_id', 'seller Id', 'seller_id' )->style ( "display:none" );
                $grid->add ( 'sellerpostItemId', 'Seller Post Item Id', 'sellerpostItemId' )->style ( "display:none" );
                
		$grid->orderBy ( 'rsp.id', 'desc' );
		$grid->paginate ( 5 );
		
		$grid->row ( function ($row) {
                    
                    $row->cells [0]->style ( 'display:none' );
                    $row->cells [1]->style ( 'display:none' );
                    $row->cells [2]->style ( 'display:none' );
                    $row->cells [3]->style ( 'display:none' );
                    $row->cells [4]->style ( 'display:none' );
                    $row->cells [5]->style ( 'display:none' );
                    $row->cells [6]->style ( 'display:none' );
                    $row->cells [8]->style ( 'display:none' );
                    $row->cells [9]->style ( 'display:none' );
                    $row->cells [10]->style ( 'display:none' );
                    $row->cells [11]->style ( 'display:none' );
                    $row->cells [12]->style ( 'display:none' );
                    $row->cells [13]->style ( 'display:none' );
                    $row->cells [14]->style ( 'display:none' );
                    $row->cells [15]->style ( 'display:none' );
                    $row->cells [16]->style ( 'display:none' );
                    $row->cells [17]->style ( 'display:none' );

                    $seller_post_id = $row->cells[0]->value;
                    $username=$row->cells [1]->value;
                    $fromCity=$row->cells [2]->value;
                    $toCity=$row->cells [3]->value;
                    $buyer_id=Auth::User ()->id;
                    $validfrom=$row->cells [4]->value;
                    $validto=$row->cells [5]->value;
                    $termconiditons=$row->cells [10]->value;  
                    $tracking=$row->cells [11]->value;  
                    $paymentmode=$row->cells [12]->value;                     
                    $cancellationcharges=$row->cells [13]->value;  
                    $docketcharges=$row->cells [14]->value;  
                    $transaction_id=$row->cells [15]->value;  
                    $seller_user_id=$row->cells [16]->value;  
                    $sellerpostItemId=$row->cells [17]->value;

                    $tracking_text = CommonComponent::getTrackingType($tracking);
					$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;
                    
                    $seller_payment_mode_method = CommonComponent::getSellerPostPaymentMethod($paymentmode);
                     if ($paymentmode == 'Advance') {
                                $paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
                        } elseif ($paymentmode == 'Credit'){
                                $credit_days = CommonComponent::getCreditdays($sellerpostItemId,'relocationpet_seller_posts','relocationpet_seller_post_items');
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode.' | '.$credit_days[0]->credit_period.' '.$credit_days[0]->credit_period_units;
                        }else {
                                $paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
                        }
                    
                    $msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
                    $sellerPostDetails=RelocationPetSellerComponent::SellerPostDetails($seller_post_id);

                    $seller_post=$sellerPostDetails['seller_post'][0];
                    $seller_post_items=$sellerPostDetails['seller_post_items'];


                    $totalAmount = 0;

                    $url = url().'/buyerbooknowforsearch/'.$seller_post_id;
                    $row->cells [7]->value = "<div class='table-data'>
                                <div class='table-row '>
                                <div class='col-md-4 padding-left-none'>
                                        $username
                                        <div class='red'>
                                                <i class='fa fa-star'></i>
                                                <i class='fa fa-star'></i>
                                                <i class='fa fa-star'></i>
                                        </div>
                                </div>
                                <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($validfrom)."</div>
                                <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($validto)."</div>
                                

                                <div class='clearfix'></div>

                                <div class='pull-left'>
                                        <div class='info-links'>
                                                <a href='#'>$track_type</a>
                                                <a href='#'>$paymentType</a>
                                        </div>
                                </div>
                                <div class='pull-right text-right'>
                                        <div class='info-links'>
                                                <a id='".$seller_post_id."' class='viewcount_show-data-link view_count_update' data-quoteId='$seller_post_id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
                                                <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_user_id."' data-buyerquoteitemid='".$seller_post_id."'><i class='fa fa-envelope-o'></i></a>
                                        </div>
                                </div>

                                <div class='col-md-12 show-data-div padding-top'>


                        <h3 class='margin-none'><i class='fa fa-map-marker'></i>$fromCity to $toCity</h3>

                        <div class='table-div table-style1 margin-top margin-bottom padding-none'>

                        <div class='table-heading inner-block-bg'>
                                <div class='col-md-2 padding-left-none'>Pet Type</div>
                                <div class='col-md-2 padding-left-none'>Cage Type</div>
                                <div class='col-md-4 padding-left-none'>O & D Charges</div>
                                <div class='col-md-2 padding-left-none'>Freight</div>
                                <div class='col-md-2 padding-left-none'>Transit Days</div>										
                        </div>

                        <div class='table-data'>";
                    
                    foreach($seller_post_items as $seller_post_edit_action_line){
                        
                        $row->cells [7]->value .=" <div class='table-row inner-block-bg'>
                                    <div class='col-md-2 padding-left-none'>".CommonComponent::getPetType($seller_post_edit_action_line->lkp_pet_type_id)."</div>
                                    <div class='col-md-2 padding-left-none'> ".CommonComponent::getCageType($seller_post_edit_action_line->lkp_cage_type_id)."</div>
                                    <div class='col-md-4 padding-left-none'>$seller_post_edit_action_line->od_charges /-</div>
                                    <div class='col-md-2 padding-none'>$seller_post_edit_action_line->rate_per_cft /-</div>
                                    <div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->transitdays $seller_post_edit_action_line->units</div>
                            </div>";
                    
                    }
                            
                        $row->cells [7]->value .=" </div>	
                                    </div>
               
                                <div class='col-md-12 padding-none form-control-fld'>
                                        <span class='data-head'>Additinal Charges</span>
                                </div>

                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Cancellation Charges : $cancellationcharges/-</span>
                                </div>
                                <div class='col-md-3 padding-left-none'>
                                        <span class='data-value'>Docket Charges : $docketcharges/-</span>
                                </div>
                                <div class='clearfix'></div> ";
                        
                        if($termconiditons!='') {
                        $row->cells [7]->value .="<div class='col-md-12 form-control-fld padding-none'>
                                    <span class='data-head'>Terms & Conditions</span>
                                    <span class='data-value'>$termconiditons</span>
                                </div>";
                        }
                                
                    $row->cells [7]->value .=" </div>
                </div>
            </div>";
                        
			
			
                });

                $filter = DataFilter::source ( $Query );
                $filter->add ( 'rsp.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
                $filter->add ( 'rsp.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
                $filter->submit ( 'search' );
                $filter->reset ( 'reset' );
                $filter->build ();

                $result = array ();
                $result ['grid'] = $grid;
                $result ['filter'] = $filter;
                return $result;

	}
        
        
	
	// buyer search for seller posts result component for relocation domestic
	public static function getRelocationPetBuyerSearchResults($request, $serviceId) {
		try {
			$prices = array();
            $sellerNames=array();
            $paymentMethods = array ();
			
			
                        
            $request['trackingfilter'] = array();
            if (isset ( $request['tracking'] ) && $request['tracking']!= '') {
                $request['trackingfilter'][] = $request['tracking'];
            }
            if (isset ( $request ['tracking1'] ) && $request ['tracking1'] != '') {
                $request['trackingfilter'][] = $request['tracking1'];
            }
           
			$Query_buyers_for_sellers = BuyerSearchComponent::search ($roleId=null,$serviceId,$statusId=null, $request );
			$Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();	
			//echo "<pre>"; print_R($Query_buyers_for_sellers_filter); die;			
			//Session::put('relocbuyerrequest', $request->all());	
                        
            if(isset($_REQUEST['from_location']) && $_REQUEST['from_location'] && isset($_REQUEST['to_location']) && $_REQUEST['to_location']!='' && isset($_REQUEST['from_date']) && $_REQUEST['from_date'] )
			{	
				session()->put([
						'searchMod' => [
							  'delivery_date_buyer'    => $request->to_date,
							  'dispatch_date_buyer'    => $request->from_date,				
							  'from_city_id_buyer'     => $request->from_location_id,
							  'to_city_id_buyer'       => $request->to_location_id,
							  'from_location_buyer'    => $request->from_location,
							  'to_location_buyer'      => $request->to_location,
                			  'pet_type_reslocation'   => $request->selPettype,
               				  'cage_type_reslocation'  => $request->selCageType,
                			  'breed_type_reslocation' => $request->selBreedtype
                
                ]
             ]);
			}			
			//Save Data in sessions			
			if (empty ( $Query_buyers_for_sellers ) && !isset($_REQUEST['filter_set'])) {
				//CommonComponent::searchTermsSendMail ();				
				Session::put('layered_filter', '');
				Session::put('layered_filter_payments', '');
				Session::put('show_layered_filter','');
			}
			// Below script for filter data getting from queries --for filters
            if(!isset($_REQUEST['filter_set'])){
				foreach ( $Query_buyers_for_sellers_filter as $seller_post_item ) {
                    if (! isset ( $paymentMethods [$seller_post_item->lkp_payment_mode_id] ) ) {
                        $paymentMethods[$seller_post_item->lkp_payment_mode_id] = $seller_post_item->payment_mode;
                        Session::put('layered_filter_payments', $paymentMethods);  
                    }
                    
                    if (! isset ( $sellerNames [$seller_post_item->seller_id] ) ) {
                        $sellerNames[$seller_post_item->seller_id] = $seller_post_item->username;
                        Session::put('layered_filter', $sellerNames);	 
                    }					
				}
			}
            if (empty ( $Query_buyers_for_sellers ) && !isset($_REQUEST['filter_set'])) {
                CommonComponent::searchTermsSendMail ();
            }
            $result = $Query_buyers_for_sellers->get ();
            

            $Query_buyers_for_sellersnew = array();
            foreach($result as $Query_buyers_for_seller){
                $resp = ($Query_buyers_for_seller->rate_per_cft*$Query_buyers_for_seller->cage_weight)+$Query_buyers_for_seller->od_charges;
                $Query_buyers_for_seller->newprice = isset($resp) ? $resp : 0;
                $prices[] = $Query_buyers_for_seller->newprice;
                $Query_buyers_for_sellersnew[] = $Query_buyers_for_seller;
            }

            if (isset ( $_REQUEST ['price'] ) && $_REQUEST ['price'] != '') {
				$splitprice = explode("    ",$_REQUEST ['price']);
				$from = trim(filter_var($splitprice[0],FILTER_SANITIZE_NUMBER_INT),"-");
				$to = trim(filter_var($splitprice[1],FILTER_SANITIZE_NUMBER_INT),"-");
				$_REQUEST['price_from'] = $from;
				$_REQUEST['price_to'] = $to;
			}else{
				if(!empty($prices)){
					$_REQUEST['price_from'] = floor(min($prices));
					$_REQUEST['price_to'] = ceil(max($prices));
					$_REQUEST['filter_price_from'] = $_REQUEST['price_from'];
					$_REQUEST['filter_price_to'] = $_REQUEST['price_to'];
				}else{
					$_REQUEST['price_from'] = 0;
					$_REQUEST['price_to'] = 1000;
				}
			}

	        if(isset($_REQUEST['price_from']) && isset($_REQUEST['price_to'])){
	            $pricefrom = $_REQUEST['price_from'];
	            $priceto = $_REQUEST['price_to'];
	            foreach($Query_buyers_for_sellersnew as $key => $Query_buyers_for_sellersnewrow){
	                if($Query_buyers_for_sellersnewrow->newprice >= $pricefrom && $Query_buyers_for_sellersnewrow->newprice <= $priceto){}
	                else{
	                    unset($Query_buyers_for_sellersnew[$key]);
	                }
	            }
	            $result = $Query_buyers_for_sellersnew;
	        }
	        if (empty ( $result ) && !isset($_REQUEST['filter_set'])) {
	            Session::put('show_layered_filter','');
	        }
                    
                        
			//echo "<pre>";print_R($_REQUEST);print_R($result);die;
			$gridBuyer = DataGrid::source ( $result );
			$gridBuyer->add ( 'id', 'ID', true )->style ( "display:none" );
			$gridBuyer->add ( 'username', 'Name', false )->attributes(array("class" => "col-md-3 padding-left-none"));
                        $gridBuyer->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-3 padding-left-none"));
			$gridBuyer->add ( 'to_date', 'Valid To ', 'to_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
			$gridBuyer->add ( 'transitdays', 'Transit Days', false )->style ( "display:none" );	
			$gridBuyer->add ( 'test', 'Below Grid', true )->style ( "display:none" );			
			$gridBuyer->add ( 'rate_per_cft', 'Rate per cft fright', false )->style ( "display:none" );		
			$gridBuyer->add ( 'tracking', 'Tracking', false )->style ( "display:none" );
			$gridBuyer->add ( 'payment_mode', 'Payment mode', false )->style ( "display:none" );
			$gridBuyer->add ( 'transaction_id', 'Transaction Id',false )->style('display:none');
			$gridBuyer->add ( 'created_by', 'created by', 'created_by' )->style ( "display:none" );				
			$gridBuyer->add ( 'price', 'Price', false )->attributes(array("class" => "col-md-2 padding-left-none"));			              
                        $gridBuyer->add ( 'cancellation_charge_price', 'cancellation_charge_price', 'cancellation_charge_price' )->style ( "display:none" );   
                        $gridBuyer->add ( 'docket_charge_price', 'docket_charge_price', 'docket_charge_price' )->style ( "display:none" );   
                        $gridBuyer->add ( 'fromcity', 'From City', 'fromcity' )->style ( "display:none" );	
                        $gridBuyer->add ( 'tocity', 'To City', 'tocity' )->style ( "display:none" );
                        $gridBuyer->add ( 'terms_conditions', 'TermsandConditions', 'terms_conditions' )->style ( "display:none" );	
                        $gridBuyer->add ( 'lkp_pet_type_id', 'Pet Type Id', 'lkp_pet_type_id' )->style ( "display:none" );	
                        $gridBuyer->add ( 'lkp_cage_type_id', 'Cage Type Id', 'lkp_cage_type_id' )->style ( "display:none" );	
                        $gridBuyer->add ( 'units', 'Units', 'units' )->style ( "display:none" );
                        $gridBuyer->add ( 'od_charges', 'Od Charges', 'od_charges' )->style ( "display:none" );
                        $gridBuyer->add ( 'cage_weight', 'Cage weight Charges', 'cage_weight' )->style ( "display:none" );
                        $gridBuyer->add ( 'postid', 'postid', 'postid' )->style ( "display:none" );
			$gridBuyer->orderBy ( 'id', 'desc' );
			$gridBuyer->paginate ( 5 );
			
			$gridBuyer->row ( function ($row) {
				$row->cells [0]->style ( 'display:none' );
				$row->cells [1]->style ( 'display:none' );				
				$row->cells [2]->style ( 'display:none' );							
				$row->cells [3]->style ( 'display:none' );
				$row->cells [4]->style ( 'display:none' );				
				$row->cells [6]->style ( 'display:none' );				
				$row->cells [7]->style ( 'display:none' );
				$row->cells [8]->style ( 'display:none' );
				$row->cells [9]->style ( 'display:none' );
				$row->cells [10]->style ( 'display:none' );
				$row->cells [11]->style ( 'display:none' );
                                $row->cells [12]->style ( 'display:none' );
                                $row->cells [13]->style ( 'display:none' );
                                $row->cells [14]->style ( 'display:none' );
                                $row->cells [15]->style ( 'display:none' );
                                $row->cells [16]->style ( 'display:none' );
                                $row->cells [17]->style ( 'display:none' );
                                $row->cells [18]->style ( 'display:none' );
                                $row->cells [19]->style ( 'display:none' );
                                $row->cells [20]->style ( 'display:none' );
                                $row->cells [21]->style ( 'display:none' );
				$row->cells [22]->style ( 'display:none' );
				$id = $row->cells [0]->value;
                                $postid = $row->cells [22]->value;
				$sellername = $row->cells [1]->value;
				if(isset($_REQUEST['total_hidden_volume']) && !empty($_REQUEST['total_hidden_volume'])){
					$volume = $_REQUEST['total_hidden_volume'];
				}else{
					$volume = $row->cells [2]->value;
				}

                                $id = $row->cells [0]->value;
				$buyerName = $row->cells [1]->value;				                            
				$fromDate = $row->cells [2]->value;
				$toDate = $row->cells [3]->value;
                                $transDays = $row->cells [4]->value;
                                $frightPerCft = $row->cells [6]->value;
                                $tracking = $row->cells [7]->value;
                                $paymentmode = $row->cells [8]->value;	
                                $transaction_id = $row->cells [9]->value;	
                                $seller_id = $row->cells [10]->value;
                                $cancellationCharges = $row->cells [12]->value;	
                                $docketCharges = $row->cells [13]->value;	
                                $fromCity = $row->cells [14]->value;
                                $toCity = $row->cells [15]->value;
                                $termsAndConditions = $row->cells [16]->value;
                                $petTypeId = $row->cells [17]->value;
                                $cageTypeId = $row->cells [18]->value;
                                $units = $row->cells [19]->value;
                                $odCharges = $row->cells [20]->value;
				$cagecharges = $row->cells [21]->value;                                
				
                    $tracking_text = CommonComponent::getTrackingType($tracking);
				$track_type = '<i class="fa fa-signal"></i>&nbsp;'.$tracking_text;
				if ($paymentmode == 'Advance') {
					$paymentType = '<i class="fa fa-credit-card"></i>&nbsp;Online Payment';
				} else {
					$paymentType = '<i class="fa fa-rupee"></i>&nbsp;'.$paymentmode;
				}
				//Total Price caclulations
                                $totalPrice = $cagecharges*$frightPerCft+$odCharges;
                                
				CommonComponent::viewCountForSeller(Auth::User()->id,$id,'relocation_seller_post_views');
                                
                                
				$url = url().'/buyerbooknowforsearch/'.$row->cells [22];
				$row->cells [5]->value="<form method='GET'role='form' action='$url' id='addptlbuyersearchbooknow_$id' name='addptlbuyersearchbooknow_$id'>"
                                        . "<div class='table-data'><div class='table-row '>
                                        <div class='col-md-3 padding-left-none'>
                                                $buyerName
                                                <div class='red'>
                                                        <i class='fa fa-star'></i>
                                                        <i class='fa fa-star'></i>
                                                        <i class='fa fa-star'></i>
                                                </div>
                                        </div>
                                        <div class='col-md-3 padding-left-none'>".CommonComponent::checkAndGetDate($fromDate)."</div>
                                        <div class='col-md-2 padding-left-none'>".CommonComponent::checkAndGetDate($toDate)."</div>
                                        <div class='col-md-2 padding-left-none'>$totalPrice /-</div>
                                        <div class='col-md-2 padding-none'>
                                                <button class='btn red-btn pull-right'>Book Now</button>
                                        </div>

                                        <div class='clearfix'></div>

                                        <div class='pull-left'>
                                                <div class='info-links'>
                                                        <a href='#'>$track_type</a>
                                                        <a href='#'>$paymentType</a>
                                                </div>
                                        </div>
                                        <div class='pull-right text-right'>
                                                <div class='info-links'>
                                                        <a id='".$id."' class='viewcount_show-data-link view_count_update' data-quoteId='$id'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>
                                                        <a href='#' class='new_message' data-transaction_no='".$transaction_id."' data-userid='".$seller_id."' data-buyerquoteitemid='".$id."'><i class='fa fa-envelope-o'></i></a>
                                                </div>
                                        </div>

                                <div class='col-md-12 show-data-div padding-top'>

                                <h3 class='margin-none'><i class='fa fa-map-marker'></i>$fromCity to $toCity</h3>
                                <div class='table-div table-style1 margin-top margin-bottom padding-none'>                               

                                <div class='table-heading inner-block-bg'>
                                        <div class='col-md-2 padding-left-none'>Pet Type</div>
                                        <div class='col-md-2 padding-left-none'>Cage Type</div>
                                        <div class='col-md-4 padding-left-none'>O & D Charges</div>
                                        <div class='col-md-2 padding-left-none'>Freight</div>
                                        <div class='col-md-2 padding-left-none'>Transit Days</div>
                                </div>                                
                                        <div class='table-data'>
                                        <div class='table-row inner-block-bg'>
                                        <div class='col-md-2 padding-left-none'>".CommonComponent::getPetType($petTypeId)."</div>
                                        <div class='col-md-2 padding-left-none'> ".CommonComponent::getCageType($cageTypeId)."</div>
                                        <div class='col-md-4 padding-left-none'>$odCharges /-</div>
                                        <div class='col-md-2 padding-none'>$frightPerCft /-</div>
                                        <div class='col-md-2 padding-left-none'>$transDays $units</div>
                                        </div>                                        
                                </div></div>
                        <div class='clearfix'></div>

                                        <div class='col-md-12 padding-none form-control-fld'>
                                                <span class='data-head'>Additional Charges</span>
                                        </div>
                                        <div class='col-md-3 padding-left-none'>
                                                <span class='data-value'>Cancellation Charges : $cancellationCharges /-</span>
                                        </div>
                                        <div class='col-md-3 padding-left-none'>
                                                <span class='data-value'>Docket Charges : $docketCharges /-</span>
                                        </div>
                                        <div class='clearfix'></div> ";
                        
                        if($termsAndConditions!='') {
                        $row->cells [5]->value .= "<div class='col-md-12 form-control-fld padding-none'>
                                                        <span class='data-head'>Terms & Conditions</span>
                                                        <span class='data-value'>$termsAndConditions</span>
                                                   </div>";
                        }
                        
                        $row->cells [5]->value .= " </div></div></div>
                                <input id='buyersearch_booknow_buyer_id_$id' type='hidden' value=".Auth::User()->id." name='buyersearch_booknow_buyer_id_$id' >
                                <input id='buyersearch_booknow_seller_id_$id' type='hidden' value=".$seller_id." name='buyersearch_booknow_seller_id_$id'>
                                <input id='buyersearch_booknow_seller_price_$postid' type='hidden' value=".$totalPrice." name='buyersearch_booknow_seller_price_$postid'>
                                <input id='buyersearch_booknow_from_date_$id' type='hidden' value=".$fromDate.">
                                <input id='buyersearch_booknow_to_date_$id' type='hidden' value=".$toDate.">
                                <input id='buyersearch_booknow_dispatch_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_dispatch_date_buyer'))."'>
                                <input id='buyersearch_booknow_delivery_date_$id' type='hidden' value='".CommonComponent::convertDateForDatabase(Session::get('session_delivery_date_buyer'))."'></form>";
			} );
			
				
				$result = array ();
				$result ['gridBuyer'] = $gridBuyer;
				//$result ['filter'] = $filter;
				return $result;
			
		} catch ( Exception $exc ) {		
		}
	}
	
	public static function getRelocationBuyerLeadPostsList(){

		$from_locations = array (
				"" => "From Location"
		);
		$to_locations = array (
				"" => "To Location"
		);
		$Query = DB::table ( 'relocation_seller_posts as rsp' );
		$Query->leftjoin ( 'relocation_seller_post_items as rspi', 'rspi.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'relocation_seller_selected_buyers as rsb', 'rsb.seller_post_id', '=', 'rsp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id' );
		$Query->join ( 'lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id' );
		$Query->join ( 'lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id' );
		$Query->join ( 'lkp_post_ratecard_types as prct', 'rsp.rate_card_type', '=', 'prct.id' );
		$Query->join ( 'lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id' );
		$Query->join ( 'users as u', 'rsp.seller_id', '=', 'u.id' );
		$Query->where( 'rsp.lkp_post_status_id', 2);
		$Query->where( 'rsb.buyer_id', Auth::User ()->id);
		$Query->where('rspi.is_private', 0);
		if (isset ( $post_status ) && $post_status != '') {
			$Query->where ( 'rsp.lkp_post_status_id', '=', $post_status );
		}
		if (isset ( $_GET ['dispatch_date'] ) && $_GET ['dispatch_date'] != '') {
			$commonDispatchDate = CommonComponent::convertDateForDatabase($_GET ['dispatch_date']);
			$Query->where ( 'rsp.from_date', '>=', $commonDispatchDate );
			$from_date = $commonDispatchDate;
			//echo "From Date :"; echo $from_date;die();
		}
		if (isset ( $_GET ['delivery_date'] ) && $_GET ['delivery_date'] != '') {
			$commonDeliveryhDate = CommonComponent::convertDateForDatabase($_GET ['delivery_date']);
			$Query->where ( 'rsp.to_date', '<=', $commonDeliveryhDate);
			$to_date = $commonDeliveryhDate;
			//echo "To Date :"; echo $to_date;die();
		}
		
		if( isset($_REQUEST['search']) && $_REQUEST['post_for']!=0){
			$post_for=$_REQUEST['post_for'];
			$Query->whereRaw('rsp.rate_card_type = "'.$post_for.'"');
		}
		$Query->groupBy('rsp.id');
		$postResults = $Query->select ( 'rsp.*', 'u.username','u.id as user_id','prct.ratecard_type','ps.post_status', 'ct.city_name as toCity', 'cf.city_name as fromCity')->get ();
		foreach ( $postResults as $post ) {
				
			if (! isset ( $from_locations [$post->from_location_id] )) {
				$from_locations [$post->from_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->from_location_id)->pluck ( 'city_name' );
			}
			if (! isset ( $to_locations [$post->to_location_id] )) {
				$to_locations [$post->to_location_id] = DB::table ( 'lkp_cities' )->where ( 'id', $post->to_location_id )->pluck ( 'city_name' );
			}
		
				
		}
		$grid = DataGrid::source ( $Query );
		
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'username', 'Seller Name', 'username' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'fromCity', 'From', 'fromCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'toCity', 'To', 'toCity' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-1 padding-left-none"));
		$grid->add ( 'ratecard_type', 'Property Type', 'ratecard_type' )->attributes(array("class" => "col-md-2 padding-left-none"));
		$grid->add ( 'dummy', '', 'show' )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->style ( "display:none" );
		$grid->add ( 'to_location_id', 'To', 'to_location_id' )->style ( "display:none" );
		
		$grid->orderBy ( 'rsp.id', 'desc' );
		$grid->paginate ( 5 );
		
		$grid->row ( function ($row) {
			
			$seller_post_id = $row->cells[0]->value;
			$username=$row->cells [1]->value;
			$buyer_id=Auth::User ()->id;
			$row->cells [0]->style ( 'display:none' );
			$row->cells [4]->value = CommonComponent::checkAndGetDate($row->cells [4]->value);
			$row->cells [5]->value = CommonComponent::checkAndGetDate($row->cells [5]->value);
			$row->cells [1]->value="$username
			<div class='red'>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			<i class='fa fa-star'></i>
			</div>";
			$row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [2]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 padding-left-none"));
			$row->cells [5]->attributes(array("class" => "col-md-1 padding-left-none"));
			$row->cells [6]->attributes(array("class" => "col-md-1 padding-left-none"));
			$row->cells [8]->style ( 'display:none' );
			$row->cells [9]->style ( 'display:none' );
			$msg_count  =    MessagesComponent::listMessages(null,POSTMESSAGETYPE,null,$seller_post_id);
			$sellerPostDetails=RelocationSellerComponent::SellerPostDetails($seller_post_id);
			
			$seller_post=$sellerPostDetails['seller_post'][0];
			$seller_post_items=$sellerPostDetails['seller_post_items'];
			
		
			$householdItems = 0;
			$vehicleItems = 0;
			foreach($seller_post_items as $key=>$seller_post_edit_action_line){
				if($seller_post_edit_action_line->rate_card_type == 1){
					$householdItems++;
					$totalAmounthouse = ($seller_post_edit_action_line->volume*$seller_post_edit_action_line->rate_per_cft)+$seller_post_edit_action_line->transport_charges;
				}elseif($seller_post_edit_action_line->rate_card_type == 2){
					$vehicleItems++;
					$totalAmountveh = $seller_post_edit_action_line->rate_per_cft+$seller_post_edit_action_line->transport_charges;
				}
			}
				
			if($householdItems>0){
				$totalAmount=$totalAmounthouse;
			}
			if($vehicleItems>0){
				$totalAmount=$totalAmountveh;
			}
			if($householdItems>0 && $vehicleItems>0){
				$totalAmount=$totalAmounthouse+$totalAmountveh;
			}
			
			$url = url().'/buyerbooknowforsearch/'.$seller_post_id;
			$row->cells [7]->value = "<div class='col-md-2 padding-none text-right'>
			<form name='addptlbuyersearchbooknow_$seller_post_id' id='addptlbuyersearchbooknow_$seller_post_id' action='$url' role='form' method='GET'>
			<div class='volume_calc'>
			<!-- input type='submit' value='Book Now' data-booknow_list='56' data-buyerpostofferid='$seller_post_id' data-url='$url' class='btn red-btn pull-right buyer_book_now' --!>
			<input id='buyersearch_booknow_buyer_id_$seller_post_id' value='$buyer_id' name='buyersearch_booknow_buyer_id_$seller_post_id' type='hidden'>
		    <input id='buyersearch_booknow_seller_id_$seller_post_id' value='$seller_post->seller_id' name='buyersearch_booknow_seller_id_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_seller_price_$seller_post_id' value='$totalAmount' name='buyersearch_booknow_seller_price_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_from_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [4]->value)." name='buyersearch_booknow_from_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_to_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [5]->value)." name='buyersearch_booknow_to_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_dispatch_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [4]->value)." name='buyersearch_booknow_dispatch_date_$seller_post_id' type='hidden'>
			<input id='buyersearch_booknow_delivery_date_$seller_post_id' value=".CommonComponent::convertDateForDatabase($row->cells [5]->value)." name='buyersearch_booknow_delivery_date_$seller_post_id' type='hidden'>
			</div>
			</form>
			</div>
			<div class='clearfix'></div>
			<div class='col-md-12 padding-none '>						
                            <div class='pull-left'>
                                <div class='info-links'>
                                <a href='#'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>".count($msg_count['result'])."</span></a>
                                <a href='#'><i class='fa fa-file-text-o'></i> Quotes<span class='badge'></span></a>
                                <a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
                                <a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>
                                </div>
                            </div>
                            <div class='pull-right text-right'>
                                <div class='info-links'>
                                <a id='".$seller_post_id."' data-sellerlistid=$seller_post_id class='viewcount_show-data-link' data-quoteId='$seller_post_id' ><span class='show-icon spot_transaction_details_list'>+</span><span class='hide-icon'>-</span> Details</a>

                                </div>
                            </div>
                    
                    <div class='details-block-div clearfix show-data-div' style='display:none;' id='spot_transaction_details_view_$seller_post_id'>
                    <div class=''>";
		
			$row->cells [7]->value .="
			<div class='col-md-12 margin-top'>		
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Cancellation Charges</span>
			<span class='data-value'>$seller_post->cancellation_charge_price</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Docket Charges</span>
			<span class='data-value'>$seller_post->docket_charge_price</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Crating Charges</span>
			<span class='data-value'>$seller_post->crating_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Storage Charges</span>
			<span class='data-value'>$seller_post->storate_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Escort Charges</span>
			<span class='data-value'>$seller_post->escort_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Handyman Charges</span>
			<span class='data-value'>$seller_post->handyman_charges</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Property Search</span>
			<span class='data-value'>$seller_post->property_search</span>
			</div>
			<div class='col-md-2 padding-left-none data-fld'>
			<span class='data-head'>Brokerage</span>
			<span class='data-value'>$seller_post->brokerage</span>
			</div>					
			</div>";
			
			$row->cells [7]->value .="<div class='col-md-12  data-fld'>
			<span class='data-head'>Terms &amp; Conditions</span>
			<span class='data-value'>$seller_post->terms_conditions</span>
			</div>";
			
			
			
			$row->cells [7]->value .="<div class='col-md-12'>";
			
			if($householdItems > 0){
			$row->cells [7]->value .="<div class='table-div table-style1 margin-top'>
				<!-- Table Head Starts Here -->
				<div class='table-heading inner-block-bg'>
				<div class='col-md-2 padding-left-none'>Property Type</div>
				<div class='col-md-2 padding-left-none'>Volume</div>
				<div class='col-md-2 padding-left-none'>Load Type</div>
				<div class='col-md-2 padding-left-none'>O & D Charges (per CFT)</div>
				<div class='col-md-2 padding-left-none'>Transport Charges</div>
				<div class='col-md-2 padding-left-none'>Transit Days</div>
				</div>
				<div class='table-data'>";
				
			foreach($seller_post_items as $seller_post_edit_action_line){
			if($seller_post_edit_action_line->rate_card_type == 1){
			$row->cells [7]->value .="<div class='table-row inner-block-bg'>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getPropertyType($seller_post_edit_action_line->lkp_property_type_id)."</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->volume CFT</div>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getLoadCategoryById($seller_post_edit_action_line->lkp_load_category_id)." </div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->rate_per_cft /-</div>
			<div class='col-md-2 padding-none'>$seller_post_edit_action_line->transport_charges /-</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->transitdays $seller_post_edit_action_line->units</div>
			</div>";
			}
			}
			
			$row->cells [7]->value .="</div>";
			
			$row->cells [7]->value .="</div>";
		    }
			
			$row->cells [7]->value .="<div class='clearfix'></div>";
			if($vehicleItems > 0){
			$row->cells [7]->value .="<div class='table-style table-style1 margin-top margin-bottom'>
			<div class='table-heading inner-block-bg'>
			<div class='col-md-3 padding-left-none'>Vehicle Category</div>
			<div class='col-md-2 padding-left-none'>Car Type</div>
			<div class='col-md-2 padding-left-none'>Cost</div>
			<div class='col-md-2 padding-none'>Transport Charges</div>
			<div class='col-md-3 padding-left-none'>Transit Days</div>
			</div>
			
			<div class='table-data'>";
			foreach($seller_post_items as $seller_post_edit_action_line){
			if($seller_post_edit_action_line->rate_card_type == 2){
			$row->cells [7]->value .="<div class='table-row inner-block-bg'>
			<div class='col-md-3 padding-left-none'>".CommonComponent::getVehicleCategoryById($seller_post_edit_action_line->lkp_vehicle_category_id)."</div>
			<div class='col-md-2 padding-left-none'>".CommonComponent::getVehicleCategorytypeById($seller_post_edit_action_line->lkp_car_size)."</div>
			<div class='col-md-2 padding-left-none'>$seller_post_edit_action_line->cost /-
			<input name='vehicle_cost_$seller_post_edit_action_line->lkp_vehicle_category_id' id='vehicle_cost_$seller_post_edit_action_line->lkp_vehicle_category_id' value='$seller_post_edit_action_line->cost' type='hidden'/>
			</div>
			<div class='col-md-2 padding-none'>$seller_post_edit_action_line->transport_charges /-</div>
			<div class='col-md-3 padding-left-none'>$seller_post_edit_action_line->transitdays $seller_post_edit_action_line->units</div>
			</div>";
			}
			}
			
			$row->cells [7]->value .="</div>
			</div>";
			}
			$row->cells [7]->value .="</div>";
			
			$row->cells [7]->value .="
			</div>
	        </div>";
			
		});
		
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'rsp.from_location_id', '', 'select' )->options ( $from_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
			$filter->add ( 'rsp.to_location_id', '', 'select' )->options ( $to_locations )->attr ( "class", "selectpicker margin-bottom" )->attr ( "onchange", "this.form.submit()" );
			$filter->submit ( 'search' );
			$filter->reset ( 'reset' );
			$filter->build ();
			
			$result = array ();
			$result ['grid'] = $grid;
			$result ['filter'] = $filter;
			return $result;
		
	}

	/**
	* Getting buyer pet move post details & seller list details
	* @author Shriram
	*/
	public static function getBuyerPostDetails($buyer_post_id, $serviceId=null,$roleid=null,$comparisonType=null,$sellerIds=null) {
			
		$objPetmove = new \App\Models\RelocationPetBuyerPost();
		$buyer_post_details = $objPetmove->getPetmovePostDetails(Auth::user()->id, $buyer_post_id);
		
		$qryQuotePrices = DB::table ('relocationpet_buyer_quote_sellers_quotes_prices as rsqb' )
			->leftjoin('users as u', 'u.id', '=', 'rsqb.seller_id')
			->leftjoin('relocationpet_seller_posts as sp', 'sp.id', '=', 'rsqb.seller_post_id')
                        ->leftjoin('relocationpet_seller_post_items as spi', 'sp.id', '=', 'spi.seller_post_id')
                        ->where( 'rsqb.buyer_quote_id', $buyer_post_id)
                        ->where( 'rsqb.total_price', '!=', '0.00');
		
		if($comparisonType==1)
			$qryQuotePrices->orderBy('rsqb.transit_days');
		
		if($comparisonType==2)
			$qryQuotePrices->orderBy('rsqb.total_price');

		if(count($sellerIds)!=0):
			$sellerIds= explode(",",$sellerIds);
			$qryQuotePrices->whereIn( 'rsqb.seller_id', $sellerIds);			
		endif;

		$sellerResults = $qryQuotePrices->select ('rsqb.private_seller_quote_id','sp.from_date','spi.lkp_cage_type_id','sp.to_date','sp.transaction_id as transaction_no', 'rsqb.*', 'u.username','spi.id as seller_post_item_id')->get();
		
		$j=0;
		$k=1;
		$p=1;
		if($comparisonType != null){
			for ($i=0;$i<count($sellerResults);$i++) {
				if($i==0){
					$j=1;
				}
				if($j>count($sellerResults)-1){
					$j=count($sellerResults)-1;
				}
				if($comparisonType == '1'){
					if($sellerResults[$i]->transit_days !=$sellerResults[$j]->transit_days ){
							
						if($k<=3){
							$sellerResults[$i]->rank="L".$k;
						} else{
							$sellerResults[$i]->rank="-";
						}
						$k++;
					}else{
						if($k<=3){
							$sellerResults[$i]->rank="L".$k;
						} else{
							$sellerResults[$i]->rank="-";
						}
					}
				}
				if($comparisonType == '2'){
					if($sellerResults[$i]->total_price!=$sellerResults[$j]->total_price){
							
						if($p<=3){
							$sellerResults[$i]->rank="L".$p;
						} else{
							$sellerResults[$i]->rank="-";
						}
						$p++;
					}else{
						if($p<=3){
							$sellerResults[$i]->rank="L".$p;
						}else{
							$sellerResults[$i]->rank="-";
						}
					}
				}
				$j++;
					
					
					
			}
		}
		
		return [
			'postDetails' => $buyer_post_details,
			'sellerResults' => $sellerResults			
		];
	}	
	
	public static function getQuotesCount($buyer_post_id){
		
		$buyer_post_edit_seller = DB::table('relocation_buyer_quote_sellers_quotes_prices')
  		->where('relocation_buyer_quote_sellers_quotes_prices.buyer_quote_id', $buyer_post_id)
  		->select('relocation_buyer_quote_sellers_quotes_prices.*')
		->get();
		return count($buyer_post_edit_seller);
		
	}
	

	/**
	 * Buyer Orders Detail Page in Relocation Page
	 * Retrieval of data related to Buyer Orders
	 *
	 */
	public static function getRelocationBuyerOrderDetails($serviceId, $orderId, $user_id) {
		try {
			

			$order_type=DB::table('orders')
			->where('orders.id', $orderId)
			->select('orders.*')
			->get();
			
			$orders=array();
			$spot=1;$term=2;
			$query = DB::table('orders');			
			$query->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
			->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id')
			->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')			
			->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id');
			
			
			$serviceId = Session::get('service_id');
			 switch ($serviceId) {
			 case RELOCATION_DOMESTIC :
			 		$query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
			 		$query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');
			 		if($order_type[0]->lkp_order_type_id==1){
			 		$query->leftJoin('relocation_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');
			 		}
			 		else{
			 		$query->leftJoin('term_buyer_quote_items as tbqi', 'tbqi.id', '=', 'orders.buyer_quote_item_id');
			 		$query->leftJoin('term_buyer_quotes as tbq', 'tbq.id', '=', 'tbqi.term_buyer_quote_id');
			 		}				 	
             		$query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
             		$query->where('orders.buyer_id', '=', $user_id);
             		if($order_type[0]->lkp_order_type_id==1){
             		$orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid','rbq.lkp_post_ratecard_type_id','rbq.lkp_vehicle_category_id','rbq.lkp_load_category_id')->first();
             		}
             		else{
             		
             	    $orders['orderDetails'] = $query->select('tbq.transaction_id as postid','tbq.id as termbuyerid','tbq.lkp_post_ratecard_type as lkp_post_ratecard_type_id','tbqi.lkp_load_type_id as lkp_load_category_id','oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total')->first();
             		}
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}
	
        
        /**
	 * Buyer Orders Detail Page in Relocation pet Page
	 * Retrieval of data related to Buyer Orders
	 *
	 */
	public static function getRelocationPetBuyerOrderDetails($serviceId, $orderId, $user_id) {
		try {
			

			$order_type=DB::table('orders')
			->where('orders.id', $orderId)
			->select('orders.*')
			->get();
			
			$orders=array();
			$spot=1;$term=2;
			$query = DB::table('orders');			
			$query->leftJoin('order_payments as op', 'orders.order_payment_id', '=', 'op.id')
			->leftJoin('order_invoices as oi', 'oi.order_id', '=', 'orders.id')
			->leftjoin('lkp_payment_modes', 'lkp_payment_modes.id', '=', 'op.lkp_payment_mode_id')			
			->leftJoin('lkp_order_statuses as os', 'orders.lkp_order_status_id', '=', 'os.id');
			
			
			$serviceId = Session::get('service_id');
			 switch ($serviceId) {
			 case RELOCATION_PET_MOVE :
                                $query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
                                $query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');			 		
                                $query->leftJoin('relocationpet_buyer_posts as rbq', 'rbq.id', '=', 'orders.buyer_quote_id');  
                                $query->leftJoin('lkp_pet_types as lkpt', 'lkpt.id', '=', 'rbq.lkp_pet_type_id');                                
                                $query->leftJoin('lkp_cage_types as lkct', 'lkpt.id', '=', 'rbq.lkp_cage_type_id'); 
                                $query->leftJoin('lkp_breed_types as lkbt', 'lkbt.id', '=', 'rbq.lkp_breed_type_id'); 
                                $query->leftjoin('users as u', 'u.id', '=', 'orders.seller_id')->where('orders.id', '=', $orderId);             		
                                $query->where('orders.buyer_id', '=', $user_id);             		
                                $orders['orderDetails'] = $query->select('oi.invoice_no as invoice','oi.service_tax_amount as inv_service_tax','orders.*','orders.dispatch_date as orderdispatchdate','orders.delivery_date as orderdeliverydate','orders.id as orderid', 'orders.price as orderprice', 'u.username', 'op.*', 
                                        'os.order_status', 'lc.city_name as from_city', 'lcity.city_name as to_city', 'lkp_payment_modes.payment_mode', 'oi.total_amt as inv_total','rbq.transaction_id as postid', 'lkpt.pet_type', 'lkct.cage_type', 'lkbt.breed_type')->first();
             		
		            break;
					}
			return $orders;
			 
		} catch ( Exception $exc ) {
			
		}
		
	}
}