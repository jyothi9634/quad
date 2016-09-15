@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
@inject('termbuyer', 'App\Components\Term\TermBuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')

<div class="main">
      <div class="container">
        <!-- Page top navigation Starts Here-->
       
        <div class="clearfix"></div>
        <span class="pull-left">
        <h1 class="page-title">Term Transaction - {!! $contractDetails[0]->contract_no !!}</h1>
        </span>  
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
        $vehicletype =  $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"lkp_vehicle_type_id");
        $from = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"from_location_id");
        $to =   $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"to_location_id");
            
       
        $displayLoadType = $contractDetails[0]->load_type;
       
        $displayVehicleType = $contractDetails[0]->vehicle_type;
       
        $displayFromLocationType = $contractDetails[0]->from;
      
        $displayToLocationType = $contractDetails[0]->to;
        }
        if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY){
        $from = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"from_location_id");
        $displayFromLocationType = $contractDetails[0]->from;
        }
        $indentQuantity='';
        if(Session::has('indentdata')){
        $indentData=Session::get('indentdata');
        
        if(isset($indentData['current_indenet_quantity_'.$contractDetails[0]->id])){
        $indentQuantity=$indentData['current_indenet_quantity_'.$contractDetails[0]->id];
        }
        }
        
        ?>
       
        <!-- Search Block Starts Here -->
        <div class="col-md-12 padding-none">
          <div class="search-block inner-block-bg">
            <div class="from-to-area">
              {!! $contractDetails[0]->username !!}
              <div class="red "> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> </div>
            </div>
            <div class="from-to-area">
              <span class="search-result ">
                <i class="fa fa-map-marker"></i> <span class="location-text"><?php echo $displayFromLocationType; ?>
                @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                to <?php echo $displayToLocationType; ?>
                @endif</span> 
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
<!--             <div> -->
<!--               <p class="search-head ">Term Post Number</p> -->
<!--               <span class="search-result">{!! $contractDetails[0]->id !!}</span>  -->
<!--             </div> -->
            <div>
              <p class="search-head">Status</p>
              <span class="search-result">{!! $contractDetails[0]->order_status !!}</span> </div>
            <div class="text-right filter-details">
              <div class="info-links"> <a class="transaction-details"><span class="show-icon">+</span> <span class="hide-icon">-</span> Details</a> </a> </div>
            </div>
          </div>
          <!--Search Block  details div starts here-->
          <!--toggle div starts-->
           {{--*/ $ratecard=$termbuyer->getRateCardType($contractDetails[0]->term_buyer_quote_id) /*--}}
           {{--*/ $intleadtype=$termbuyer->getInternationalType($contractDetails[0]->term_buyer_quote_id) /*--}}
          <div class="col-md-12 show-trans-details-div padding-none">
            <!-- Table Starts Here -->
            <div class="table-div table-style1 padding-none">
              <!-- Table Head Starts Here -->
              @if(Session::get('service_id')==ROAD_FTL) 
              <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none"> From</div>
                <div class="col-md-2 padding-left-none">To</div>
                <div class="col-md-3 padding-left-none">Load Type</div>
                <div class="col-md-2 padding-left-none">Vehicle Type</div>
                <div class="col-md-1 padding-left-none">Quantity</div>
                <div class="col-md-1 padding-left-none">Price(MT)</div>
              </div>
               @elseif(Session::get('service_id')==RELOCATION_DOMESTIC) 
              
               @if($ratecard==1 || $ratecard==0)
              <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none"> From</div>
                <div class="col-md-3 padding-left-none">To</div>
                <div class="col-md-3 padding-left-none">Volume</div>
                <div class="col-md-3 padding-left-none">Price(MT)</div>
              </div>
              @else
               <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none"> From</div>
                <div class="col-md-3 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">Transport Charges</div>
                <div class="col-md-2 padding-left-none">O&D Charges</div>
                <div class="col-md-2 padding-left-none">Price</div>
              </div>
              @endif
             @elseif(Session::get('service_id')==COURIER) 
               <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none">From</div>
                <div class="col-md-3 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">Courier Type</div>
                <div class="col-md-2 padding-left-none">Courier Delivery Type</div>
                <div class="col-md-2 padding-left-none">Price</div>
              </div>
             @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL) 
               <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none">From</div>
                <div class="col-md-3 padding-left-none">To</div>
                <div class="col-md-2 padding-left-none">No of Moves</div>
                @if($intleadtype==1)
                <div class="col-md-2 padding-left-none">Average KG/Move</div>
                @else
                <div class="col-md-2 padding-left-none">Average CBM/Move</div>
                @endif
                <div class="col-md-2 padding-left-none">Price</div>
               </div>
              @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY) 
                <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none">Location</div>
                <div class="col-md-3 padding-left-none">Service</div>
                <div class="col-md-3 padding-left-none">Days</div>
                <div class="col-md-3 padding-left-none">Price</div>
              </div>
             @else
               <div class="table-heading inner-block-bg">
                <div class="col-md-2 padding-left-none">From</div>
                <div class="col-md-2 padding-left-none">To</div>
                <div class="col-md-3 padding-left-none">Load Type</div>
                 <div class="col-md-1 padding-left-none">Quantity</div>
                <div class="col-md-2 padding-left-none">Rate per KG</div>
                <div class="col-md-1 padding-left-none">KG per CFT</div>
              </div>
              @endif
              <!-- Table Head Ends Here -->
             
              <div class="table-data">
                <!-- Table Row Starts Here -->
                
                @foreach($contractDetails as $key=>$getBuyerTermQuotesdata)
                @if(Session::get('service_id')==ROAD_FTL) 
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->vehicle_type !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_quantity !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_price !!}</div>
                 @elseif(Session::get('service_id')==RELOCATION_DOMESTIC) 
                 @if($ratecard==1 || $ratecard==0)
	              <div class="table-heading inner-block-bg">
	                <div class="col-md-3 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
	                <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
	                <div class="col-md-3 padding-left-none">{!! $indentData['total_hidden_volume_'.$getBuyerTermQuotesdata->id] !!}</div>
	                <div class="col-md-3 padding-left-none">{!! $indentData['total_hidden_frieght_'.$getBuyerTermQuotesdata->id] !!}</div>
	              </div>
	             @else
	             <div class="table-heading inner-block-bg">
	                <div class="col-md-3 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
	                <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->contract_transport_charges !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->contract_od_charges !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $price !!}</div>
	              </div>
	             @endif
	             @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY) 
                <div class="table-heading inner-block-bg">
                <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->from !!}</div>
                <div class="col-md-3 padding-left-none">{!! $commonComponent->getAllGMServiceTypesById($getBuyerTermQuotesdata->lkp_gm_service_id) !!}</div>
                <div class="col-md-3 padding-left-none">{!! $indentData['total_hidden_days_'.$getBuyerTermQuotesdata->id] !!}</div>
                <div class="col-md-3 padding-left-none">{!! $indentData['total_hidden_amnt_'.$getBuyerTermQuotesdata->id] !!}-/</div>
                </div>   
	             @elseif(Session::get('service_id')==COURIER) 
                <div class="table-row inner-block-bg">
                  <div class="col-md-3 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-2 padding-left-none">
                  @if($getBuyerTermQuotesdata->lkp_courier_type_id==1)
                  Document
                  @else
                  Parcel
                  @endif
                  </div>
                  <div class="col-md-2 padding-none">
                  @if($getBuyerTermQuotesdata->lkp_courier_delivery_type_id ==1)
                  Domestic
                  @else
                  International
                  @endif
                  </div>
                  <div class="col-md-2 padding-none">{!! $price !!}</div>   
                  @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL) 
                   <div class="table-heading inner-block-bg">
	                <div class="col-md-3 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
	                <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->number_loads !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $_REQUEST['total_hidden_kgs_'.$getBuyerTermQuotesdata->id] !!}</div>
	                <div class="col-md-2 padding-left-none">{!! $price !!}</div>
	              </div>
                @else
                <div class="table-row inner-block-bg">
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_quantity !!}</div>
                  <div class="col-md-2 padding-none">{!! $getBuyerTermQuotesdata->contract_rate_per_kg !!}</div>
                  <div class="col-md-1 padding-none">{!! $getBuyerTermQuotesdata->contract_kg_per_cft !!}</div>                 
                @endif 
                <!-- show/hide div starts-->
                                    
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

@if(isset($toLocationid) && !empty($toLocationid))
    {{--*/ $toCityid = $toLocationid /*--}}
@else
    {{--*/ $toCityid = '' /*--}}
@endif
         <div class="block" id="ftl_term_booknow">
                {!! Form::open(['url' =>'#','id' => 'ftl_term_insert' , 'autocomplete'=>'off']) !!}
                {{--*/ $booknow_flag = 1 /*--}}
                    {!! Form::hidden('term_booknow_quote_id', $quoteId, array('id' => 'term_booknow_quote_id')) !!}
                    {!! Form::hidden('term_booknow_buyer_id', Auth::id(), array('id' => 'term_booknow_buyer_id')) !!}
                    {!! Form::hidden('term_booknow_contract_id', $contractId, array('id' => 'term_booknow_contract_id')) !!}
                    {!! Form::hidden('enquiry_type', TERMSORDER, ['id' => 'enquiry_type']) !!}
                    {!! Form::hidden('term_total_price', $price, ['id' => 'term_total_price']) !!}
                    {!! Form::hidden('term_seller_id', $sellerId, ['id' => 'term_seller_id']) !!}
                    {!! Form::hidden('term_contract_from_date', $contractFromDate, ['id' => 'term_contract_from_date']) !!}
                    {!! Form::hidden('term_contract_to_date', $contractToDate, ['id' => 'term_contract_to_date']) !!}
                    {!! Form::hidden('term_contract_to_dateformated', date("d/m/Y", strtotime($contractDetails[0]->to_date)), ['id' => 'term_contract_to_dateformated']) !!}
                    @if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY) 
                    {!! Form::hidden('term_contract_dispatch_date', $_REQUEST['global_pickup_date_'.$contractDetails[0]->id], ['id' => 'term_contract_dispatch_date']) !!}
                    @endif
                    @include('partials.buyer_booknow')
                {!! Form::close() !!}
                <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                 <input type="hidden" name="commerical_type" id="commerical_type" value="1">

            </div>
      </div>
    </div> 	
   
    @include('partials.gsa_termbooknow')	

@endsection