@extends('app')
@section('content')
@inject('termbuyer', 'App\Components\Term\TermBuyerComponent')
@inject('common', 'App\Components\CommonComponent')

 <!-- Inner Menu Starts Here -->
    <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->
    <!-- Inner Menu Ends Here -->
    <div class="main">
      <div class="container">
       @include('partials.content_top_navigation_links')
        <div class="clearfix"></div>
        <span class="pull-left">
        <h1 class="page-title">Term Transaction - {!! $contractDetails[0]->contract_no !!}</h1>
        </span> <span class="pull-right"> 
 <?php //check status for term quotes
if ($contractDetails[0]->contract_status == PENDING_ACCEPTANCE) {
	$displayStatus = 'Pending Acceptance';
} elseif ($contractDetails[0]->contract_status == CONTRACT_ACCEPTED) {
	$displayStatus = 'Contract Accepted';
} elseif ($contractDetails[0]->contract_status == CONTRACT_CANCELLED) {
	$displayStatus = 'Cancel Contract';
} elseif ($contractDetails[0]->contract_status == ORDER_CANCELLED) {
	$displayStatus = 'Cancelled';
}	

//check the conditions for multi or not items
if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY){
$loadtype = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"lkp_load_type_id");
$vehicletype = 	$termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"lkp_vehicle_type_id");
}

$from = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"from_location_id");
if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY){
$to = 	$termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"to_location_id");
}

if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY){
if($loadtype == "multi"){
	$displayLoadType = "Many";
} else {
	$displayLoadType = $contractDetails[0]->load_type;
}
if($vehicletype == "multi"){
	$displayVehicleType = "Many";
}else {
	$displayVehicleType = $contractDetails[0]->vehicle_type;
}
}

if($from == "multi"){
	$displayFromLocationType = "Many";
}else {
	$displayFromLocationType = $contractDetails[0]->from;
}
if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY){
if($to == "multi"){
	$displayToLocationType = "Many";
}else {
	$displayToLocationType = $contractDetails[0]->to;
}
}

?>

        <a href="{{ url('buyerordersearch/') }}" class="back-link1">Back to Orders</a> </span>
        <!-- Search Block Starts Here -->
        <div class="col-md-12 padding-none">
          <div class="search-block inner-block-bg">
            <div class="from-to-area">
              {!! $contractDetails[0]->username !!}
              <div class="red "> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> </div>
            </div>
            <div class="from-to-area">
              <span class="search-result ">
                <i class="fa fa-map-marker"></i> 
                @if(count($contractDetails)>1)
                <span class="location-text">Many to Many</span>
                @else
                <span class="location-text">{!! $contractDetails[0]->from !!} 
                @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY) 
                to {!! $contractDetails[0]->to !!}
                @endif</span>
                @endif 
              </span>
            </div>
            <div class="date-area">
              <div class="col-md-6 padding-none">
                <p class="search-head">Valid From</p>
                <span class="search-result"> <i class="fa fa-calendar-o"></i> 
                @if(isset($contractDetails[0]->from_date) && $contractDetails[0]->from_date != '0000-00-00')
                {{date("d/m/Y", strtotime($contractDetails[0]->from_date))}}  
                @else &nbsp;
                @endif 
               </span> </div>
              <div class="col-md-6 padding-none">
                <p class="search-head">Valid To</p>
               <span class="search-result"> <i class="fa fa-calendar-o"></i> 
               @if(isset($contractDetails[0]->to_date) && $contractDetails[0]->to_date != '0000-00-00')
               {{date("d/m/Y", strtotime($contractDetails[0]->to_date))}}
               @else &nbsp;
               @endif 
             </span> </div>
            </div>
            
            <div>
              <p class="search-head">Status</p>
              <span class="search-result">{!! $contractDetails[0]->order_status !!}</span> </div>
            <div class="text-right filter-details">
              <div class="info-links"> <a class="transaction-details"><span class="show-icon">-</span> <span class="hide-icon">+</span> Details</a> </a> </div>
            </div>
          </div>
          <!--Search Block  details div starts here-->
          <!--toggle div starts-->
          {{--*/ $ratecard=$termbuyer->getRateCardType($contractDetails[0]->term_buyer_quote_id) /*--}}
          <div class="col-md-12 show-trans-details-div padding-none" style="display:block">
            <!-- Table Starts Here -->
            <div class="table-div table-style2 padding-none">
              <!-- Table Head Starts Here -->
              @if(Session::get('service_id')==ROAD_FTL) 
              <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none"> From</div>
                <div class="col-md-2 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">Load Type</div>
                <div class="col-md-1 padding-left-none">Vehicle Type</div>
                <div class="col-md-1 padding-left-none">Quantity</div>
                <div class="col-md-1 padding-left-none">Price(MT)</div>
                <div class="col-md-2 padding-left-none">&nbsp;</div>
              </div>
               @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
               @if($ratecard==1 || $ratecard==0) 
              <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none"> From</div>
                <div class="col-md-2 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">Volume</div>
                <div class="col-md-2 padding-left-none">Price(MT)</div>
                <div class="col-md-3 padding-left-none">&nbsp;</div>
               </div>
               @else
                <div class="table-heading inner-block-bg">
                <div class="col-md-1 padding-left-none"> From</div>
                <div class="col-md-1 padding-left-none">To</div>
                <div class="col-md-1 padding-left-none">Vehicle Category</div>
                <div class="col-md-1 padding-left-none">Vehicle Size</div>
                <div class="col-md-1 padding-left-none">Vehcile Model</div>
                <div class="col-md-1 padding-left-none">No of Vehicles</div>
                <div class="col-md-1 padding-left-none">Transport Charges</div>
                <div class="col-md-1 padding-left-none">O&D Charges</div>
                <div class="col-md-1 padding-left-none">Price </div>
               </div>
               @endif
               @elseif(Session::get('service_id')==COURIER)
               <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none">From</div>
                <div class="col-md-2 padding-left-none">To</div>
				<div class="col-md-3 padding-left-none">Volume</div>                               
                <div class="col-md-2 padding-left-none">No of Packages</div>
                <div class="col-md-3 padding-left-none">&nbsp;</div>
              </div>
               @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
              <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none">From</div>
                <div class="col-md-2 padding-left-none">To</div>
				<div class="col-md-3 padding-left-none">No of Moves</div> 
				@if($contractDetails[0]->lkp_lead_type_id==1)                              
                <div class="col-md-2 padding-left-none">Average KG/Move</div>
                @else
                <div class="col-md-2 padding-left-none">Average CBM/Move</div>
                @endif
                <div class="col-md-3 padding-left-none">&nbsp;</div>
              </div>
              @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
               <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none">From</div>
                <div class="col-md-3 padding-left-none">Services</div>
				<div class="col-md-2 padding-left-none">Numbers</div>                               
                <div class="col-md-1 padding-left-none">Rate</div>
                <div class="col-md-2 padding-left-none">&nbsp;</div>
              </div>
              @else
               <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none">From</div>
                <div class="col-md-2 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">Load Type</div>
                <div class="col-md-1 padding-left-none">Volume</div>               
                <div class="col-md-1 padding-left-none">
                KG per                
				@if(Session::get('service_id')==ROAD_PTL)
				CFT
				@elseif(Session::get('service_id')==RAIL)
				CFT				
				@elseif(Session::get('service_id')==OCEAN)
				CBM
				@elseif(Session::get('service_id')==AIR_INTERNATIONAL)
				CCM
				@elseif(Session::get('service_id')==AIR_DOMESTIC)
				CCM
				@else
				CFT
				@endif
                </div>
                 <div class="col-md-1 padding-left-none">Rate per KG</div>
                <div class="col-md-2 padding-left-none">&nbsp;</div>
              </div>
             
              @endif
              
              <!-- Table Head Ends Here -->
             
              <div class="table-data">
                <!-- Table Row Starts Here -->
                
				@foreach($contractDetails as $key=>$getBuyerTermQuotesdata)
				@if(Session::get('service_id')==COURIER)
				{!! Form::open(['url' =>'termbooknow','id' => 'term_click_booknow_'.$getBuyerTermQuotesdata->id,'name'=>'couriertermbooknow','class'=>'couriertermbooknow','rel'=>$getBuyerTermQuotesdata->id, 'autocomplete'=>'off']) !!}
				@else
				{!! Form::open(['url' =>'termbooknow','id' => 'term_click_booknow_'.$getBuyerTermQuotesdata->id ,'rel'=>$getBuyerTermQuotesdata->id, 'autocomplete'=>'off']) !!}
				@endif
				@if(Session::get('service_id')==ROAD_FTL) 
				
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->vehicle_type !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_quantity !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_price !!}</div>
                @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
                 @if($ratecard==1 || $ratecard==0) 
                <div class="table-row inner-block-bg">
                  <div class="col-md-3 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-2 padding-none">{!! $getBuyerTermQuotesdata->contract_quantity !!}</div>
                  <div class="col-md-2 padding-none">{!! $getBuyerTermQuotesdata->contract_price !!}</div>
                    
                @else
                <div class="table-row inner-block-bg">
                <div class="col-md-1 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                <div class="col-md-1 padding-left-none">{{$common->getVehicleCategoryById($getBuyerTermQuotesdata->lkp_vehicle_category_id)}}</div>
                <div class="col-md-1 padding-left-none">{{$common->getVehicleCategorytypeById($getBuyerTermQuotesdata->lkp_vehicle_category_type_id)}}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->vehicle_model !!}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->no_of_vehicles !!}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->contract_transport_charges !!}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->contract_od_charges !!}</div>
                <div class="col-md-1 padding-left-none">{!! $getBuyerTermQuotesdata->contract_price !!} </div>
                  
                @endif
                @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->number_loads !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->avg_kg_per_move !!}</div>
               @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
	              <div class="table-row inner-block-bg">
	                <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->from !!}</div>
	                <div class="col-md-3 padding-left-none">{{$common->getAllGMServiceTypesById($getBuyerTermQuotesdata->lkp_gm_service_id)}}</div>
					<div class="col-md-2 padding-left-none">{{$getBuyerTermQuotesdata->measurement}} {{ $getBuyerTermQuotesdata->measurement_units }}</div>                               
	                <div class="col-md-1 padding-left-none">{{$getBuyerTermQuotesdata->contract_price}}</div>
	              
                @elseif(Session::get('service_id')==COURIER)
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>
                  <div class="col-md-2 padding-none">{!! $getBuyerTermQuotesdata->number_packages !!}</div>
                @else
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_quantity !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_kg_per_cft !!}</div>
                  {!! Form::hidden('contract_kgper_cft_'.$getBuyerTermQuotesdata->id,$getBuyerTermQuotesdata->contract_kg_per_cft, ['id' => 'contract_kgper_cft_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control']) !!}
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_rate_per_kg !!}</div> 
                  {!! Form::hidden('contract_rateperkg_'.$getBuyerTermQuotesdata->id,$getBuyerTermQuotesdata->contract_rate_per_kg, ['id' => 'contract_rateperkg_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control']) !!}                                  
                @endif  
                  <?php 
                 
			 	$currentDate = date("Y-m-d");
				if ($getBuyerTermQuotesdata->contract_status == PENDING_ACCEPTANCE) {
					$updatedStatus = '';
				} elseif ($getBuyerTermQuotesdata->contract_status == CONTRACT_ACCEPTED ) {
// 					if ($currentDate >=$getBuyerTermQuotesdata->from_date && $currentDate <=$getBuyerTermQuotesdata->to_date) {
// 					$updatedStatus = ' Place Indent';
// 					} else {
// 					$updatedStatus = '';
// 					}
					$updatedStatus = ' Place Indent';
				} elseif ($getBuyerTermQuotesdata->contract_status == CONTRACT_CANCELLED) {
					$updatedStatus = '';
				} elseif ($getBuyerTermQuotesdata->contract_status == ORDER_CANCELLED) {
					$updatedStatus = '';
				}	
				?>
				
                  @if(Session::get('service_id')!=RELOCATION_DOMESTIC)
                  <div class="col-md-3 padding-none">
                  @else
                  @if($ratecard==1 || $ratecard==0) 
                  <div class="col-md-3 padding-none">
                  @else
                  <div class="col-md-3 padding-none">
                  @endif
                  @endif
                  {{--*/ $placeindentclass='' /*--}}
                  @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                  
                  @if($ratecard==1) 
                  {{--*/ $placeindentclass='placeindent' /*--}}
                  @else
                  {{--*/ $placeindentclass='' /*--}}
                  @endif
                  
                  @endif
                  
                  @if(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                  
                  @if($contractDetails[0]->lkp_lead_type_id==2) 
                  {{--*/ $placeindentclass='placeindent' /*--}}
                  @else
                  {{--*/ $placeindentclass='' /*--}}
                  @endif
                  
                  @endif
                  <?php if($updatedStatus!="") { ?>
                  <div class="btn add-btn pull-right show-data-cust {{$placeindentclass}}" id='{!! $getBuyerTermQuotesdata->id !!}' data-placeindenet='{!! $getBuyerTermQuotesdata->id !!}'>{!! $updatedStatus !!} </div>
                  <?php } ?>
				   {{--*/ $Indents = $termbuyer->getIndentsByContractId($getBuyerTermQuotesdata->id,Session::get('service_id'));   /*--}}
						  @if($Indents)						 
							<div class="placeindenet_history_showhide btn add-btn pull-right show-data-cust" id='{!! $getBuyerTermQuotesdata->id !!}' data-placeindenet='{!! $getBuyerTermQuotesdata->id !!}' >
								<span class="detailsslide-2">Previous Indents </span>
							</div>						  
						 @endif   
                  </div><!-- show/hide div starts-->

                  <div class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right  padding-top " id="placeindenet_history_details_{!! $getBuyerTermQuotesdata->id !!}" style="display:none">
                    @if($Indents)
                      <div class=" col-md-6 table-div table-style1 padding-none">
                      @if(Session::get('service_id')==ROAD_FTL)
                        <div class="table-heading inner-block-bg">
                          <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Qty</div>
                          <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Price</div>
                        </div>
                        <div class="table-data">
                        @foreach($Indents as $Indent)
                          <div class="table-row inner-block-bg">
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->indent_quantity !!}</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->contract_price !!}</div>
                          </div>
                        @endforeach
                          </div>
                       @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
                          <?php //echo "<pre>";print_R($Indents);echo "</pre>";?>
                                  <div class="table-heading inner-block-bg">
                                      <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                                          @if($ratecard==1)
                                            Volume
                                          @else
                                              Number of Vehicles
                                          @endif
                                      </div>
                                      <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Price</div>
                                  </div>
                                  <div class="table-data">
                                      @foreach($Indents as $Indent)
                                          <div class="table-row inner-block-bg">
                                              <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                                                  @if($ratecard==1)
                                                      {!! $Indent->volume !!}
                                                  @else
                                                      {!! $Indent->indent_quantity !!}
                                                  @endif
                                              </div>
                                              <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->contract_price !!}</div>
                                          </div>
                                      @endforeach
                                  </div>
                       @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                       <div class="table-heading inner-block-bg">
                          <div class="col-md-6 col-sm-6 col-xs-6 padding-none">No of Days</div>
                          <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Price</div>
                        </div>
                       <div class="table-data">
                        @foreach($Indents as $Indent)
                          <div class="table-row inner-block-bg">
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->indent_quantity !!}</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->contract_price !!}</div>
                          </div>
                        @endforeach
                          </div>
                       @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                        @if(isset($Indents[0]->lkp_property_type_id) && $Indents[0]->lkp_property_type_id==0)
                              <div class="table-heading inner-block-bg">
                                 <div class="col-md-3 col-sm-6 col-xs-6 padding-none">Carton 1</div>
                                 <div class="col-md-3 col-sm-6 col-xs-6 padding-none">Carton 2</div>
                                 <div class="col-md-3 col-sm-6 col-xs-6 padding-none">Carton 3</div>
                                 <div class="col-md-3 col-sm-6 col-xs-6 padding-none">Price</div>
                              </div>
                        @else
                              <div class="table-heading inner-block-bg">
                                 <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Property Type</div>
                                 <div class="col-md-6 col-sm-6 col-xs-6 padding-none">Volume</div>                                
                              </div>
                        @endif
                       <div class="table-data">
                        @foreach($Indents as $Indent)
                          @if(isset($Indent->lkp_property_type_id) && $Indent->lkp_property_type_id==0)
                          <div class="table-row inner-block-bg">
                            <div class="col-md-3 col-sm-6 col-xs-6 padding-none">{!! $Indent->cartons_one !!}</div>
                            <div class="col-md-3 col-sm-6 col-xs-6 padding-none">{!! $Indent->cartons_two !!}</div>
                            <div class="col-md-3 col-sm-6 col-xs-6 padding-none">{!! $Indent->cartons_three !!}</div>
                            <div class="col-md-3 col-sm-6 col-xs-6 padding-none">{!! $Indent->contract_price !!}</div>
                          </div>
                          @else
                          <div class="table-row inner-block-bg">
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $common->getPropertyType($Indent->lkp_property_type_id) !!}</div>
                            <div class="col-md-6 col-sm-6 col-xs-6 padding-none">{!! $Indent->volume !!}</div>                            
                          </div>
                          @endif
                        @endforeach
                          </div>
                       @else
                         <div class="table-heading inner-block-bg">
                          <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Volume</div>
                          <div class="col-md-4 col-sm-4 col-xs-4 padding-none">No of Packages</div>
                          <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Price</div>
                        </div>
                        <div class="table-data">
                        @foreach($Indents as $Indent)
                          <div class="table-row inner-block-bg">
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{!! $Indent->volume !!}</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{!! $Indent->noofpackages 	 !!}</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{!! $Indent->contract_price !!}</div>
                          </div>
                        @endforeach
                          </div>

                       @endif
                      </div>
                    @endif
                  </div>


                  @if(Session::get('service_id')==ROAD_FTL)
                  <div class="col-md-12 show-data-div show-data-div-styles"  id="placeindenet_details_{!! $getBuyerTermQuotesdata->id !!}" style="display:none">
                    <!--	coloumn starts-->
                    <div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                        {!! Form::text('total_quanity_'.$getBuyerTermQuotesdata->id, $getBuyerTermQuotesdata->contract_quantity  , ['id' => 'total_quanity_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input numberVal fivedigitsthreedecimals_deciVal','placeholder' => 'Total Quantity', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                        {!! Form::text('current_indenet_quantity_'.$getBuyerTermQuotesdata->id,'', ['id' => 'indenet_quantity_'.$getBuyerTermQuotesdata->id,'qty_id'=>$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 sm-input indenet_quantity numberVal fivedigitsthreedecimals_deciVal','placeholder' => 'Current Indent Quantity *']) !!}

                        {!! Form::hidden('vehicle_capacity_'.$getBuyerTermQuotesdata->id, $getBuyerTermQuotesdata->capacity  , ['id' => 'vehicle_capacity_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'Vehicle Capacity', 'readonly'=>'readonly']) !!}
                        {!! Form::hidden('vehicle_units_'.$getBuyerTermQuotesdata->id, $getBuyerTermQuotesdata->units  , ['id' => 'vehicle_units_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'Vehicle Capacity', 'readonly'=>'readonly']) !!}
                        {!! Form::hidden('noofloads_'.$getBuyerTermQuotesdata->id, '' , ['id' => 'noofloads_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'No of Loads', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>

                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                        {!! Form::text('contract_price_'.$getBuyerTermQuotesdata->id,$getBuyerTermQuotesdata->contract_price, ['id' => 'contract_price_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'Price *', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                        {!! Form::text('numofloads'.$getBuyerTermQuotesdata->id,'', ['id' => 'numofloads_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'Number of Loads *', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    <div class="col-md-2 padding-left-none	">
                      <div class="input-prepend">
                        {!! Form::text('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'', ['id' => 'total_price_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 textbox-gray-bg sm-input','placeholder' => 'Total Price *', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-2 padding-none pull-right ">
                    {!!	Form::hidden('valid_id',$getBuyerTermQuotesdata->id,array('class'=>'form-control form-control1','id'=>'valid_id')) !!}
                    {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
					{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
					{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
                    {!! Form::submit('Book Now', ['name' => 'booknow','id'=>$getBuyerTermQuotesdata->id,'class'=>'btn red-btn pull-right md-cust-btn boonowvalidations', 'onclick'=>'checkSetTermBooknow()']) !!}
                    </div>
                    <!--	coloumn ends-->
                  </div><!-- show/hide div ends-->
                  @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)

                  <div class="col-md-12 show-data-div show-data-div-styles"  id="placeindenet_details_{!! $getBuyerTermQuotesdata->id !!}" style="display:none">

					@if($ratecard==1 || $ratecard==0)
	           		 <div class="col-md-12 inner-block-bg1 inner-block-bg-white">


							<div class="relocation_house_hold_buyer_create">
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-home"></i></span>
									{!!	Form::select('property_type_'.$getBuyerTermQuotesdata->id,(['' => 'Property Type *'] +$property_types), '' ,['class' =>'selectpicker property_type_select','id'=>'property_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>

							</div>
							<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('volume_'.$getBuyerTermQuotesdata->id, '', ['id' => 'volume_'.$getBuyerTermQuotesdata->id,'class' => 'form-control','readonly' => true, 'placeholder' => 'Volume*']) !!}
										<span class="add-on unit1 manage">
											CFT
										</span>
									</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('load_type_'.$getBuyerTermQuotesdata->id,(['' => 'Load Type *'] +$load_types), '' ,['class' =>'selectpicker','id'=>'load_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>
							</div>
							<div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Inventory Details</span>
							</div>
							<div class="advanced-search-details">
								<div class="col-md-3 form-control-fld margin-top padding-left-none">
									<div class="radio-block">
										<span class="padding-right-15">Origin Elevator</span>
										<input type="radio" id="elevator1_a" name="elevator_origin_{!! $getBuyerTermQuotesdata->id !!}" value="1" checked />
										<label for="elevator1_a"><span></span>Yes</label>

										<input type="radio" id="elevator1_b" name="elevator_origin_{!! $getBuyerTermQuotesdata->id !!}" value="0">
										<label for="elevator1_b"><span></span>No</label>
									</div>
								</div>
								<div class="col-md-3 form-control-fld margin-top padding-none">
									<div class="radio-block">
										<span class="padding-right-15">Destination Elevator</span>
										<input type="radio" id="elevator2_a" name="elevator_destination_{!! $getBuyerTermQuotesdata->id !!}" value="1" checked>
										<label for="elevator2_a"><span></span>Yes</label>

										<input type="radio" id="elevator2_b" name="elevator_destination_{!! $getBuyerTermQuotesdata->id !!}" value="0">
										<label for="elevator2_b"><span></span>No</label>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-3 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="origin_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="origin_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" checked > <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="origin_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="origin_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Handyman Services</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="insurance_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Insurance</span></div>
									<div class="radio-block"><input type="checkbox" name="escort_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="escort_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Escort</span></div>
									<div class="radio-block"><input type="checkbox" name="mobilty_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="mobilty_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Mobility</span></div>
									<div class="radio-block"><input type="checkbox" name="property_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="property_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Property</span></div>
									<div class="radio-block"><input type="checkbox" name="setting_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="setting_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Setting Service</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_domestic_{!! $getBuyerTermQuotesdata->id !!}" id="insurance_domestic_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Insurance Domestic</span></div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="destination_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="destination_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="destination_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="destination_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Handyman Services</span></div>
								</div>
								<div class="clearfix"></div>

							<div class="col-md-3 form-control-fld margin-top">
							<h2 class="filter-head1 margin-bottom">Complete Inventory</h2>
								<div class="normal-select">
									{!!	Form::select('room_type',(['' => 'Select Inventory *'] +$room_types), '' ,['class' =>'selectpicker select-inventory indent-inventory','id'=>'room_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>
							</div>
							<div class="clearfix"></div>
								<!-- Table Starts Here -->
							<div class="col-md-12 form-control-fld table-div table-style1 inventory-block margin-bottom-none">
								<div class="table-div table-style1 inventory-table">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">&nbsp;</div>
										<div class="col-md-2 padding-left-none text-center">No of Items</div>
										<div class="col-md-2 padding-left-none text-center">Packing Required</div>
										<div class="col-md-2 padding-left-none text-center">Crating Required</div>
									</div>
									<!-- Table Head Ends Here -->
									<div  id="inventory_data_{{$getBuyerTermQuotesdata->id}}" name="inventory_data_{{$getBuyerTermQuotesdata->id}}"></div>
								</div>

								<!-- Table Starts Here -->
								<div class="col-md-12 form-control-fld">
								<input type=button class="btn add-btn pull-right save-continue" name="savecontinue" id="savecontinue" value="Save & Continue">
								</div>
								<div class="clearfix"></div>
								<div class="col-md-12 after-inventory-block margin-top">
									<div class="table-div table-style1">
									<div name="inventory_count_div_{{$getBuyerTermQuotesdata->id}}" id="inventory_count_div_{{$getBuyerTermQuotesdata->id}}"></div>
								</div>
							</div>

								</div>
							</div>


							</div>

							<div class="relocation_vehicle_buyer_create" style="display:none;">
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('vehicle_category_'.$getBuyerTermQuotesdata->id,(['' => 'Vehicle Category *'] +$vehicletypecategories), '' ,['class' =>'selectpicker','id'=>'vehicle_category_'.$getBuyerTermQuotesdata->id,'onchange'=>'return getVehicleTypesTerm()']) !!}
								</div>
							</div>

							<div class="col-md-3 form-control-fld vehicle_type_car">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('vehicle_category_type_'.$getBuyerTermQuotesdata->id,(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), '' ,['class' =>'selectpicker','id'=>'vehicle_category_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>
							</div>

							<div class="col-md-2 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!! Form::text('vehicle_model_'.$getBuyerTermQuotesdata->id, '', ['id' => 'vehicle_model_'.$getBuyerTermQuotesdata->id,'class' => 'form-control', 'placeholder' => 'Vehicle Model*']) !!}
								</div>
							</div>


							</div>

				<div class="clearfix"></div>

				<div class="clearfix"></div>

                    <!--  coloumn starts-->
                    <div class="col-md-3">
                     <span class="data-head" name="total_volume_{{$getBuyerTermQuotesdata->id}}" id="total_volume_{{$getBuyerTermQuotesdata->id}}">Volume : <label id="displayVolumeW_{{$getBuyerTermQuotesdata->id}}">0.00</label>
					 <input type="hidden" value="" name="total_hidden_volume_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_volume_{{$getBuyerTermQuotesdata->id}}" placeholder="Display Vol. Weight *" class="form-control"></span>
                    </div>
                    <!--  coloumn ends-->

                    <!--  coloumn starts-->
                    <div class="col-md-3">
                      <span class="data-head" name="total_frieght_{{$getBuyerTermQuotesdata->id}}" id="total_frieght_{{$getBuyerTermQuotesdata->id}}">Total Freight : <label id="displaybaseFright_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                      <input type="hidden" value="" name="total_hidden_frieght_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_frieght_{{$getBuyerTermQuotesdata->id}}" placeholder="Display Vol. Weight *" class="form-control"></span>
                    </div>
                    <!--  coloumn ends-->

                     <!--  coloumn starts-->
                    <div class="col-md-3">
                      <span class="data-head" name="total_amount_{{$getBuyerTermQuotesdata->id}}" id="total_amount_{{$getBuyerTermQuotesdata->id}}">Total Amount : <label id="displaytotalamnt_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                      <input type="hidden" value="" name="total_hidden_amount_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_amount_{{$getBuyerTermQuotesdata->id}}" placeholder="total Amount *" class="form-control">
                    </div>
                    <!--  coloumn ends-->
					@else
                    <div class="col-md-3 padding-none">
                     {!!	Form::text('term_numberofveh_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control form-control1 spl-txt term_veh numericvalidation','placeholder'=>'Number of Vehicles *','id'=>'term_numberofveh_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                   </div>

                   <div class="col-md-3">
                      <span class="data-head" name="total_frieght_veh_{{$getBuyerTermQuotesdata->id}}" id="total_frieght_veh_{{$getBuyerTermQuotesdata->id}}">Total Freight : <label id="displaybaseFright_veh_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                      <input type="hidden" value="" name="total_hidden_frieght_veh_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_frieght_veh_{{$getBuyerTermQuotesdata->id}}" placeholder="Display Vol. Weight *" class="form-control"></span>
                    </div>
                    <!--  coloumn ends-->

                     <!--  coloumn starts-->
                    <div class="col-md-3">
                      <span class="data-head" name="total_amount_veh_{{$getBuyerTermQuotesdata->id}}" id="total_amount_veh_{{$getBuyerTermQuotesdata->id}}">Total Amount : <label id="displaytotalamnt_veh_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                      <input type="hidden" value="" name="total_hidden_amount_veh_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_amount_veh_{{$getBuyerTermQuotesdata->id}}" placeholder="total Amount *" class="form-control">
                    </div>

                      @endif
                     <div class="clearfix"></div>

                     <!--  coloumn starts-->
                    <div class="col-md-2 padding-none pull-right ">
                        {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
						{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
						{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
	                    <input type="submit" value="Booknow" class="btn red-btn pull-right relocationbooknow" id="{{$getBuyerTermQuotesdata->id}}" rel="{{$ratecard}}" name="booknow">
                    </div>
				</div>

				<input type="hidden" name="placeindent" id="placeindent" value="1">
				<input type="hidden" name="contractprice_{{$getBuyerTermQuotesdata->id}}" id="contractprice_{{$getBuyerTermQuotesdata->id}}" value="{{$getBuyerTermQuotesdata->contract_price}}">
                <input type="hidden" name="total_hidden_amnt_{{$getBuyerTermQuotesdata->id}}" id="total_hidden_amnt_{{$getBuyerTermQuotesdata->id}}" value="">

                  </div>

                  @elseif(Session::get('service_id')==COURIER)
                 
	                 @if(isset($contractDetails[0]->lkp_courier_type_id) && $contractDetails[0]->lkp_courier_type_id == 1)
								{{--*/ $doc_select =  'style = display:none'/*--}}
								@else
								{{--*/ $doc_select =  'style = display:block'/*--}}
					 @endif
                  
                  <div class="col-md-12 show-data-div show-data-div-styles">
                  
                  <div id ='documents_display_courier' {{ $doc_select }} class="col-md-3 form-control-fld">
							<div class="normal-select">
									{!!	Form::select('ptlpurposesType',(['' => 'Courier Purposes*'] +$CourierTypes), '' ,['class' =>'selectpicker','id'=>'ptlPurposesType']) !!}
							</div>
				 </div>
                  <div class="clearfix"></div>
                    <span id="displayVolumeW"></span>
                     <input type="hidden" name="total_ccm_{{$getBuyerTermQuotesdata->id}}" id="total_ccm_{{$getBuyerTermQuotesdata->id}}" value="">
                    <!--	coloumn starts-->
                    <div {{ $doc_select }} class="col-md-6 padding-left-none">
                   
                      <div class="input-prepend">
                     		<div class="col-md-3 padding-none">
                        {!!	Form::text('courier_term_length_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control form-control1 spl-txt courier_term_length numberVal ','placeholder'=>'L *','id'=>'courier_term_length_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        {!!	Form::text('courier_term_width_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control spl-txt courier_term_width numberVal','placeholder'=>'B *','id'=>'courier_term_width_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        {!!	Form::text('courier_term_height_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control spl-txt courier_term_height numberVal','placeholder'=>'H *','id'=>'courier_term_height_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        {!!	Form::hidden('placeindent_id',$getBuyerTermQuotesdata->id,array('class'=>'form-control form-control1','id'=>'placeindent_id')) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        <span class="add-on unit-days manage">
                         <div class="manage normal-select">
<!--                           {!!	Form::select('term_weighttype_'.$getBuyerTermQuotesdata->id,(['' => 'Length Unit *'] +$volumeWeightTypes), '' ,['class' =>'selectpicker bs-select-hidden courier_displayvolumeweight','id'=>'courier_term_weighttype_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id,'onChange'=>'volumeWeightCourierTerm(this.value,21,this)']) !!} -->
                         {!!	Form::select('term_weighttype_'.$getBuyerTermQuotesdata->id,($volumeWeightTypes), '' ,['class' =>'selectpicker bs-select-hidden courier_displayvolumeweight','id'=>'courier_term_weighttype_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id]) !!}
                          </div>
                        </span>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-3 form-control-fld padding-none">

                            <div class="col-md-7 padding-none">
                                    <div class="input-prepend">
                                            {!!	Form::text('ptlUnitsWeight_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control form-control1 spl-txt courier_CheckUnitWeight numberVal fourdigitstwodecimals_deciVal', 'termpack_id'=>$getBuyerTermQuotesdata->id , 'placeholder'=>'Unit Weight *','id'=>'courier_CheckUnitWeight_'.$getBuyerTermQuotesdata->id)) !!}
                                    </div>
                            </div>
                            <div class="col-md-5 padding-none">
                                    <div class="input-prepend">
                                            <span class="add-on unit-days merge">
                                                    {!!	Form::select('courier_CheckWeightUnit_'.$getBuyerTermQuotesdata->id,(['' => 'Weight Unit *'] +$unitsWeightTypes), '' ,['class' =>'selectpicker courier_CheckWeightUnit','id'=>'courier_CheckWeightUnit_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id]) !!}
                                            </span>
                                    </div>
                            </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-3 form-control-fld padding-right-none">
                      <div class="input-prepend">
                       {!! Form::text('term_noofpackages_'.$getBuyerTermQuotesdata->id,'', ['id' => 'courier_term_noofpackages_'.$getBuyerTermQuotesdata->id, 'termpack_id'=>$getBuyerTermQuotesdata->id , 'class'=>'form-control form-control1 spl-txt sm-input courier_noofpack numericvalidation ' ,'placeholder' => 'No of Packages *', 'maxlength' => 5]) !!}
                       <div id="error-package" class="error "></div>
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    
                    <div class="col-md-3 form-control-fld padding-right-none">
                      <div class="input-prepend">
                       {!! Form::text('package_value'.$getBuyerTermQuotesdata->id,'', ['id' => 'courierterm_package_value_'.$getBuyerTermQuotesdata->id, 'termpack_id'=>$getBuyerTermQuotesdata->id , 'class'=>'form-control form-control1 spl-txt sm-input numericvalidation  courier_packvalue' ,'placeholder' => 'Package Value *','maxlength' => 5]) !!}
                       <div id="error-package" class="error "></div>
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    
                    <!--	coloumn starts-->
                    <div class="col-md-3 form-control-fld padding-left-none">
                      <div class="input-prepend">
                       {!! Form::hidden('term_unit_weight_'.$getBuyerTermQuotesdata->id,$getBuyerTermQuotesdata->volume, ['id' => 'term_unit_weight_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 spl-txt textbox-gray-bg sm-input','placeholder' => 'Unit Weight', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    
                    {{--*/ $getTermBuyerQuoteSlabs = $termbuyer->getTermBuyerQuoteSlabs($getBuyerTermQuotesdata->term_buyer_quote_id,$getBuyerTermQuotesdata->seller_id,$getBuyerTermQuotesdata->lkp_service_id) /*--}}
                    {{--*/ $getMaxWeightIncWeight = $termbuyer->getMaxWeightIncWeight($getBuyerTermQuotesdata->term_buyer_quote_id,$getBuyerTermQuotesdata->lkp_service_id) /*--}}
                    
                    <input type="hidden" name ='price_slab_hidden_value' id='price_slab_hidden_value_{{$getBuyerTermQuotesdata->id}}' value='{{ count($getTermBuyerQuoteSlabs) }}'>
                    <input type="hidden" name ='buyer_quote_id' id='buyer_quote_id_{{$getBuyerTermQuotesdata->id}}' value='{{ $getBuyerTermQuotesdata->term_buyer_quote_id }}'>
                    <input type="hidden" name ='seller_id' id='seller_id_{{$getBuyerTermQuotesdata->id}}' value='{{$getBuyerTermQuotesdata->seller_id}}'>
                    <input type="hidden" name ='courier_type' id='courier_type_{{$getBuyerTermQuotesdata->id}}' value='{{$contractDetails[0]->lkp_courier_type_id}}'>
                    <input type="hidden" name ='max_weight' id='max_weight_{{$getBuyerTermQuotesdata->id}}' value='{{ $termbuyer->getMaxWeightAccepted($getBuyerTermQuotesdata->term_buyer_quote_id,$getBuyerTermQuotesdata->lkp_service_id) }}'>
                    <input type="hidden" name ='doc_type' id='doc_type_{{$getBuyerTermQuotesdata->id}}' value='1'>
                    
                    
                    				<div class="table-heading inner-block-bg">
                    				
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>
										<div class="col-md-3 padding-left-none">Quote</div>
										
									</div>
                    				<!-- Table Head Ends Here -->
									<div class="table-data form-control-fld padding-none">
										<?php $i = 1 ?>
										@foreach($getTermBuyerQuoteSlabs as $key=>$pricelab)
										<!-- Table Row Starts Here -->

										<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_min_rate }}</div>
											{!! Form::hidden("low_weight_salb_$i",$pricelab->slab_min_rate,['placeholder' => '0.00','class'=>'form-control form-control1','id'=>"low_weight_salb_$i"."_".$getBuyerTermQuotesdata->id]) !!}
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_max_rate }}</div>
											{!! Form::hidden("high_weight_slab_$i",$pricelab->slab_max_rate,['placeholder' => '0.00','class'=>'form-control form-control1','id'=>"high_weight_slab_$i"."_".$getBuyerTermQuotesdata->id]) !!}
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_rate }}</div>
											{!! Form::hidden("price_slab_$i",$pricelab->slab_rate,['placeholder' => '0.00','class'=>'form-control form-control1','id'=>"price_slab_$i"."_".$getBuyerTermQuotesdata->id]) !!}
											<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
										</div>
										<?php $i++ ?>
										<input type="hidden" name ='term_buyer_quote_sellers_quotes_price_id' id='term_buyer_quote_sellers_quotes_price_id_{{$getBuyerTermQuotesdata->id}}' value='{{$pricelab->term_buyer_quote_sellers_quotes_price_id}}'>
										<!-- Table Row Ends Here -->
										@endforeach
										
										

									</div>
                    
                    <!--	coloumn ends-->
                    
                    @if($getMaxWeightIncWeight[0]->incremental_weight != 0.00)
                    <input type="hidden" name ='incremental_weight' id='incremental_weight_{{$getBuyerTermQuotesdata->id}}' value='1'>
					<input type="hidden" name ='remaining_incremental_weight' id='remaining_incremental_weight_{{$getBuyerTermQuotesdata->id}}' value='{{ $getMaxWeightIncWeight[0]->incremental_weight }}'>
                    <h2 class="filter-head1">Incremental Weight</h2>
						<div class="col-md-5 form-control-fld padding-none margin-top">
						<div class="col-md-3 padding-left-none">
						{{ $getMaxWeightIncWeight[0]->incremental_weight }} {{ $termbuyer->getMaxWeightAcceptedUnits($getBuyerTermQuotesdata->term_buyer_quote_id,$getBuyerTermQuotesdata->lkp_service_id) }}
						</div>
						<div class="col-md-3 padding-left-none">{{ $getMaxWeightIncWeight[0]->incremental_weight_price }} Rs/-</div>
						<input type="hidden" name ='rate_per_increment' id='rate_per_increment_{{$getBuyerTermQuotesdata->id}}' value='{{$getMaxWeightIncWeight[0]->incremental_weight_price}}'>
						</div>
					@else
					<input type="hidden" name ='incremental_weight' id='incremental_weight_{{$getBuyerTermQuotesdata->id}}' value='0'>	
					<input type="hidden" name ='remaining_incremental_weight' id='remaining_incremental_weight_{{$getBuyerTermQuotesdata->id}}' value='1'>
					<input type="hidden" name ='rate_per_increment' id='rate_per_increment_{{$getBuyerTermQuotesdata->id}}' value='1'>
					@endif
                    <div class="col-md-12 form-control-fld padding-none margin-top ">
                                                      <div class="col-md-3 padding-left-none">
                                                         <span class="data-value">Conversion Factor : {{ $getQuoteAddtionalDetails[0]->conversion_factor }}</span>
                                                         <input type="hidden" name ='conversion_factor' id='conversion_factor_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->conversion_factor}}'>
                                                      </div>
                                                      <div class="col-md-3 padding-left-none">
                                                         <span class="data-value">Transit Days: {{ $getQuoteAddtionalDetails[0]->transit_days }} Days</span>
                                                         <input type="hidden" name ='transit_days' id='transit_days_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->transit_days}}'>
                                                      </div>
                    </div> 
                    <div class="col-md-12 padding-none">
                        <h5 class="data-head">Additional Charges</h5>
                        <div class="col-md-3 form-control-fld padding-left-none">
                          Fuel Surcharge :{{ $getQuoteAddtionalDetails[0]->fuel_charges }} %
                          <input type="hidden" name ='fuel_charges' id='fuel_charges_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->fuel_charges}}'>
                        </div>  
                        <div class="col-md-3 form-control-fld padding-left-none">
                         Check on Delivery :{{ $getQuoteAddtionalDetails[0]->cod_charges }} %
                          <input type="hidden" name ='cod_charges' id='cod_charges_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->cod_charges}}'>
                        </div>  
                        <div class="col-md-3 form-control-fld padding-left-none">
                          Freight Collect :{{ $getQuoteAddtionalDetails[0]->freight_charges }} /-
                          <input type="hidden" name ='freight_charges' id='freight_charges_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->freight_charges}}'>
                        </div>
                        <div class="col-md-3 form-control-fld padding-left-none">
                          ARC :{{ $getQuoteAddtionalDetails[0]->arc_charges }} %
                          <input type="hidden" name ='arc_charges' id='arc_charges_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->arc_charges}}'>
                        </div>
                        <div class="col-md-3 form-control-fld padding-left-none">
                          Maximum Value :{{ $getQuoteAddtionalDetails[0]->max_value }} /-
                          <input type="hidden" name ='max_value' id='max_value_{{$getBuyerTermQuotesdata->id}}' value='{{$getQuoteAddtionalDetails[0]->max_value}}'>
                        </div>
                  </div>
                    
                    
                    <!--  coloumn starts-->
                    <div class="col-md-2 padding-none pull-right ">
                    {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
					{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
					{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
                    {!! Form::submit('Booknow', ['name' => 'booknow','id'=>$getBuyerTermQuotesdata->id,'class'=>'btn red-btn pull-right booknowcouriervalidationsterm']) !!}
                    </div>
                    <!--  coloumn ends-->

                    <div class="clearfix"></div>

                    <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                      <span class="data-head">Total Freight :</span>
                      <span class="data-value" id="totalfrieght_{{$getBuyerTermQuotesdata->id}}">
						0.00 /-</span>
                    </div>
                    <!--  coloumn ends-->

                     <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                      <span class="data-head">Total Amount : </span>
                      <span class="data-value" id="totalamnt_{{$getBuyerTermQuotesdata->id}}">
												0.00 /-</span>
                      {!!	Form::hidden('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','placeholder'=>'total Amount *','id'=>'total_hidden_amnt_'.$getBuyerTermQuotesdata->id)) !!}</span>
                    </div>
                    <!--  coloumn ends-->
                    
                    
                  </div><!-- show/hide div ends-->
                  @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                  <div class="col-md-12 show-data-div show-data-div-styles">
                  @if($contractDetails[0]->lkp_lead_type_id==1)  
                   <div class="table-div table-style1">
			            <!-- Table Head Starts Here -->
			            <div class="table-heading inner-block-bg">
			                    <div class="col-md-8 padding-left-none">Carton Type</div>
			                    <div class="col-md-4 padding-left-none">Nos</div>
			            </div>
			            <!-- Table Head Ends Here -->
			            <div class="table-data">
			                    <!-- Table Row Starts Here -->
			                    @foreach($cartons as $carton)
			                    <div class="table-row inner-block-bg">
			                            <div class="col-md-8 padding-left-none">{{ $carton->carton_type }} ({{ $carton->carton_description }})</div>
			                            <div class="col-md-4 padding-left-none">
						<!--<input type="text" class="form-control form-control1 input-short pull-left">-->
			                                    <input type="text" class="cartons form-control form-control1 input-short pull-left clsRIASNoOfCartons" name="cartons_{{ $carton->id}}" rel="{{ $carton->weight}}" id="carton_type_{{$getBuyerTermQuotesdata->id}}">
			                            </div>
			                    </div>
			                    @endforeach
			                    <!-- Table Row Ends Here -->
			                    
			            </div>
			            
			            <div class="col-md-3 form-control-fld">
                        <div class="data-head padding-top">
                          Total Weight:  <span id="total-weight_{{$getBuyerTermQuotesdata->id}}"></span></div>

                    </div> 

                     <div class="col-md-3 form-control-fld">
                        <div class="data-head padding-top">
                          Total Freight: <span id="total-frieght_{{$getBuyerTermQuotesdata->id}}"></span></div>

                    </div> 

                     <div class="col-md-3 form-control-fld">
                        <div class="data-head padding-top">
                          Total O & D:  <span id="total-od_{{$getBuyerTermQuotesdata->id}}"></span></div>

                    </div> 

                     <div class="col-md-3 form-control-fld">
                        <div class="data-head padding-top">
                          Total : <span id="totalairamount_{{$getBuyerTermQuotesdata->id}}"></span></div>

                    </div> 

					<div class="col-md-2 padding-none pull-right ">
                        {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
						{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
						{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghtone',$getBuyerTermQuotesdata->fright_hundred , ['id' => 'frieghtone', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghtthree',$getBuyerTermQuotesdata->fright_three_hundred , ['id' => 'frieghtthree', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghtfive',$getBuyerTermQuotesdata->fright_five_hundred , ['id' => 'frieghtfive', 'class'=>'form-control']) !!}
						{!! Form::hidden('intodcharges',$getBuyerTermQuotesdata->contract_od_charges, ['id' => 'intodcharges', 'class'=>'form-control']) !!}
						{!!	Form::hidden('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_amnt_'.$getBuyerTermQuotesdata->id)) !!}
						{!!	Form::hidden('total_hidden_kgs_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_kgs_'.$getBuyerTermQuotesdata->id)) !!}
	                    <input type="submit" value="Booknow" class="btn red-btn pull-right relocationinternatonalairbooknow" id="{{$getBuyerTermQuotesdata->id}}" name="booknow">
                    </div>
			    </div>
			    @else
			    
			    <div class="relocation_house_hold_buyer_create">
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-home"></i></span>
									{!!	Form::select('property_type_'.$getBuyerTermQuotesdata->id,(['' => 'Property Type *'] +$property_types), '' ,['class' =>'selectpicker property_type_select','id'=>'property_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>

							</div>
							<div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="less-searchint">-</span> Inventory Details</span>
							</div>
							<div class="advanced-search-detailsint">
								
								<div class="clearfix"></div>
								<div class="col-md-3 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="origin_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="origin_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" checked > <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="origin_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="origin_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Handyman Services</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="insurance_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Insurance</span></div>
									
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="destination_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="destination_storage_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="destination_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}" id="destination_handy_serivce_{!! $getBuyerTermQuotesdata->id !!}"> <span class="lbl padding-8">Handyman Services</span></div>
								</div>
								<div class="clearfix"></div>

							<div class="col-md-3 form-control-fld margin-top">
							<h2 class="filter-head1 margin-bottom">Complete Inventory</h2>
								<div class="normal-select">
									{!!	Form::select('room_type',(['' => 'Select Inventory *'] +$room_types), '' ,['class' =>'selectpicker select-inventory indent-inventory','id'=>'room_type_'.$getBuyerTermQuotesdata->id]) !!}
								</div>
							</div>
							<div class="clearfix"></div>
								<!-- Table Starts Here -->
							<div class="col-md-12 form-control-fld table-div table-style1 inventory-block margin-bottom-none">
								<div class="table-div table-style1 inventory-table">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">&nbsp;</div>
										<div class="col-md-2 padding-left-none text-center">No of Items</div>
										<div class="col-md-2 padding-left-none text-center">Packing Required</div>
										<div class="col-md-2 padding-left-none text-center">Crating Required</div>
									</div>
									<!-- Table Head Ends Here -->
									<div  id="inventory_data_{{$getBuyerTermQuotesdata->id}}" name="inventory_data_{{$getBuyerTermQuotesdata->id}}"></div>
								</div>

								<!-- Table Starts Here -->
								<div class="col-md-12 form-control-fld">
								<input type=button class="btn add-btn pull-right save-continue-international" name="savecontinue" id="savecontinue" value="Save & Continue" rel="reloc_international">
								</div>
								<div class="clearfix"></div>
								<div class="col-md-12 after-inventory-block margin-top">
									<div class="table-div table-style1">
									<div name="inventory_count_div_{{$getBuyerTermQuotesdata->id}}" id="inventory_count_div_{{$getBuyerTermQuotesdata->id}}"></div>
								</div>
							</div>

								</div>
							</div>
							 <div class="col-md-3 form-control-fld">
                        <div class="data-head padding-top">
                          Shipment Type: <span id="shipment_type_{{$getBuyerTermQuotesdata->id}}"></span></div>

	                    </div> 
	
	                    <div class="col-md-3 form-control-fld">
	                        <div class="data-head padding-top">
	                          Volume (CBM) : <span id="volume_cbm_{{$getBuyerTermQuotesdata->id}}"></span></div>
	
	                    </div> 
	
	
	                    <div class="clearfix"></div>
	                   
	                    <div class="col-md-3 form-control-fld">
	                        <div class="data-head padding-top">
	                          Total Freight: <span id="frieght_ocean_{{$getBuyerTermQuotesdata->id}}"></span></div>
	
	                    </div> 
	
	                     <div class="col-md-3 form-control-fld">
	                        <div class="data-head padding-top">
	                          Total O & D: <span id="od_ocean_{{$getBuyerTermQuotesdata->id}}"></span></div>
	
	                    </div> 
	
	                     <div class="col-md-3 form-control-fld">
	                        <div class="data-head padding-top">
	                          Total : <span id="total_ocean_{{$getBuyerTermQuotesdata->id}}"></span></div>
	
	                    </div> 
							
						<div class="col-md-2 padding-none pull-right ">
                        {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
						{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
						{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
						{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
						{!! Form::hidden('odlcl',$getBuyerTermQuotesdata->odlcl_charges , ['id' => 'odlcl', 'class'=>'form-control']) !!}
						{!! Form::hidden('odtwenty',$getBuyerTermQuotesdata->odtwentyft_charges , ['id' => 'odtwenty', 'class'=>'form-control']) !!}
						{!! Form::hidden('odforty',$getBuyerTermQuotesdata->odfortyft_charges , ['id' => 'odforty', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghtlcl',$getBuyerTermQuotesdata->frieghtlcl_charges, ['id' => 'frieghtlcl', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghttwenty',$getBuyerTermQuotesdata->frieghttwentft_charges, ['id' => 'frieghttwenty', 'class'=>'form-control']) !!}
						{!! Form::hidden('frieghtforty',$getBuyerTermQuotesdata->frieghtfortyft_charges, ['id' => 'frieghtforty', 'class'=>'form-control']) !!}
						{!!	Form::hidden('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_amnt_'.$getBuyerTermQuotesdata->id)) !!}
						{!!	Form::hidden('total_hidden_kgs_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_kgs_'.$getBuyerTermQuotesdata->id)) !!}
						<input type="hidden" name="placeindent" id="placeindent" value="1">
	                    <input type="submit" value="Booknow" class="btn red-btn pull-right relocationinternatonaloceanbooknow" id="{{$getBuyerTermQuotesdata->id}}" name="booknow">
                    </div>
					</div>
				  @endif
                  </div>
                  @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                  <div class="col-md-12 show-data-div show-data-div-styles">
                  <div class="col-md-2 form-control-fld">
		            <div class="input-prepend">
		                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
		                {!! Form::text('consignment_pickup_date_'.$getBuyerTermQuotesdata->id, '',
		                    array('id' => 'consignment_pickup_date_'.$getBuyerTermQuotesdata->id, 'class'=>'calendar form-control buyer_counter_offer_consignment_pickup_date','placeholder'=>'Pickup Date *','readonly' => true)) !!}
		            </div>
		            <label class="error" id="buyer_counter_offer_consignment_pickup_date_error_{!! $getBuyerTermQuotesdata->id !!}"></label>
		        </div>
                  <div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                        {!! Form::text('number_days_'.$getBuyerTermQuotesdata->id,'',['id' => 'number_days_'.$getBuyerTermQuotesdata->id, 'class'=>'number_days form-control form-control1 sm-input numberVal clsTransitDays','placeholder' => 'No of Days']) !!}
                      </div>
                    </div>
                  <div class="col-md-3 form-control-fld">
	                        <div class="data-head padding-top">
	                         Total : <span id="total_mobility_{{$getBuyerTermQuotesdata->id}}"></span></div>
	
	               </div>   
                    {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
					{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
					{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_mobilityprice',$getBuyerTermQuotesdata->contract_price , ['id' => 'contract_mobilityprice', 'class'=>'form-control']) !!}
					{!!	Form::hidden('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_amnt_'.$getBuyerTermQuotesdata->id)) !!}
					{!!	Form::hidden('total_hidden_days_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'total_hidden_days_'.$getBuyerTermQuotesdata->id)) !!}
					{!!	Form::hidden('global_pickup_date_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'global_pickup_date_'.$getBuyerTermQuotesdata->id)) !!}
					{!! Form::hidden('term_contract_to_dateformated', date("d/m/Y", strtotime($contractDetails[0]->to_date)), ['id' => 'term_contract_to_dateformated']) !!}
					{!! Form::hidden('term_contract_from_date', date("d/m/Y", strtotime($contractDetails[0]->from_date)), ['id' => 'term_contract_from_date']) !!}
					<input type="submit" value="Booknow" class="btn red-btn pull-right relocationmobility" id="{{$getBuyerTermQuotesdata->id}}" name="booknow">
                  </div>
                  @else
                  <div class="col-md-12 show-data-div show-data-div-styles">
                    <!--	coloumn starts-->
                    <div class="col-md-6 padding-left-none">
                      <div class="input-prepend">
                        <div class="col-md-3 padding-none">
                        {!!	Form::text('term_length_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control form-control1 spl-txt term_length numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'L *','id'=>'term_length_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        {!!	Form::text('term_width_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control spl-txt term_width numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'B *','id'=>'term_width_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        {!!	Form::text('term_height_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control spl-txt term_height numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'H *','id'=>'term_height_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id )) !!}
                        {!!	Form::hidden('placeindent_id',$getBuyerTermQuotesdata->id,array('class'=>'form-control form-control1','id'=>'placeindent_id')) !!}
                        </div>
                        <div class="col-md-3 padding-none">
                        <span class="add-on unit-days manage">
                          <div class="manage normal-select">
                          {!!	Form::select('term_weighttype_'.$getBuyerTermQuotesdata->id,($volumeWeightTypes), '' ,['class' =>'selectpicker bs-select-hidden displayvolumeweight  modifydisplayvolumeweight','id'=>'term_weighttype_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id]) !!}
                          </div>
                        </span>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-3 form-control-fld padding-none">

                            <div class="col-md-7 padding-none">
                                    <div class="input-prepend">
                                            {!!	Form::text('ptlUnitsWeight_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control form-control1 spl-txt ptlCheckUnitWeight numberVal fivedigitsthreedecimals_deciVal', 'termpack_id'=>$getBuyerTermQuotesdata->id , 'placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight_'.$getBuyerTermQuotesdata->id)) !!}
                                    </div>
                            </div>
                            <div class="col-md-5 padding-none">
                                    <div class="input-prepend">
                                            <span class="add-on unit-days merge">
                                                    {!!	Form::select('ptlCheckUnitWeight_'.$getBuyerTermQuotesdata->id,(['' => 'Weight Unit *'] +$unitsWeightTypes), '' ,['class' =>'selectpicker ptlCheckUnitWeight','id'=>'ptlCheckUnitWeight_'.$getBuyerTermQuotesdata->id,'termpack_id'=>$getBuyerTermQuotesdata->id]) !!}
                                            </span>
                                    </div>
                            </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-3 form-control-fld padding-right-none">
                      <div class="input-prepend">
                       {!! Form::text('term_noofpackages_'.$getBuyerTermQuotesdata->id,'', ['id' => 'term_noofpackages_'.$getBuyerTermQuotesdata->id, 'termpack_id'=>$getBuyerTermQuotesdata->id , 'class'=>'form-control form-control1 spl-txt sm-input numericvalidation numtermpack' ,'placeholder' => 'No Of Packages *', 'maxlength' => 5]) !!}
                       <div id="error-package" class="error "></div>
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--	coloumn starts-->
                    <div class="col-md-3 form-control-fld padding-left-none">
                      <div class="input-prepend">
                       {!! Form::hidden('term_unit_weight_'.$getBuyerTermQuotesdata->id,$getBuyerTermQuotesdata->volume, ['id' => 'term_unit_weight_'.$getBuyerTermQuotesdata->id, 'class'=>'form-control form-control1 spl-txt textbox-gray-bg sm-input','placeholder' => 'Unit Weight', 'readonly'=>'readonly']) !!}
                      </div>
                    </div>
                    <!--	coloumn ends-->
                    <!--  coloumn starts-->
                    <div class="col-md-2 padding-none pull-right ">
                    {!! Form::hidden('quote_item_id',$getBuyerTermQuotesdata->term_buyer_quote_item_id , ['id' => 'quote_item_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_id',$getBuyerTermQuotesdata->id , ['id' => 'contract_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('quoteId',$getBuyerTermQuotesdata->term_buyer_quote_id , ['id' => 'quoteId', 'class'=>'form-control']) !!}
					{!! Form::hidden('enquiry_type', TERMSORDER , ['id' => 'enquiry_type','class'=>"form-control"]) !!}
					{!! Form::hidden('seller_id',$getBuyerTermQuotesdata->seller_id , ['id' => 'seller_id', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_from_date',$contractDetails[0]->from_date , ['id' => 'contract_from_date', 'class'=>'form-control']) !!}
					{!! Form::hidden('contract_to_date',$contractDetails[0]->to_date , ['id' => 'contract_to_date', 'class'=>'form-control']) !!}
                    {!! Form::submit('Booknow', ['name' => 'booknow','id'=>$getBuyerTermQuotesdata->id,'class'=>'btn red-btn pull-right boonowvalidationsterm']) !!}
                    </div>
                    <!--  coloumn ends-->

                    <div class="clearfix"></div>

                    <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                     <span class="data-head">Volume : <label id="displayVolumeW_{{$getBuyerTermQuotesdata->id}}">0.00</label>
					 {!! Form::hidden('total_price_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','placeholder'=>'Display Vol. Weight *','id'=>'term_hidden_volume_'.$getBuyerTermQuotesdata->id)) !!}
                                         {!! Form::hidden('volume_hidden_ltl_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','id'=>'term_hidden_volume_ltl_'.$getBuyerTermQuotesdata->id)) !!}
                     </span>
                    </div>
                    <!--  coloumn ends-->

                     <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                      <span class="data-head">Volumetric Weight : <label id="displayVolumetricWeight_{{$getBuyerTermQuotesdata->id}}">0.00</label> </span>
                      {!! Form::hidden('hiddenvolumetricWeight_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','placeholder'=>'Display Volumetric. Weight *','id'=>'hidden_volumetric_weight_'.$getBuyerTermQuotesdata->id)) !!}</span>
                    </div>
                    <!--  coloumn ends-->

                    <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                      <span class="data-head">Total Freight : <label id="displaybaseFright_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                    </div>
                    <!--  coloumn ends-->

                     <!--  coloumn starts-->
                    <div class="col-md-3 padding-left-none">
                      <span class="data-head">Total Amount : <label id="totalamnt_{{$getBuyerTermQuotesdata->id}}">0.00</label></span>
                      {!!	Form::hidden('total_hidden_amnt_'.$getBuyerTermQuotesdata->id,'',array('class'=>'form-control','placeholder'=>'total Amount *','id'=>'total_hidden_amnt_'.$getBuyerTermQuotesdata->id)) !!}</span>
                    </div>
                    <!--  coloumn ends-->
                  </div><!-- show/hide div ends-->
                </div>
                  @endif

                </div>
              {!! Form::close() !!}
			  @endforeach
                <!-- Table Row Ends Here -->
              </div>

            </div>
            <!-- Table Ends Here -->
          </div>
          <!--toggle div ends-->
          <!--Search Block  details div ends here-->
          <!-- Search Block Ends Here -->
        </div>
      </div>
    </div>
@endsection