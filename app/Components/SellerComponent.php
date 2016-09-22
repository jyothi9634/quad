<?php
namespace App\Components;
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
use App\Models\FtlSearchTerm;

class SellerComponent {
	
	/**
	* Seller Posts List Page
	* Retrieval of data related to seller posts list items to populate in the seller list widget
	* Displays a grid with a list of all seller posts
	*/	
	public static function getSellerPostsList($statusId, $roleId, $serviceId, $id) {

		//Filters values to populate in the page
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$load_types = array(""=>"Load Type");
		$Query = DB::table ( 'seller_posts as sp' );
		$Query->join ( 'seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->join ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		$Query->where('sp.seller_id',Auth::user()->id);
		$Query->where('spi.seller_post_id',$id);
		
		if(Session::get('leads') &&  Session::get('leads')==2){
			Session::put('leads', '2');
				
		
			$Query->leftJoin('buyer_quote_items as bqi', function($join)
			{
				$join->on('bqi.from_city_id', '=', 'spi.from_location_id');
				$join->on('bqi.to_city_id', '!=', 'spi.to_location_id');
					
			});
				
				
			$Query->where('sp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			
			
			
			$Query->leftJoin('buyer_quote_items as bqi', function($join)
			{
				$join->on('bqi.from_city_id', '=', 'spi.from_location_id');
				$join->on('bqi.to_city_id', '=', 'spi.to_location_id');
					
			});
			$Query->leftjoin ( 'buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
				
		}
		
		
		
			
		
		//conditions to make search
		if(isset($statusId) && $statusId != ''){
			$Query->where('sp.lkp_post_status_id', $statusId);
		}
		if(isset($serviceId) && $serviceId != ''){
			$Query->where('sp.lkp_service_id', $serviceId);
		}

		$sellerresults = $Query->select ( 'spi.id', 'sp.from_date','spi.price',
				'sp.to_date', 'sp.transaction_id' ,'spi.lkp_vehicle_type_id','spi.lkp_load_type_id','spi.price',
				'sp.lkp_access_id', 'ps.post_status','sp.id as post_id','spi.from_location_id','spi.to_location_id'
		)
		->groupBy('spi.id')
		->get ();

		
				
		
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('seller_post_items')
				->where('seller_post_items.id',$seller->id)
				->select('*')
				->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				if(!isset($to_locations[$seller_post_item->to_location_id])){
					$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				}
				if(!isset($vehicle_types[$seller_post_item->lkp_load_type_id])){
					$vehicle_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
				}
				if(!isset($load_types[$seller_post_item->lkp_vehicle_type_id])){
					$load_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
			}
		}
		//Functionality to handle filters based on the selection ends

		$grid = DataGrid::source ( $Query );

		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_location_id', 'From', 'from_location_id' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
		$grid->add ( 'to_location_id', 'To', 'to_location_id' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
		$grid->add ( 'lkp_vehicle_type_id', 'Vehicle Type', 'lkp_vehicle_type_id' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none hidden-xs"));
		$grid->add ( 'lkp_load_type_id', 'Load Type', 'lkp_load_type_id' )->attributes(array("class" => "col-md-3 col-sm-3 col-xs-5 padding-none hidden-xs"));
		$grid->add ( 'price', 'Price', 'price' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none"));
		$grid->add ( 'post_status', 'Status', '' )->attributes(array("class" => "col-md-1 col-sm-1 col-xs-4 padding-none text-right pull-right hidden-xs"));
		$grid->add ( 'below_grid', 'Below Grid', true )->style ( "display:none" );
		
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );


		$grid->row ( function ($row) {	

		
					
			
			$row->cells [0]->style ( 'display:none' );	
			$spId = $row->cells [0]->value;
			$row->cells[0]->value = '<a href=/sellerpostdetail/'.$spId.'>';
			$row->cells [5]->style ( 'text-align:right' );	
			$row->cells [5]->value = CommonComponent::getPriceType($row->cells [5]->value);
			$row->cells [6]->style ( 'width:100%' );
			//$pId = $row->cells [7]->value;
			/*if($row->cells [4]->value == 1)
				$row->cells [4]->value = 'Public';
			else 
				$row->cells [4]->value = 'Private';*/
			
			//View Count
			$countview = DB::table('seller_post_item_views')
			->where('seller_post_item_views.created_by','=',Auth::user()->id)
			->where('seller_post_item_views.seller_post_item_id','=',$spId)
			->select('seller_post_item_views.id','seller_post_item_views.view_counts')
			->get();
			if(!isset($countview[0]->view_counts))
				$countview = 0;
			else
				$countview = $countview[0]->view_counts;
			
			$row->cells [1]->value = ''.CommonComponent::getCityName($row->cells [1]->value).'';
			$row->cells [2]->value = ''.CommonComponent::getCityName($row->cells [2]->value).'';
			$row->cells [3]->value = ''.CommonComponent::getVehicleType($row->cells [3]->value).'';
			$row->cells [4]->value = ''.CommonComponent::getLoadType($row->cells [4]->value).'';
			$seller_post_items  = DB::table('seller_post_items')
				->where('seller_post_items.id',$spId)
				->select('*')
				->get();
			

			$row->cells [1]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
			$row->cells [2]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none hidden-xs"));
			$row->cells [4]->attributes(array("class" => "col-md-3 col-sm-3 col-xs-5 padding-none hidden-xs"));
			$row->cells [5]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none"));
			$row->cells [6]->attributes(array("class" => "col-md-1 col-sm-1 col-xs-4 padding-none text-right pull-right hidden-xs"));
			$row->cells [7]->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 pull-right padding-none below_each_row_grid"));

			
			
			if(Session::get('leads')==2){
				
				/*$buyerpublicquotedetails    = DB::table('buyer_quotes')
				->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
				//->join('buyer_quote_selected_sellers','buyer_quote_selected_sellers.buyer_quote_id','=','buyer_quotes.id')
				->join('users','users.id','=','buyer_quotes.created_by')
				->join('lkp_load_types','lkp_load_types.id','=','buyer_quote_items.lkp_load_type_id')
				->join('lkp_cities','lkp_cities.id','=','buyer_quote_items.from_city_id')
				->join('lkp_vehicle_types','lkp_vehicle_types.id','=','buyer_quote_items.lkp_vehicle_type_id')
				->leftjoin('buyer_quote_sellers_quotes_prices','buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','buyer_quote_items.id')
				->where('buyer_quote_items.from_city_id',$seller_post_items[0]->from_location_id)
				//->where('buyer_quote_items.to_city_id',$seller_post_items[0]->to_location_id)
				->where('buyer_quotes.lkp_quote_access_id',1)
				->select('buyer_quote_items.id')
				->groupBy('buyer_quote_items.id')
				->get();*/
				
				$buyerpublicquotedetails    = DB::table('buyer_quotes')
				->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
				->join('users','users.id','=','buyer_quotes.created_by')
				->join('lkp_load_types','lkp_load_types.id','=','buyer_quote_items.lkp_load_type_id')
				->join('lkp_cities','lkp_cities.id','=','buyer_quote_items.from_city_id')
				->join('lkp_vehicle_types','lkp_vehicle_types.id','=','buyer_quote_items.lkp_vehicle_type_id')
				->where('buyer_quote_items.from_city_id',$seller_post_items[0]->from_location_id)
				->where('buyer_quote_items.to_city_id','!=',$seller_post_items[0]->to_location_id)
				->where('buyer_quotes.lkp_quote_access_id',1)
				->select('buyer_quote_items.id','buyer_quote_items.created_by as buyer_id','users.username','buyer_quote_items.dispatch_date',
						'buyer_quote_items.delivery_date','lkp_load_types.load_type',
						'lkp_vehicle_types.vehicle_type','buyer_quote_items.lkp_quote_price_type_id',
						'buyer_quote_items.from_city_id','buyer_quote_items.to_city_id',
						'lkp_cities.city_name',
						'buyer_quote_items.price'
				)
				->groupBy('buyer_quote_items.id')
				->get();
				
				
				$total_count = count($buyerpublicquotedetails);
				
			}else{
				
			
				$buyerdetails   = DB::table('buyer_quotes')
							->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
							->join('buyer_quote_selected_sellers','buyer_quote_selected_sellers.buyer_quote_id','=','buyer_quotes.id')
							->join('users','users.id','=','buyer_quotes.created_by')
							->join('lkp_load_types','lkp_load_types.id','=','buyer_quote_items.lkp_load_type_id')
							->join('lkp_cities','lkp_cities.id','=','buyer_quote_items.from_city_id')
							->join('lkp_vehicle_types','lkp_vehicle_types.id','=','buyer_quote_items.lkp_vehicle_type_id')
							->join('buyer_quote_sellers_quotes_prices','buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','buyer_quote_items.id')
							->where('buyer_quote_selected_sellers.seller_id',Auth::user()->id)
							->where('buyer_quotes.lkp_quote_access_id',2)
							->where('buyer_quote_items.from_city_id',$seller_post_items[0]->from_location_id)
							->where('buyer_quote_items.to_city_id',$seller_post_items[0]->to_location_id)
							->select('buyer_quotes.id')
							->groupBy('buyer_quote_items.id')
							->get();
			
				$buyerpublicquotedetails    = DB::table('buyer_quotes')
										->join('buyer_quote_items','buyer_quote_items.buyer_quote_id','=','buyer_quotes.id')
										//->join('buyer_quote_selected_sellers','buyer_quote_selected_sellers.buyer_quote_id','=','buyer_quotes.id')
										->join('users','users.id','=','buyer_quotes.created_by')
										->join('lkp_load_types','lkp_load_types.id','=','buyer_quote_items.lkp_load_type_id')
										->join('lkp_cities','lkp_cities.id','=','buyer_quote_items.from_city_id')
										->join('lkp_vehicle_types','lkp_vehicle_types.id','=','buyer_quote_items.lkp_vehicle_type_id')
										->leftjoin('buyer_quote_sellers_quotes_prices','buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=','buyer_quote_items.id')
										->where('buyer_quote_items.from_city_id',$seller_post_items[0]->from_location_id)
										->where('buyer_quote_items.to_city_id',$seller_post_items[0]->to_location_id)
										->where('buyer_quotes.lkp_quote_access_id',1)
										->select('buyer_quote_items.id')
										->groupBy('buyer_quote_items.id')
										->get();
				$total_count = count($buyerdetails)+count($buyerpublicquotedetails);
			}
			
			
			
			$row->cells [7]->value = '<div class="clearfix"></div>
									<div class="col-md-2 col-sm-2 col-xs-2 padding-none margin-top text-center">
										<a href="#">
											<div class="margin-center">
												<i class="fa fa-envelope"></i> 
												<span class="red superscript-table">0</span>
											</div>
											Messages
										</a>
									</div>
									<div class="col-md-2 col-sm-2 col-xs-3 padding-none margin-top text-center test">
										<a href="/sellerpostdetail/'.$spId.'">
											<div class="margin-center">
												<i class="fa fa-file-text-o"></i> 
												<span class="red superscript-table">'.$total_count.'</span>
											</div>';
											//Enquiries

											if(Session::get('leads') &&  Session::get('leads')==2)
												$row->cells [7]->value .= 'Leads';
											else
												$row->cells [7]->value .= 'Enquiries';
										
										$row->cells [7]->value .= '</a>
									</div>
									
									<div class="col-md-3 col-sm-3 col-xs-4 padding-none margin-top text-center">
										<a href="#">
											<div class="margin-center">
												<i class="fa fa-bar-chart-o"></i> 
												<span class="red superscript-table">0</span>
											</div>
											Market Analytics
										</a>
									</div>
									<div class="col-md-1 col-sm-1 col-xs-3 padding-none margin-top text-center pull-right">
										
										<div class="margin-center">
											<i class="fa fa-eye" title="Views"></i>
											<span class="red superscript-table">';
											$row->cells [7]->value .= $countview.'</span>
										</div>
										Views
										
									</div>			
									
									<!--div class="col-md-1 col-sm-1 col-xs-5 padding-right-none margin-top text-right pull-right underline_link">

										<a href="/updateseller/">edit12</a>
									</div-->

								';		


		} );

    //Functionality to build filters in the page starts
    $filter = DataFilter::source ( $Query );
    $filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");

    $filter->add ( 'sp.from_date', 'From', 'date' )->attr("class","filter_calendar");
    $filter->add ( 'sp.to_date', 'To', 'date' )->attr("class","filter_calendar");

    $filter->submit('search');
    $filter->reset('reset');
    $filter->build();
	//Functionality to build filters in the page ends

    $result = array();
    $result['grid'] = $grid;
    $result['filter'] = $filter;
    return $result;
}


	/**
	 * Seller Posts List Page
	 * Retrieval of data related to seller posts list items to populate in the seller list widget
	 * Displays a grid with a list of all seller posts
	 */
	public static function getSellerList($statusId, $serviceId, $roleId, $serviceId) {

		//Filters values to populate in the page
		$from_locations = array(""=>"From Location");
		$to_locations = array(""=>"To Location");
		$vehicle_types = array(""=>"Vehicle Type");
		$load_types = array(""=>"Load Type");
	
		// query to retrieve seller posts list and bind it to the grid
		$Query = DB::table ( 'seller_posts as sp' );
		$Query->leftjoin ( 'seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id' );
		$Query->leftjoin ( 'lkp_post_statuses as ps', 'ps.id', '=', 'sp.lkp_post_status_id' );
		
		if(isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] ==2)){
			Session::put('leads', '2');
			
			$Query->where('sp.lkp_access_id',1);
		}
		else{
			Session::put('leads', '1');
			
			$Query->leftjoin ( 'buyer_quote_selected_sellers as bqss', 'bqss.seller_id', '=', 'spi.created_by' );
			
		}
		
		$Query->where('sp.seller_id',Auth::user()->id);

		//conditions to make search
		if(isset($statusId) && $statusId != ''){
			$Query->where('sp.lkp_post_status_id', $statusId);
		}
		if(isset($serviceId) && $serviceId != ''){
			$Query->where('sp.lkp_service_id', $serviceId);
		}
	
		$sellerresults = $Query->select ( 'sp.id', 'sp.from_date',
				'sp.to_date','sp.lkp_access_id'
		)
		->groupBy('sp.id')
		->get ();
		//Functionality to handle filters based on the selection starts
		foreach($sellerresults as $seller){
			$seller_post_items  = DB::table('seller_post_items')
			->where('seller_post_items.seller_post_id',$seller->id)
			->select('*')
			->get();
			foreach($seller_post_items as $seller_post_item){
				if(!isset($from_locations[$seller_post_item->from_location_id])){
					$from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
				}
				if(!isset($to_locations[$seller_post_item->to_location_id])){
					$to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
				}
				if(!isset($load_types[$seller_post_item->lkp_load_type_id])){
					$load_types[$seller_post_item->lkp_load_type_id] = DB::table('lkp_load_types')->where('id', $seller_post_item->lkp_load_type_id)->pluck('load_type');
				}
				if(!isset($vehicle_types[$seller_post_item->lkp_vehicle_type_id])){
					$vehicle_types[$seller_post_item->lkp_vehicle_type_id] = DB::table('lkp_vehicle_types')->where('id', $seller_post_item->lkp_vehicle_type_id)->pluck('vehicle_type');
				}
			}
		}
		//Functionality to handle filters based on the selection ends
	
		$grid = DataGrid::source ( $Query );
	
		$grid->add ( 'id', 'ID', true )->style ( "display:none" );
		$grid->add ( 'from_date', 'Valid From', 'from_date' )->attributes(array("class" => "col-md-4 col-sm-4 col-xs-4 padding-none"));
		//$grid->add ( 'tracking', '', '' )->style ( 'text-align:right' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
		$grid->add ( 'to_date', 'Valid To', 'to_date' )->attributes(array("class" => "col-md-4 col-sm-4 col-xs-4 padding-none"));
		//$grid->add ( 'created_at', '', '' )->style ( 'text-align:right' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
		$grid->add ( 'lkp_access_id', 'Post Visibility', '' )->style ( 'text-align:left' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none"));
		//$grid->add ( 'updated_at', '', '' )->style ( "display:none" );
		$grid->edit ( 'status', 'Status', '' )->style ( 'text-align:right' )->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none text-right"));
	
		$grid->orderBy ( 'id', 'desc' );
		$grid->paginate ( 5 );
	
	
		$grid->row ( function ($row) {
			$row->cells [0]->style ( 'display:none' );
			$spId = $row->cells [0]->value;
			
			$row->cells [3]->value = CommonComponent::getQuoteAccessById($row->cells [3]->value);

			$row->cells [3]->style ( 'text-align:left' );
			//$row->cells [4]->style ( 'text-align:left' );
			$row->cells [4]->style ( 'text-align:right' );
			$row->cells [4]->value = "<a href='sellerposts/$spId'class='red  underline_link'><u>Details +</u></a>";
			$frmdate = $row->cells [1]->value;
			$frmdate = date('d/m/Y', strtotime($frmdate));
			$row->cells [1]->value = $frmdate;
			
			$todate = $row->cells [2]->value;
			$todate = date('d/m/Y', strtotime($todate));
			$row->cells [2]->value = $todate;
			$row->cells [1]->attributes(array("class" => "col-md-4 col-sm-4 col-xs-4 padding-none"));
			//$row->cells [2]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
			$row->cells [2]->attributes(array("class" => "col-md-4 col-sm-4 col-xs-4 padding-none"));
			//$row->cells [4]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-5 padding-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 padding-none text-right"));

			$data_link = url()."/sellerposts/$spId";
			$row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row html_link mobile-padding-none","data_link"=>$data_link));
			
		} );
			//Functionality to build filters in the page starts
			$filter = DataFilter::source ( $Query );
			$filter->add ( 'spi.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_vehicle_type_id', 'Vehicle Type', 'select')->options($vehicle_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
			$filter->add ( 'spi.lkp_load_type_id', 'Load Type', 'select')->options($load_types)->attr("class","selectpicker")->attr("onchange","this.form.submit()");
	
			$filter->add ( 'sp.from_date', 'From', 'date' )->attr("class","filter_calendar");
			$filter->add ( 'sp.to_date', 'To', 'date' )->attr("class","filter_calendar");
	
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends
	
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	
	
	
	
	/**
	 * Buyer Orders List Page
	 * Retrieval of data related to Buyer Orders list items to populate in the Orders list widget
	 * Displays a grid with a list of all Buyer Orders
	 */
	public static function getBuyerOrdersList($post, $data) {
	
		//Filters values to populate in the page
		$from_locations = array("" => "From Location");
		$to_locations = array("" => "To Location");
		$buyers = array("" => "Seller");
		$consignee = array("" => "Consignee");
	
		// query to retrieve seller posts list and bind it to the grid
		$query = DB::table('orders');
		$query->leftJoin('order_payments as op', 'op.id', '=', 'orders.order_payment_id');
		$query->leftJoin('order_invoices as oi', 'oi.id', '=', 'orders.order_invoice_id');
		$query->leftJoin('lkp_cities as lc', 'lc.id', '=', 'orders.from_city_id');
		$query->leftJoin('lkp_cities as lcity', 'lcity.id', '=', 'orders.to_city_id');
		$query->leftJoin('lkp_services as ls', 'ls.id', '=', 'orders.lkp_service_id');
		$query->leftJoin('lkp_order_statuses as os', 'os.id', '=', 'orders.lkp_order_status_id');
		$query->where('orders.buyer_id', '=', Auth::user()->id);
	
		if (isset($post['lkp_order_type_id']) && $post['lkp_order_type_id'] != '') {
			$order_type = $post['lkp_order_type_id'];
		}
		if (isset($post['service_id']) && $post['service_id'] != '') {
			$service_id = $post['service_id'];
		}
		if (isset($post['status_id']) && $post['status_id'] != '') {
			$order_status = $post['status_id'];
		}
	
		//conditions to make search
		if (isset($post['lkp_order_type_id']) && $data['lkp_order_type_id'] != '') {
			$query->where('orders.lkp_order_type_id', $order_type);
		}
		if (isset($post['service_id']) && $data['service_id'] != '') {
			$query->where('orders.lkp_service_id', $service_id);
		}
		if (isset($post['status_id']) && $data['status_id'] != '') {
			$query->where('orders.lkp_order_status_id', $order_status);
		}
        
        if (isset ( $post ['start_dispatch_date'] ) && $post ['start_dispatch_date'] != '') {
			$query->where ( 'orders.dispatch_date', '>=', $post ['start_dispatch_date'] );
			$from_date = $_GET ['start_dispatch_date'];
		}
		if (isset ( $post ['end_dispatch_date'] ) && $post ['end_dispatch_date'] != '') {
			$query->where ( 'orders.dispatch_date', '<=', $post ['end_dispatch_date'] );
			$to_date = $_GET ['end_dispatch_date'];
		}
	
		$orderresults = $query->select('orders.*', 'os.order_status as order_status', 'oi.invoice_no as invoice_no', 'lc.city_name as from_city', 'lcity.city_name as to_city')
		->get();
	//print_r($orderresults);exit;
	
		//Functionality to handle filters based on the selection starts
		foreach ($orderresults as $order) {
			$order_items = DB::table('orders')
			->where('orders.id', $order->id)
			->select('*')
			->get();
			foreach ($order_items as $order_item) {
				if (!isset($from_locations[$order_item->from_city_id])) {
					$from_locations[$order_item->from_city_id] = DB::table('lkp_cities')->where('id', $order_item->from_city_id)->pluck('city_name');
				}
				if (!isset($to_locations[$order_item->to_city_id])) {
					$to_locations[$order_item->to_city_id] = DB::table('lkp_cities')->where('id', $order_item->to_city_id)->pluck('city_name');
				}
				if (!isset($buyers[$order_item->buyer_consignor_name])) {
					$buyers[$order_item->buyer_consignor_name] = $order_item->buyer_consignor_name;
				}
				if (!isset($consignee[$order_item->buyer_consignee_name])) {
					$consignee[$order_item->buyer_consignee_name] = $order_item->buyer_consignee_name;
				}
			}
		}
		//Functionality to handle filters based on the selection ends
	
		$grid = DataGrid::source($query);
	
		$grid->attributes(array("class" => "table table-striped"));
	
		$grid->add('id', 'ID', false)->style('display:none');
		$grid->add('buyer_consignor_name', 'Name', 'buyer_consignor_name')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
		$grid->add('from_city', 'From', 'from_city')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
		$grid->add('to_city', 'To', 'to_city')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
		$grid->add('dispatch_date', 'Dispatch date', 'dispatch_date')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 hidden-xs padding-none"));
		$grid->add('buyer_consignee_name', 'Consignee', 'buyer_consignee_name')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 hidden-xs padding-none"));
		$grid->add('order_no|strip_tags', 'Order No', 'order_no')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-2 hidden-xs padding-none"));
		$grid->add('invoice_no', 'Invoice No', 'invoice_no')->attributes(array("class" => "col-md-2 col-sm-2 col-xs-1 padding-none hidden-xs hidden-sm hidden-md hidden-lg"));
		//$grid->add('order_status', 'Status', 'order_status')->attributes(array("class" => "col-md-1 col-sm-2 col-xs-12 padding-none"));
		$grid->add('a', '', '');
		$grid->orderBy('id', 'desc');
		$grid->paginate(5);
	
	
	
		$grid->row(function ($row) {
                        $order_id = $row->cells [0]->value;
                        $row->cells[0]->value = '<a href=/orders/buyer_orderdetails/'.$order_id.'>';
			$row->cells [0]->style('display:none');

			$row->cells [1]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
			$row->cells [2]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
			$row->cells [3]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 padding-none"));
			$row->cells [4]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 hidden-xs padding-none"));
			$row->cells [5]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-4 hidden-xs padding-none"));
			$row->cells [6]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-1 hidden-xs padding-none"));
			$row->cells [7]->attributes(array("class" => "col-md-2 col-sm-2 col-xs-12 hidden-xs padding-none table-details hidden-xs hidden-sm hidden-md hidden-lg"));
			//$row->cells [8]->attributes(array("class" => "col-md-1 col-sm-2 col-xs-12 padding-none table-details"));
			$row->cells [8]->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none"));


                        
			//$row->cells [9]->style('width:100%');
			$status = $row->cells [8]->value;
			$consignor = $row->cells [1]->value;
			$row->cells [1]->value = ''.$consignor.'';
                        $row->cells[2]->value = ''.$row->cells[2]->value.'';
			$row->cells[3]->value = ''.$row->cells[3]->value.'';
			$row->cells [4]->value = ''.CommonComponent::convertDateForDatabase($row->cells [4]->value).'';
                        $row->cells[5]->value = ''.$row->cells[5]->value.'';	
			$row->cells[6]->value = ''.$row->cells[6]->value.'';
			$row->cells[7]->value = ''.$row->cells[7]->value.'</a>';
			/*$row->cells[8]->value = '<div class="profile-bar">
                                    <div class="profile-complete"></div>
                                </div>' . $status . '
                                <br><a href="#">Message</a>';*/
	
			$row->cells [8]->value = '<div
                                    class="col-md-2 col-sm-2 col-xs-2 padding-none margin-top text-center">
                                    <a href="#">
                                        <div class="margin-center">
                                            <i class="fa fa-envelope"></i> <span
                                                class="red superscript-table">0</span>
                                        </div> Messages
                                    </a>
                                </div>
                                <div
                                    class="col-md-2 col-sm-2 col-xs-2 padding-none margin-top text-center">
                                    <a href="#">
                                        <div class="margin-center">
                                            <i class="fa fa-file-text-o"></i> <span
                                                class="red superscript-table">0</span>
                                        </div> Status
                                    </a>
                                </div>
                                <div
                                    class="col-md-2 col-sm-2 col-xs-3 padding-none margin-top text-center">
                                    <a href="#">
                                        <div class="margin-center">
                                            <i class="fa fa-file-text-o"></i> <span
                                                class="red superscript-table">0</span>
                                        </div> Documents
                                    </a>
                                </div>
                                <div
                                    class="col-md-3 col-sm-2 col-xs-3 padding-none margin-top pull-right">
                                    <div class="profile-bar margin-top">
                                    	<div class="profile-complete"></div>
                                	</div>' . $status . '
                                	
                                	<a href="#">Message</a></div>';
                        
                        $row->attributes(array("class" => "col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row"));
			
		});
	
			//Functionality to build filters in the page starts
			$filter = DataFilter::source($query);
			$filter->add('orders.from_city_id', 'From Location', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->add('orders.to_city_id', 'To Location', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->add('orders.buyer_consignor_name', 'Seller', 'select')->options($buyers)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->add('orders.buyer_consignee_name', 'Consignee', 'select')->options($consignee)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
			$filter->add('orders.order_no', 'Order No', 'text')->attr("class", "top-text-fld")->attr("onchange", "this.form.submit()");
	
			$filter->add('orders.start_dispatch_date', 'From', 'date')->attr("class", "datetimepicker dateRange dateRangeFrom");
			$filter->add('orders.end_dispatch_date', 'To', 'date')->attr("class", "datetimepicker dateRange dateRangeTo");
	
			$filter->submit('search');
			$filter->reset('reset');
			$filter->build();
			//Functionality to build filters in the page ends
	
			$result = array();
			$result['grid'] = $grid;
			$result['filter'] = $filter;
			return $result;
	}
	
        public static function searchTermsSendMail() {
            $users = DB::table('users')->where('id', ADMIN)->get();
            $user = User::where('id', Auth::user()->id)->first();
            //print_r($user);exit;
            //$users->email = 'swathi.pakala@quadone.com';
            if(isset($_REQUEST['from_city_id']) && $_REQUEST['from_city_id']!=""){
            $from = DB::table('lkp_cities')
                    ->where('lkp_cities.id', '=', $_REQUEST['from_city_id'])
                    ->select('lkp_cities.city_name')->first();
            $users[0]->from = $from->city_name;
            }else{
            	$users[0]->from ="";
            }
            if(isset($_REQUEST['to_city_id']) && $_REQUEST['to_city_id']!=""){
            $to = DB::table('lkp_cities')
                    ->where('lkp_cities.id', '=', $_REQUEST['to_city_id'])
                    ->select('lkp_cities.city_name')->first();
            $users[0]->to = $to->city_name;
            }else{
            	$users[0]->to ="";
            }
            if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id']!=""){
            $load_type = DB::table('lkp_load_types')
                    ->where('lkp_load_types.id', '=', $_REQUEST['lkp_load_type_id'])
                    ->select('lkp_load_types.load_type')->first();
            $users[0]->load_type = $load_type->load_type;
            }else{
            	$users[0]->load_type ="";
            }
            if(isset($_REQUEST['lkp_vehicle_type_id']) && $_REQUEST['lkp_vehicle_type_id']!=""){
            $vehicle_type = DB::table('lkp_vehicle_types')
                    ->where('lkp_vehicle_types.id', '=', $_REQUEST['lkp_vehicle_type_id'])
                    ->select('lkp_vehicle_types.vehicle_type')->first();
            $users[0]->vehicle_type = $vehicle_type->vehicle_type;
            }else{
            	$users[0]->vehicle_type ="";
            }
           //print_r($vehicle_type['vehicle_type']);exit;
            if(isset($_REQUEST['dispatch_date']) && $_REQUEST['dispatch_date']!=""){
            $users[0]->dispatch = $_REQUEST['dispatch_date'];
            }
            $users[0]->user = $user['username'];
            
            CommonComponent::send_email(FTL_SEARCH_KEYWORDS, $users);
            
        }
	public static function sellerPostDetails($from_city_id,$to_city_id,$buyer_quote_id){
            $data=DB::table('seller_post_items as spi')
                ->join( 'seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
                ->join( 'buyer_quote_sellers_quotes_prices', 'buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
                ->where('spi.from_location_id','=',$from_city_id)
                ->where('spi.to_location_id','=',$to_city_id)
                ->where('buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyer_quote_id)
                ->where('spi.created_by','=',Auth::user()->id)
                ->where('sp.lkp_post_status_id','=',OPEN)
                ->select('spi.seller_post_id','spi.id','sp.tracking','sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking','sp.accept_payment_credit',
                                'sp.accept_payment_debit','sp.credit_period',
                                'sp.credit_period_units','sp.accept_credit_netbanking','sp.accept_credit_cheque')
                                ->get();
            return $data;
        }
        public static function ptlSellerPostDetails($from_zipcode,$to_zipcode,$bqid){
            $serviceID  =   Session::get('service_id');
            switch($serviceID){
                case ROAD_PTL:
                    $data   =   DB::table('ptl_seller_post_items')
			->join( 'ptl_seller_posts', 'ptl_seller_posts.id', '=', 'ptl_seller_post_items.seller_post_id' )
			->join( 'ptl_buyer_quote_sellers_quotes_prices', 'ptl_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'ptl_seller_post_items.id' )
			->where('ptl_seller_post_items.from_location_id','=',$from_zipcode)
			->where('ptl_seller_post_items.to_location_id','=',$to_zipcode)
			->where('ptl_seller_post_items.created_by','=',Auth::user()->id)
			->where('ptl_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('ptl_seller_posts.lkp_post_status_id','=',OPEN)
			->select('ptl_seller_post_items.seller_post_id',
					'ptl_seller_post_items.id',
					'ptl_seller_posts.tracking',
					'ptl_seller_posts.lkp_payment_mode_id',
					'ptl_seller_posts.accept_payment_netbanking',
					'ptl_seller_posts.accept_payment_credit',
					'ptl_seller_posts.accept_payment_debit',
					'ptl_seller_posts.credit_period',
					'ptl_seller_posts.credit_period_units',
					'ptl_seller_posts.accept_credit_netbanking',
					'ptl_seller_posts.accept_credit_cheque')
					->get();
                break;
                case RAIL:
                    $data   =   DB::table('rail_seller_post_items as spi')
			->join( 'rail_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
			->join( 'rail_buyer_quote_sellers_quotes_prices', 'rail_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
			->where('spi.from_location_id','=',$from_zipcode)
			->where('spi.to_location_id','=',$to_zipcode)
			->where('spi.created_by','=',Auth::user()->id)
			->where('rail_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('sp.lkp_post_status_id','=',OPEN)
			->select('spi.seller_post_id', 'spi.id', 'sp.tracking', 'sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking', 'sp.accept_payment_credit',
                                'sp.accept_payment_debit', 'sp.credit_period','sp.credit_period_units', 
                                'sp.accept_credit_netbanking', 'sp.accept_credit_cheque')
					->get();
                break;
                case AIR_DOMESTIC:
                    $data   =   DB::table('airdom_seller_post_items as spi')
			->join( 'airdom_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
			->join( 'airdom_buyer_quote_sellers_quotes_prices', 'airdom_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
			->where('spi.from_location_id','=',$from_zipcode)
			->where('spi.to_location_id','=',$to_zipcode)
			->where('spi.created_by','=',Auth::user()->id)
			->where('airdom_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('sp.lkp_post_status_id','=',OPEN)
			->select('spi.seller_post_id', 'spi.id', 'sp.tracking', 'sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking', 'sp.accept_payment_credit',
                                'sp.accept_payment_debit', 'sp.credit_period','sp.credit_period_units', 
                                'sp.accept_credit_netbanking', 'sp.accept_credit_cheque')
					->get();
                break;
                case AIR_INTERNATIONAL:
                    $data   =   DB::table('airint_seller_post_items as spi')
			->join( 'airint_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
			->join( 'airint_buyer_quote_sellers_quotes_prices', 'airint_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
			->where('spi.from_location_id','=',$from_zipcode)
			->where('spi.to_location_id','=',$to_zipcode)
			->where('spi.created_by','=',Auth::user()->id)
			->where('airint_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('sp.lkp_post_status_id','=',OPEN)
			->select('spi.seller_post_id', 'spi.id', 'sp.tracking', 'sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking', 'sp.accept_payment_credit',
                                'sp.accept_payment_debit', 'sp.credit_period','sp.credit_period_units', 
                                'sp.accept_credit_netbanking', 'sp.accept_credit_cheque')
					->get();
                break;
                case OCEAN:
                    $data   =   DB::table('ocean_seller_post_items as spi')
			->join( 'ocean_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
			->join( 'ocean_buyer_quote_sellers_quotes_prices', 'ocean_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
			->where('spi.from_location_id','=',$from_zipcode)
			->where('spi.to_location_id','=',$to_zipcode)
			->where('spi.created_by','=',Auth::user()->id)
			->where('ocean_buyer_quote_sellers_quotes_prices.buyer_quote_id','=',$bqid)
			->where('sp.lkp_post_status_id','=',OPEN)
			->select('spi.seller_post_id', 'spi.id', 'sp.tracking', 'sp.lkp_payment_mode_id',
                                'sp.accept_payment_netbanking', 'sp.accept_payment_credit',
                                'sp.accept_payment_debit', 'sp.credit_period','sp.credit_period_units', 
                                'sp.accept_credit_netbanking', 'sp.accept_credit_cheque')
					->get();
                break;
            }
            return $data;
        }
        
        public static function truckleaseSellerPostDetails($from_city_id,$buyer_quote_id){
        	$data=DB::table('trucklease_seller_post_items as spi')
        	->join( 'trucklease_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )
        	->join( 'trucklease_buyer_quote_sellers_quotes_prices', 'trucklease_buyer_quote_sellers_quotes_prices.seller_post_item_id', '=', 'spi.id' )
        	->where('spi.from_location_id','=',$from_city_id)
        	->where('trucklease_buyer_quote_sellers_quotes_prices.buyer_quote_item_id','=',$buyer_quote_id)
        	->where('spi.created_by','=',Auth::user()->id)
        	->where('sp.lkp_post_status_id','=',OPEN)
        	->select('spi.seller_post_id','spi.id','sp.tracking','sp.lkp_payment_mode_id',
        			'sp.accept_payment_netbanking','sp.accept_payment_credit',
        			'sp.accept_payment_debit','sp.credit_period',
        			'sp.credit_period_units','sp.accept_credit_netbanking','sp.accept_credit_cheque')
        			->get();
        	return $data;
        }
	public static function truckHaulSellerPostDetails($oid){
        	$data=DB::table('orders as o')
        	->join( 'truckhaul_seller_post_items as spi', 'o.seller_post_item_id', '=', 'spi.id' )
                ->join( 'truckhaul_seller_posts as sp', 'sp.id', '=', 'spi.seller_post_id' )        
        	->where('o.id','=',$oid)
        	->select('spi.vehicle_number')
        			->first();
        	return $data;
        }
	
        public static function getUserDetBooknow($id){
        
        	$getUserrole = DB::table('users')
			->where('users.id', $id)
			->select('users.primary_role_id','users.is_business')
			->first();
			
			
				if($getUserrole->is_business == 1){
					$buyerTable = 'seller_details';
					$contact = 'contact_mobile';
					$contactland='contact_landline';
					$gta = 'gta';
					$tin = 'tin';
					$serivce = 'service_tax_number';
					$est= 'established_in';
                     $principal_place='principal_place';
				}else{
					$buyerTable = 'seller_details';
					$contact = 'contact_mobile';
					$contactland='contact_landline';
					$gta = 'gta';
					$tin = 'tin';
					$serivce = 'service_tax_number';
					$est= 'established_in';
                    $principal_place='principal_place';
				}
			
			
			$getUserDetails = DB::table('users')
			->leftJoin( $buyerTable , 'users.id', '=', $buyerTable.'.user_id' )
			->where('users.id', $id)
			->select($buyerTable.'.'.$principal_place .' as principal_place','users.*',$buyerTable.'.description',$buyerTable.'.address1',$buyerTable.'.address2',$buyerTable.'.address3',$buyerTable.'.'.$contact .' as phone',
					$buyerTable.'.'.$gta .' as gat',$buyerTable.'.'.$tin .' as tin',$buyerTable.'.'.$serivce .' as service',$buyerTable.'.'.$est .' as est',$buyerTable.'.'.$contactland .' as land')
			->first();
			
		return $getUserDetails;
        }     
	
}
