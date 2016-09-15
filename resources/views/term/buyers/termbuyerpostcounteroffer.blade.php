@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('termbuyerCommonComponent', 'App\Components\Term\TermBuyerComponent')
@extends('app') 
@section('content')

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Left Nav Ends Here -->
<div class="main">
    <div class="container">
        
        
         @if(Session::has('updatebid')) 
        <div class="flash">
        <p class="text-success col-sm-12 text-center flash-txt alert-success">
        {{ Session::get('updatebid') }}
        </p>
        </div>
        @endif
        
        
        @if(Session::has('sumsg')) 
        <div class="flash">
        <p class="text-success col-sm-12 text-center flash-txt alert-success">
        {{ Session::get('sumsg') }}
        </p>
        </div>
        @endif
        
        @if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
        {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
        @else
        {{--*/ $countQuotes = 0 /*--}}
        @endif
        @if(!empty($_REQUEST) )
        {{--*/ $type = $_REQUEST['type'] /*--}}
        @else
        {{--*/ $type = 'quotes' /*--}}
        @endif
        @if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
        {{--*/ $countMessages = count($allMessagesList['result']) /*--}}
        @else
        {{--*/ $countMessages = 0 /*--}}
        @endif
        @if(isset($arrayBuyerCounterOffer) && !empty($arrayBuyerCounterOffer))
        @foreach ($arrayBuyerCounterOffer as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $buyer_quote_id = $data->term_buyer_quote_id /*--}}
        {{--*/ $loadType = $data->load_type /*--}}
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $bidType = $data->lkp_bid_type_id /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->quoteStatus /*--}}
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}                    
        @endforeach
        @else
        {{--*/ $id = '' /*--}}
        {{--*/ $transactionId = '' /*--}}
        {{--*/ $buyer_quote_id = '' /*--}}
        {{--*/ $loadType = '' /*--}}
        {{--*/ $vehicleType = '' /*--}}
        {{--*/ $isCancelled = '' /*--}}
        {{--*/ $postStatus = '' /*--}}
        {{--*/ $exactDispatchDate = '' /*--}}
        {{--*/ $exactDeliveryDate = '' /*--}}                
        @endif
        @if(isset($fromLocation) && !empty($fromLocation))
        {{--*/ $fromCity = $fromLocation /*--}}
        @else
        {{--*/ $fromCity = '' /*--}}
        @endif
        @if(isset($toLocation) && !empty($toLocation))
        {{--*/ $toCity = $toLocation /*--}}
        @else
        {{--*/ $toCity = '' /*--}}
        @endif
        @if(isset($deliveryDate) && !empty($deliveryDate))
        {{--*/ $deliveryDate = $deliveryDate /*--}}
        @else
        {{--*/ $deliveryDate = '' /*--}}
        @endif
        @if(isset($dispatchDate) && !empty($dispatchDate))
        {{--*/ $dispatchDate = $dispatchDate /*--}}
        @else
        {{--*/ $dispatchDate = '' /*--}}
        @endif
        @if (Session::has('cancelsuccessmessage'))
        <div class="flash alert-info">
            <p class="text-success col-sm-12 text-center flash-txt-counterofer">
            {{ Session::get('cancelsuccessmessage') }}
            </p>
        </div>
        @endif
        @if(Session::get('service_id') == ROAD_FTL)
        <a href="{{ url('createbuyerquote') }}"> <button class="btn post-btn pull-right">Post & get Quote</button></a>
        @elseif(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL || Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN || Session::get('service_id') == COURIER)
        <a href="{{ url('ptl/createbuyerquote') }}"> <button class="btn post-btn pull-right">Post & get Quote</button></a>
        
        @endif
        <div class="clearfix"></div>
        <span class="pull-left">
            <h1 class="page-title">Term Contract - {!! $transactionId !!}</h1>
        </span>
        <span class="pull-right">

        @if($postStatus == '2')                                 
          @if($postStatus == 2)                  
            <a href="{{ url('termbiddatedit/'. $buyer_quote_id) }}" class="back-link1">Edit Bidtime</a>
            
          @endif 
        @endif

        @if($postStatus == '2')                           		
          @if($postStatus == 2 && $quoteAccessType == 'Private')                  
            <a href="{{ url('termdraftedit/'. $buyer_quote_id) }}" class="edit-icon red">
                <i class="fa fa-edit" title="Edit"></i>
            </a>
          @endif 
          <a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
          <a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
        @endif
        </span>
        <!-- Search Block Starts Here -->
        <div class="search-block inner-block-bg margin-bottom">
            <?php
                $loadtype = $termbuyerCommonComponent->checkMulti($serviceId,$buyer_quote_id,"lkp_load_type_id");
                $vehicletype = 	$termbuyerCommonComponent->checkMulti($serviceId,$buyer_quote_id,"lkp_vehicle_type_id");
                $from = $termbuyerCommonComponent->checkMulti($serviceId,$buyer_quote_id,"from_location_id");
                $to = 	$termbuyerCommonComponent->checkMulti($serviceId,$buyer_quote_id,"to_location_id");
                $bidenddate = $termbuyerCommonComponent->getBidDatesData($serviceId,$buyer_quote_id);
                
                ?>
            @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY) 
            <div class="from-to-area">
                <span class="search-result">
                <i class="fa fa-map-marker"></i>
                <span class="location-text">
                @if($from == "multi")
                Many
                @else
                {{ $fromCity }}
                @endif
                to 
                @if($to == "multi")
                Many
                @else
                {{ $toCity }}
                @endif
                </span>
            </div>
            @else
            <div class="from-to-area">
                <span class="search-result">
                <i class="fa fa-map-marker"></i>
                <span class="location-text">                
                {{ $fromCity }}                
                </span>
            </div>
            @endif
            <div class="date-area">
                <div class="col-md-6 padding-none">
                    <p class="search-head">Valid From</p>
                    <span class="search-result">
                        <i class="fa fa-calendar-o"></i>
                        {!! $dispatchDate !!}
                    </span>
                </div>
                <div class="col-md-6 padding-none">
                    <p class="search-head">Valid To</p>
                    <span class="search-result">
                        <i class="fa fa-calendar-o"></i>
                        {!! $deliveryDate !!}
                    </span>
                </div>
            </div>
            @if($arrayBuyerCounterOffer[0]->lkp_service_id!=RELOCATION_DOMESTIC && $arrayBuyerCounterOffer[0]->lkp_service_id!=COURIER && $arrayBuyerCounterOffer[0]->lkp_service_id!=RELOCATION_INTERNATIONAL && $arrayBuyerCounterOffer[0]->lkp_service_id!=RELOCATION_GLOBAL_MOBILITY)
            <div>
                <p class="search-head">Load Type</p>
                <span class="search-result">
                @if($loadtype == "multi")
                Many
                @else
                {!! $loadType !!}
                @endif
                </span>
            </div>
            @endif
                        
            @if($arrayBuyerCounterOffer[0]->lkp_service_id == COURIER )
            <div>
                <p class="search-head">Courier Delivery Type</p>
                <span class="search-result">
                {!! $arrayBuyerCounterOffer[0]->courier_delivery_type !!}
                </span>
            </div>
            @endif
            
            @if(Session::get('service_id') == ROAD_FTL)
            <div>
                <p class="search-head">Vehicle Type</p>
                <span class="search-result">
                @if($vehicletype == "multi")
                Many
                @else
                {!! $vehicleType !!}
                @endif
                </span>
            </div>
            @endif
            
            <div>
            
                <p class="search-head">Bid Type</p>
                <span class="search-result">
                @if($arrayBuyerCounterOffer[0]->lkp_bid_type_id==1)
                Open
                @else
                 @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_DOMESTIC || $arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_INTERNATIONAL || $arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_GLOBAL_MOBILITY)
                 NA
                 @else
                Close
                @endif
                @endif
                </span>
                {{--*/ $documentname = $commonComponent->getBuyerBidDocumentsCheckingCount($buyer_quote_id) /*--}}
                @if($documentname != "")
                <a class="detailsslide-term red" style="width: 95px;display: inline-block;margin:0px;" href="{{ url('downloadbuyerbids/'.$buyer_quote_id) }}">
                Download Bid Documents
                </a>
                @endif
            </div>
            
            <div class="bid-date-area">
                <p class="search-head">Bid Ending Date & Time</p>
                <span class="search-result">
                {{--{{$commonComponent->checkAndGetDate($bidenddate[0]->bid_end_date)}} --}}
                {{$commonComponent->getBidDateTimeNewFormat($buyer_quote_id,Session::get ( 'service_id' ))}}
<!--                {{$commonComponent->getAMPM($bidenddate[0]->bid_end_date,$bidenddate[0]->bid_end_time)}}-->
                </span>
            </div>
            <div>
                <p class="search-head">Posted For</p>
                @if($arrayBuyerCounterOffer[0]->lkp_service_id!=RELOCATION_DOMESTIC)
                <span class="search-result">{!! $arrayBuyerCounterOffer[0]->quote_access !!}</span>
                @else
                <span class="search-result">
               @if($arrayBuyerCounterOffer[0]->lkp_post_ratecard_type==1)
               HHG
               @else
               Vehicle
               @endif
                </span>
                @endif
            </div>
            <div class="text-right filter-details">
                <div class="info-links">
                    <a class="transaction-details"><span class="show-icon">+</span>
                    <span class="hide-icon">-</span> Details</a>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-12 show-trans-details-div inner-block-bg">
            <!-- Table Starts Here -->
            <div class="table-div table-style1 padding-none">
                <!-- Table Head Starts Here -->
                @if($arrayBuyerCounterOffer[0]->lkp_service_id==ROAD_FTL)
                <div class="table-heading inner-block-bg">
                    <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">Load Type</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">Vehicle Type</div>
                    <div class="col-md-2 col-sm-1 col-xs-4 padding-none">Quantity</div>
                </div>
                @endif
                 @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_DOMESTIC)
                  @if($arrayBuyerCounterOffer[0]->lkp_post_ratecard_type==1)
                <div class="table-heading inner-block-bg">
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">Avg Volume/Shipment</div>
                    <div class="col-md-3 col-sm-3 col-xs-4 padding-none">No of Shipments</div>
                </div>
                @else
                <div class="table-heading inner-block-bg">
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">Vehicle Category</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">Vehicle Type</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">Vehicle Model</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">No of Vehicles</div>
                </div>
                @endif
                @endif
                @if($arrayBuyerCounterOffer[0]->lkp_service_id==ROAD_PTL || $arrayBuyerCounterOffer[0]->lkp_service_id==RAIL || $arrayBuyerCounterOffer[0]->lkp_service_id==AIR_DOMESTIC || $arrayBuyerCounterOffer[0]->lkp_service_id==AIR_INTERNATIONAL || $arrayBuyerCounterOffer[0]->lkp_service_id==OCEAN)
                <div class="table-heading inner-block-bg">
                    <div class="col-md-2 col-sm-1 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">Load Type</div>
                    <div class="col-md-2 col-sm-2 col-xs-5 padding-right-none">Packaging Type</div>
                    <div class="col-md-2 col-sm-1 col-xs-5 padding-right-none">Number of Packages</div>
                    <div class="col-md-2 col-sm-1 col-xs-5 padding-right-none">Volume</div>
                </div>
                @endif
                @if($arrayBuyerCounterOffer[0]->lkp_service_id==COURIER)
                <div class="table-heading inner-block-bg">
                    <div class="col-md-3 col-sm-1 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-3 col-sm-1 col-xs-5 padding-right-none">Number of Packages</div>
                    <div class="col-md-3 col-sm-1 col-xs-5 padding-right-none">Volume</div>
                </div>
                @endif
                 @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_INTERNATIONAL)
                <div class="table-heading inner-block-bg">
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">From Location</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">To Location</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">No of Moves</div>
                    <div class="col-md-3 col-sm-3 col-xs-4 padding-none">
                         @if($arrayBuyerCounterOffer[0]->lkp_lead_type_id == 1)
                        Average KG/Move
                        @else
                        Average CBM/Move
                        @endif
                    </div>
                </div>
                @endif
                @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_GLOBAL_MOBILITY)
                <div class="table-heading inner-block-bg">                    
                    <div class="col-md-6 col-sm-3 col-xs-3 padding-left-none">Service</div>
                    <div class="col-md-6 col-sm-3 col-xs-3 padding-none"> Numbers </div>
                </div>
                @endif                
                <div class="table-data">
                    @foreach ($arrayBuyerCounterOffer as $data)
                    @if($arrayBuyerCounterOffer[0]->lkp_service_id==ROAD_FTL)
                    <div class="table-row inner-block-bg">
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->load_type }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->vehicle_type }}</div>
                        <div class="col-md-2 col-sm-1 col-xs-4 padding-left-none">{{ $data->quantity }}  {{ $data->units }}</div>
                    </div>
                    @endif
                     @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_DOMESTIC)
                     @if($arrayBuyerCounterOffer[0]->lkp_post_ratecard_type==1)
                     <div class="table-row inner-block-bg">
                        <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->volume }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->number_packages }}</div>
                        
                    </div>
                   
                    @else
                     <div class="table-row inner-block-bg">
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">{{$commonComponent->getVehicleCategoryById($data->lkp_vehicle_category_id)}}</div>
	                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">
	                    @if($data->lkp_vehicle_category_id==1)
	                    {{$commonComponent->getVehicleCategorytypeById($data->lkp_vehicle_category_type_id)}}
	                    @else
	                    N/A
	                    @endif
	                    </div>
	                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">{{ $data->vehicle_model }}</div>
	                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">{{ $data->no_of_vehicles }}</div>
                        
                    </div>
                    @endif
                    @endif
                    
                    @if($arrayBuyerCounterOffer[0]->lkp_service_id==COURIER)
                    
                    {{--*/ $getTermBuyerQuoteSlabsInd = $termbuyerCommonComponent->getTermBuyerQuoteSlabsInd($buyer_quote_id) /*--}}
                    {{--*/ $getMaxWeightIncWeightInd = $termbuyerCommonComponent->getMaxWeightIncWeightInd($buyer_quote_id,$serviceId) /*--}}
                    
                    <div class="table-row inner-block-bg">
                        <div class="col-md-3 col-sm-1 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-3 col-sm-1 col-xs-5 padding-left-none">{{ $data->number_packages }}</div>
                        <div class="col-md-3 col-sm-1 col-xs-5 padding-left-none">{{ $data->volume }}</div>
                    </div>
                    @endif
                   @if($arrayBuyerCounterOffer[0]->lkp_service_id==ROAD_PTL || $arrayBuyerCounterOffer[0]->lkp_service_id==RAIL || $arrayBuyerCounterOffer[0]->lkp_service_id==AIR_DOMESTIC || $arrayBuyerCounterOffer[0]->lkp_service_id==AIR_INTERNATIONAL || $arrayBuyerCounterOffer[0]->lkp_service_id==OCEAN)
                    <div class="table-row inner-block-bg">
                        <div class="col-md-2 col-sm-1 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->load_type }}</div>
                        <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $data->packaging_type_name }}</div>
                        <div class="col-md-2 col-sm-1 col-xs-5 padding-left-none">{{ $commonComponent->number_format($data->number_packages,false) }}</div>
                        <div class="col-md-2 col-sm-1 col-xs-5 padding-left-none">{{ $data->volume }}</div>
                        @if($arrayBuyerCounterOffer[0]->lkp_service_id==AIR_INTERNATIONAL || $arrayBuyerCounterOffer[0]->lkp_service_id==OCEAN)
                        <div class='pull-right text-right'>
                            <div class='info-links'>						
                                <span id="{{$data->buyerquoteItemId}}" data-sellerlistid="{{$data->buyerquoteItemId}}" class='buyertermdetails_list cursor-pointer'> 
                                <span class='ftl_spot_transaction_details'><span class='show_details'>+
                                </span><span class='hide_details' style='display: none;'>-</span> Details</span> 	
                                </span>                       
                            </div>
                        </div>
                        <div class="col-md-12 show-data-div buyer_listdetails_{{$data->buyerquoteItemId}}"  id='spot_transaction_details_view_{{ $data->buyerquoteItemId }}'>
                            <div class='col-md-12 tab-modal-head'>
                                <h3>
                                    <span class='close-icon'>x</span>
                                </h3>
                            </div>
                            <div class="col-md-3 padding-left-none data-fld">
                                <span class="data-head">Shipment Type</span>
                                <span class="data-value">{{ $data->shipment_type }}</span>
                            </div>
                            <div class="col-md-3 padding-left-none data-fld">
                                <span class="data-head">Sender Identity</span>
                                <span class="data-value">{{ $data->sender_identity }}</span>
                            </div>
                            <div class="col-md-3 padding-left-none data-fld">
                                <span class="data-head">IE Code</span>
                                @if($data->ie_code!='')
                                <span class="data-value">{{ $data->ie_code }}</span>
                                @else
                                <span class="data-value">NA</span>
                                @endif
                            </div>
                            <div class="col-md-3 padding-left-none data-fld">
                                <span class="data-head">Product Made</span>									
                                @if($data->product_made!='')
                                <span class="data-value">{{ $data->product_made }}</span>
                                @else
                                <span class="data-value">NA</span>
                                @endif
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        @endif				
                    </div>
                    @endif
                     @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_INTERNATIONAL)
                     <div class="table-row inner-block-bg">
                        <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $data->from_city }}</div>
                        <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $data->to_city }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->number_loads }}</div>
                        <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">{{ $data->avg_kg_per_move }}</div>                        
                    </div>
                     @endif
                   @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_GLOBAL_MOBILITY)
                  <div class="table-row inner-block-bg">
                        <div class="col-md-6 col-sm-2 col-xs-5 padding-left-none">{{ $data->serviceType }}</div>
                        <div class="col-md-6 col-sm-2 col-xs-5 padding-left-none">{{ $data->measurement }} {{ $data->measurement_units }}</div>                                               
                  </div>
                  @endif
                     
                    @endforeach
                </div>
            </div>
            @if($arrayBuyerCounterOffer[0]->lkp_service_id==COURIER)
            <div class="col-md-12 inner-block-bg inner-block-bg1 ">
							<h2 class="filter-head1">Maximum Weight Accepted : {{ $termbuyerCommonComponent->getMaxWeightAccepted($buyer_quote_id,$serviceId) }} {{ $termbuyerCommonComponent->getMaxWeightAcceptedUnits($buyer_quote_id,$serviceId) }}</h2>

							<div class="col-md-12 padding-none">
								<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1 margin-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>										
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none">
										
										@foreach($getTermBuyerQuoteSlabsInd as $key=>$pricelabind)
										<!-- Table Row Starts Here -->

										<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">{{ $pricelabind->slab_min_rate }}</div>
											<div class="col-md-3 padding-left-none">{{ $pricelabind->slab_max_rate }}</div>											
											<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
										</div>
										
										<!-- Table Row Ends Here -->
										@endforeach
										
										

									</div>
							

									
								</div>	
									@if($getMaxWeightIncWeightInd[0]->increment_weight != 0.00)
									<h2 class="filter-head1">Incremental Weight</h2>
									<div class="col-md-5 form-control-fld margin-top">
										<div class="col-md-3 padding-left-none">{{ $getMaxWeightIncWeightInd[0]->increment_weight }} {{ $termbuyerCommonComponent->getMaxWeightAcceptedUnits($buyer_quote_id,$serviceId) }}</div>
									</div>	
									@endif

								<!-- Table Starts Here -->
							</div>

							
							<div class="clearfix"></div>

				
					</div>
				</div>
				@endif
                                
            @if($arrayBuyerCounterOffer[0]->lkp_service_id==RELOCATION_INTERNATIONAL)
            {{--*/ $sourceStorage = ($arrayBuyerCounterOffer[0] ->source_storage == 1) ? 'checked' : ''; /*--}}
            {{--*/ $destStorage = ($arrayBuyerCounterOffer[0] ->destination_storage == 1) ? 'checked' : ''; /*--}}            
                <div class="col-md-3 form-control-fld">
                    <div class="radio-block"><input type="checkbox" {{$sourceStorage}} disabled/> <span class="lbl padding-8">Storage</span></div>
                </div>
                <div class="col-md-3 form-control-fld">
                    <div class="radio-block"><input type="checkbox" {{$destStorage}} disabled/> <span class="lbl padding-8">Storage</span></div>
                </div>
                @if($arrayBuyerCounterOffer[0]->lkp_lead_type_id == 2)
                {{--*/ $sourceHandy = ($arrayBuyerCounterOffer[0] ->source_handyman == 1) ? 'checked' : ''; /*--}}
                {{--*/ $destHandy = ($arrayBuyerCounterOffer[0] ->destination_handyman == 1) ? 'checked' : ''; /*--}}  
                <div class="col-md-3 form-control-fld">
                    <div class="radio-block"><input type="checkbox" {{$sourceHandy}} disabled/> <span class="lbl padding-8">Handyman Services</span></div>
                </div>
                <div class="col-md-3 form-control-fld">
                    <div class="radio-block"><input type="checkbox" {{$destHandy}} disabled/> <span class="lbl padding-8">Handyman Services</span></div>
                </div>
                @endif
            @endif
            
            @if(isset($arrayBuyerCounterOffer[0]->lkp_quote_access_id) && $arrayBuyerCounterOffer[0]->lkp_quote_access_id!='' && $arrayBuyerCounterOffer[0]->lkp_quote_access_id == 2)            
            <div class="col-md-12">
                <div class="col-md-2 padding-left-none data-fld">
                    <span class="data-head">Post private</span>
                    @if(isset($privateSellerNames) && !empty($privateSellerNames))
                        @foreach($privateSellerNames as $key=>$privateSellerName)
                            <span class="data-value">{!! $privateSellerName->username !!}</span>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif           
            
        </div>
        <div class="main-right">
            <div class="pull-left">
                <div class="info-links">
                    <a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-messages" ><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
                    <a href="#" class="{{($type=="quotes")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes
                    <span class="badge">{!! $quotesCount !!}</span></a>
                    <a href="#"><i class="fa fa-thumbs-o-up"></i> Leads</a>
                    <a href="#"><i class="fa fa-line-chart"></i> Market Analytics</a>
                    <a href="#"><i class="fa fa-file-text-o"></i> Documentation</a>
                </div>
            </div>
            @if(Session::get('service_id') == COURIER)
            <div class="col-md-3 pull-right compare-fld">
                <div class="normal-select pull-right">
                    @if(count($arrayBuyerQuoteSellersQuotesPrices)>0)
                    <a href="javascript:void(0)"><button class="btn red-btn flat-btn pull-right">Compare</button></a>
                    @endif	
                </div>
            </div>
            @elseif(Session::get('service_id') != RELOCATION_INTERNATIONAL && Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)
            <div class="col-md-3 pull-right compare-fld">
                <div class="normal-select pull-right">
                    @if(count($arrayBuyerQuoteSellersQuotesPrices)>0)
                    <a href="/comparesellerquotes/{{ $buyerQuoteId }}"><button class="btn red-btn flat-btn pull-right">Compare</button>
                    </a>
                    @endif	
                </div>
            </div>
            @endif
            <div class="clearfix"></div>
            {{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
            <div id="ftl-buyer-messages" class="tabs-group" {{$msg_style}}>
            <div class="tab-style">
                {!! $allMessagesList['grid'] !!}
            </div>
        </div>
        {{--*/ $quotes_style   =($type=="quotes")?"style=display:block":"style=display:none" /*--}}            
        <div id="ftl-buyer-quotes" class="tabs-group table-div" {{$quotes_style}}>
        
        <div class="table-heading inner-block-bg">
							<div class="col-md-3 padding-left-none">Vendor Name<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none"></div>
						</div>
							<!-- Table Row Starts Here -->
							@if(isset($arrayBuyerQuoteSellersQuotesPrices))
								@if(count($arrayBuyerQuoteSellersQuotesPrices)==0)
								<div class="table-data">
									<div class="table-row inner-block-bg text-center">
										No records founds
									</div>
								</div>
								@endif
							@endif
        
        
        
        @if(count($arrayBuyerQuoteSellersQuotesPrices)>0)
        <div class="table-data">
        @foreach ($arrayBuyerQuoteSellersQuotesPrices as $sellerdata)
        
            <div class="table-row inner-block-bg">
                <div class="col-md-3 padding-left-none">
                    {{ $sellerdata->username }}<br>
                    <div class="red">
                        <i class='fa fa-star'></i>
                        <i class='fa fa-star'></i>
                        <i class='fa fa-star'></i>
                    </div>
                </div>
                <div class="col-md-9 padding-none">
                    <!--L1-->
                    <span class="pull-right detailsslide-term hidden-xs" id ="{{$sellerdata->seller_id}}">
                    {{--*/ $sellerContractCount = $termbuyerCommonComponent->getTermBuyerContractDetails($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    @if($sellerContractCount>=1)
                    <button class="btn red-btn  pull-right" type="button">Contract Generated</button>
                    @else
                    <button class="btn red-btn  pull-right" type="button">Generate Contract</button>
                    @endif
                    </span>
                </div>
                <div class="clearfix"></div>
                <div class="pull-right">
                    <div class="info-links">
                        <span class="detailsslide-term hidden-xs cursor-pointer" id ="{{$sellerdata->seller_id}}">
                        <span class="show_details">+</span><span class="hide_details">-</span> Details
                        </span>
                        <a href="#" class="red underline_link new_message" data-transaction_no="{{$transactionId}}" data-userid='{{$sellerdata->seller_id}}' data-id="{{$id}}" data-buyerquoteitemid="0" data-term="1"><i class="fa fa-envelope-o"></i></a>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 padding-left-none term_quote_details_{{ $sellerdata->seller_id}}" class="show-data-div margin-top" style='display: none;'>
                <div class="table-div table-style1 padding-none">
                    @if($sellerdata->lkp_service_id==ROAD_FTL)
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-2 padding-left-none">From</div>
                        <div class="col-md-2 padding-left-none">To</div>
                        <div class="col-md-2 padding-left-none">Load Type</div>
                        <div class="col-md-2 padding-left-none">Vehicle Type</div>
                        <div class="col-md-1 padding-left-none">Quantity</div>
                        <div class="col-md-1 padding-left-none">Quote</div>
                        <div class="col-md-2 padding-none">Contract Quantity</div>
                    </div>
                    @elseif($sellerdata->lkp_service_id==RELOCATION_DOMESTIC)
                    @if($arrayBuyerCounterOffer[0]->lkp_post_ratecard_type==1)
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-2 padding-left-none">From</div>
                        <div class="col-md-2 padding-left-none">To</div>
                        <div class="col-md-2 padding-left-none">Avg Volume/Shipment </div>
                        <div class="col-md-2 padding-left-none">No of Shipments</div>
                        <div class="col-md-2 padding-left-none">Rate per CFT </div>
                        <div class="col-md-2 padding-none">Transit Days </div>
                    </div>
                    @else
                    <div class="table-heading inner-block-bg">
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-2 padding-left-none">Transport Charges </div>
                    <div class="col-md-2 padding-none">O&D Charges</div>
                    <div class="col-md-2 padding-none">Transit Days </div>
                   </div>
                    @endif
                    @elseif($sellerdata->lkp_service_id==ROAD_PTL || $sellerdata->lkp_service_id==RAIL || $sellerdata->lkp_service_id==AIR_DOMESTIC || $sellerdata->lkp_service_id==AIR_INTERNATIONAL || $sellerdata->lkp_service_id==OCEAN)
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-2 padding-left-none">From</div>
                        <div class="col-md-2 padding-left-none">To</div>
                        <div class="col-md-2 padding-left-none">Load Type</div>
                        <div class="col-md-1 padding-left-none">Package Type</div>
                        <div class="col-md-1 padding-left-none">Number of packages</div>
                        <div class="col-md-1 padding-left-none">Volume</div>
                        <div class="col-md-1 padding-left-none">Rate Per Kg</div>
                        <div class="col-md-1 padding-left-none">KG Per Cft</div>
                        <div class="col-md-1 padding-none">Contract Volume</div>
                    </div>
                    @elseif($sellerdata->lkp_service_id==COURIER)
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-3 padding-left-none">From</div>
                        <div class="col-md-3 padding-left-none">To</div>
                        <div class="col-md-3 padding-left-none">Volume</div>
                        <div class="col-md-3 padding-left-none">Number of packages</div>
                    </div>
                    @elseif($sellerdata->lkp_service_id==RELOCATION_INTERNATIONAL)
                    <div class="table-heading inner-block-bg">                        
                        @if($arrayBuyerCounterOffer[0]->lkp_lead_type_id == 1)
                        <div class="col-md-2 padding-left-none arrow-down">From</div>
                        <div class="col-md-2 padding-left-none arrow-down">To</div>
                        <div class="col-md-2 padding-left-none">Freight Charges Upto 100 KG</div>
                        <div class="col-md-2 padding-left-none">Freight Charges Upto 300 KG</div>
                        <div class="col-md-2 padding-left-none">Freight Charges Upto 500 KG</div>
                        <div class="col-md-1 padding-left-none">O & D Charges (per CFT)</div>
                        @else
                        <div class="col-md-2 padding-left-none arrow-down">From</div>
                        <div class="col-md-2 padding-left-none arrow-down">To</div>
                        <div class="col-md-1 padding-left-none">O & D LCL(per CBM)</div>
                        <div class="col-md-1 padding-left-none">O & D 20 FT (per CBM)</div>
                        <div class="col-md-1 padding-left-none">O & D 40 FT (per CBM)</div>
                        <div class="col-md-1 padding-left-none">Freight LCL (per CBM)</div>
                        <div class="col-md-1 padding-left-none">Freight FCL 20 FT (Flat)</div>
                        <div class="col-md-1 padding-left-none">Freight FCL 40 FT (Flat)</div>                        
                        @endif
                        <div class="col-md-1 padding-left-none">Transit Days</div>
                    </div>
                    @elseif($sellerdata->lkp_service_id==RELOCATION_GLOBAL_MOBILITY)
                    <div class="table-heading inner-block-bg">                                                
                        <div class="col-md-4 padding-left-none">Service</div>
                        <div class="col-md-4 padding-left-none">Numbers</div>
                        <div class="col-md-4 padding-left-none">Quotes</div>
                    </div>
                    @endif
                    {{--*/ $sellerDetailsLeads = $termbuyerCommonComponent->getTermBuyerQuoteSellersQuotesPriceitemsFromId($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    @if($sellerdata->lkp_service_id==ROAD_FTL)
                    {{--*/ $totalprice = 0 /*--}}
                    @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $check_final = 0 /*--}}
                    {{--*/ $seller_quantity=$termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id ) /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->load_type }}</div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->vehicle_type }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->quantity }} </div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->initial_quote_price }}</div>
                                <div class='col-md-2 padding-none'>
                                @if($sellerquotedata->final_quote_price == 0.0000)
                                 {{--*/ $check_final = 1 /*--}}
                                <input  type='text' class="form-control form-control1 clsFTLTContractQty" id ="contractquote_{{$sellerquotedata->id}}" name ="contractquote_{{$sellerquotedata->id}}" disabled>
                                @else
                                <input  type='text' class="form-control form-control1 clsFTLTContractQty" value="{{ $seller_quantity }}" disabled>
                                {{--*/$totalprice = $totalprice +$seller_quantity /*--}}   
                                @endif
                                </div>
                        </div>
                    </div>
                    @endforeach
                    @if($totalprice == 0)
                    @if($check_final==1)
                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>
                    @endif
                    @else
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>
                    @endif
                    </form>
                    @elseif($sellerdata->lkp_service_id==RELOCATION_DOMESTIC)
                    {{--*/ $totalprice = 0 /*--}}
                    @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $seller_quantity=$termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id) /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">
                            @if($arrayBuyerCounterOffer[0]->lkp_post_ratecard_type==1)
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">{{ $sellerquotedata->volume }}</div>
                                <div class="col-md-2 col-sm-3 col-xs-5 padding-right-none">{{ $sellerquotedata->number_packages }}</div>
                                <div class="col-md-2 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->rate_per_cft }} </div>
                                <div class="col-md-2 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->transit_days }}</div>
                            @else
                            	<div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->transport_charges }} </div>
			                    <div class="col-md-2 padding-none">{{ $sellerquotedata->odcharges }}</div>
			                    <div class="col-md-2 padding-none">{{ $sellerquotedata->transit_days }} </div>
                            @endif    
                        </div>
                    </div>
                    @endforeach
                    {{--*/ $sellerContractCount = $termbuyerCommonComponent->getTermBuyerContractDetails($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    @if($sellerContractCount>=1)
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>
                    
                    @else
                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>
                    
                    @endif
                    </form>
                    @elseif($sellerdata->lkp_service_id==RELOCATION_INTERNATIONAL)
                     @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $seller_quantity=$termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id) /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">
                           	 
                                @if($arrayBuyerCounterOffer[0]->lkp_lead_type_id == 1)
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $sellerquotedata->fright_hundred }}</div>
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-right-none">{{ $sellerquotedata->fright_three_hundred }}</div>
                                <div class="col-md-2 col-sm-2 col-xs-4 padding-none">{{ $sellerquotedata->fright_five_hundred }} </div>
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->odcharges }} </div>
                                @else
                                <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-1 col-sm-1 col-xs-5 padding-left-none">{{ $sellerquotedata->odlcl_charges }}</div>
                                <div class="col-md-1 col-sm-1 col-xs-5 padding-right-none">{{ $sellerquotedata->odtwentyft_charges }}</div>
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->odfortyft_charges }} </div>
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->frieghtlcl_charges }} </div>
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->frieghttwentft_charges }} </div>
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->frieghtfortyft_charges }} </div>                                
                                @endif
                                <div class="col-md-1 col-sm-1 col-xs-4 padding-none">{{ $sellerquotedata->transit_days }} Days</div>
                            
                               
                        </div>
                    </div>
                    @endforeach
                    {{--*/ $sellerContractCount = $termbuyerCommonComponent->getTermBuyerContractDetails($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    @if($sellerContractCount>=1)
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>
                    
                    @else
                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>
                    
                    @endif
                    </form>
                              
                              
                   @elseif($sellerdata->lkp_service_id==RELOCATION_GLOBAL_MOBILITY)
                     @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $seller_quantity=$termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id) /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">                           	 
                                
                                <div class="col-md-4 col-sm-3 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">                                    
                                    <span class="lbl padding-8">{{ $sellerquotedata->serviceType }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-4 col-sm-1 col-xs-5 padding-right-none">{{ $sellerquotedata->measurement }} {{ $sellerquotedata->measurement_units }}</div>   
                                <div class="col-md-4 col-sm-1 col-xs-5 padding-right-none">{{ $sellerquotedata->initial_quote_price }}</div> 
                        </div>
                    </div>
                    @endforeach
                    {{--*/ $sellerContractCount = $termbuyerCommonComponent->getTermBuyerContractDetails($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    @if($sellerContractCount>=1)
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>                    
                    @else
                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>                    
                    @endif
                    </form> 
                    
                    @elseif($sellerdata->lkp_service_id==ROAD_PTL || $sellerdata->lkp_service_id==RAIL || $sellerdata->lkp_service_id==AIR_DOMESTIC || $sellerdata->lkp_service_id==AIR_INTERNATIONAL || $sellerdata->lkp_service_id==OCEAN)
                    {{--*/ $totalprice = 0 /*--}}
                    @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $check_final_ltl = 0 /*--}}
                    {{--*/ $seller_quantity = $termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id) /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">
                                <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-2 padding-left-none">{{ $sellerquotedata->load_type }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->packaging_type_name }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->number_packages }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->volume }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->initial_rate_per_kg }}</div>
                                <div class="col-md-1 padding-left-none">{{ $sellerquotedata->initial_kg_per_cft }}</div>
                                <div class='col-md-1 padding-none'>
                                    @if($sellerquotedata->final_rate_per_kg == 0.0000)
                                    {{--*/ $check_final_ltl = 1 /*--}}
                                    <input  type='text' class="form-control form-control1 numberVal clsRailTContractVol" id ="contractquote_{{$sellerquotedata->id}}" name ="contractquote_{{$sellerquotedata->id}}" disabled>
                                    @else
                                    {{--*/$totalprice = $totalprice + $seller_quantity /*--}}
                                    <input  type='text' class="form-control form-control1 clsRailTContractVol" value="{{ $seller_quantity }}" disabled>
                                    @endif
                                </div>
                        </div>
                    </div>
                    @endforeach
                    @if($totalprice == 0)
	                    @if($check_final_ltl==1)
	                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>
	                    @endif
                    @else
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>
                    @endif
                    </form>
                    @elseif($sellerdata->lkp_service_id==COURIER)
                    
                    {{--*/ $getTermBuyerQuoteSlabs = $termbuyerCommonComponent->getTermBuyerQuoteSlabs($sellerdata->term_buyer_quote_id,$sellerdata->seller_id,$sellerdata->lkp_service_id) /*--}}
                    {{--*/ $getQuoteAddtionalDetails = $commonComponent->getQuoteAddtionalDetails($sellerdata->term_buyer_quote_id,$sellerdata->seller_id) /*--}}
                    {{--*/ $getMaxWeightIncWeight = $termbuyerCommonComponent->getMaxWeightIncWeight($sellerdata->term_buyer_quote_id,$sellerdata->lkp_service_id) /*--}}
                    
                    {{--*/ $totalprice = 0 /*--}}
                    {{--*/ $totalsellerquantity = 0 /*--}}
                    @foreach ($sellerDetailsLeads as $sellerquotedata)
                    {{--*/ $check_final_ltl = 0 /*--}}
                    {{--*/ $seller_quantity=$termbuyerCommonComponent->getContractQuantity($sellerdata->seller_id,$sellerquotedata->term_buyer_quote_item_id) /*--}}
                   	{{--*/  $totalsellerquantity = $totalsellerquantity+$seller_quantity   /*--}}
                    <div class="table-data">
                        <div class="table-row inner-block-bg">
                            <form id="generate_contract_{{ $sellerdata->seller_id}}">
                                <div class="col-md-3 col-sm-2 col-xs-5 padding-left-none">
                                    <input type="checkbox" class="seller_quote_items_{{ $sellerdata->seller_id}}" name="{{ $sellerdata->seller_id}}_{{$sellerquotedata->term_buyer_quote_item_id}}" id="{{ $sellerdata->seller_id}}_{{$sellerquotedata->term_buyer_quote_item_id}}" onchange="javascript:checkSellerPostitem(this.id)">
                                    <span class="lbl padding-8">{{ $sellerquotedata->fromcity }}</span>
                                    <input type="hidden" name="buyer_quote_id" id="buyer_quote_id" value="{{$sellerdata->term_buyer_quote_id}}">
                                </div>
                                <div class="col-md-3 padding-left-none">{{ $sellerquotedata->tocity }}</div>
                                <div class="col-md-3 padding-left-none">{{ $sellerquotedata->volume }}</div>
                                <div class="col-md-3 padding-left-none">{{ $sellerquotedata->number_packages }}</div>
                                    @if($sellerquotedata->final_rate_per_kg==0.0000)
                                    {{--*/ $check_final_ltl = 1 /*--}}
                                    @else
                                    {{--*/$totalprice = $totalprice +$seller_quantity /*--}}
                                    @endif
                        </div>
                    </div>
                    @endforeach
                    
                    
                    <div class="col-md-12 inner-block-bg inner-block-bg1 ">
							<h2 class="filter-head1">Maximum Weight Accepted : {{ $termbuyerCommonComponent->getMaxWeightAccepted($sellerdata->term_buyer_quote_id,$sellerdata->lkp_service_id) }} {{ $termbuyerCommonComponent->getMaxWeightAcceptedUnits($sellerdata->term_buyer_quote_id,$sellerdata->lkp_service_id) }}</h2>

							<div class="col-md-12 padding-none">
								<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>
										<div class="col-md-3 padding-left-none">Quote</div>
										
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none">
										
										@foreach($getTermBuyerQuoteSlabs as $key=>$pricelab)
										<!-- Table Row Starts Here -->

										<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_min_rate }}</div>
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_max_rate }}</div>
											<div class="col-md-3 padding-left-none">{{ $pricelab->slab_rate }}</div>
											
											<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
										</div>
										
										<!-- Table Row Ends Here -->
										@endforeach
										
										

									</div>
									@if($getMaxWeightIncWeight[0]->incremental_weight != 0.00)
									<h2 class="filter-head1">Incremental Weight</h2>
									<div class="col-md-5 form-control-fld padding-none margin-top">
									<div class="col-md-3 padding-left-none">{{ $getMaxWeightIncWeight[0]->incremental_weight }} {{ $termbuyerCommonComponent->getMaxWeightAcceptedUnits($sellerdata->term_buyer_quote_id,$sellerdata->lkp_service_id) }}</div>
									<div class="col-md-3 padding-left-none"> Rs. {{ $getMaxWeightIncWeight[0]->incremental_weight_price }} /-</div>
									</div>	
									@endif
									
									<div class="col-md-12 form-control-fld padding-none margin-top ">
                                                      <div class="col-md-3 padding-left-none">
                                                         <span class="data-value">Conversion Factor : {{ $getQuoteAddtionalDetails[0]->conversion_factor }}</span>
                                                      </div>
                                                      <div class="col-md-3 padding-left-none">
                                                         <span class="data-value">Transit Days: {{ $getQuoteAddtionalDetails[0]->transit_days }}</span>
                                                      </div>
                                     </div>
									
									<div class="col-md-12 padding-none">
                                                      <h5 class="data-head margin-left-none">Additional Charges</h5>
                                                      <div class="col-md-2 padding-left-none">
                                                         <span class="data-value">Fuel Surcharge: {{ $getQuoteAddtionalDetails[0]->fuel_charges }} %</span>
                                                      </div>
                                                      <div class="col-md-2 padding-left-none">
                                                         <span class="data-value">Check on Delivry: {{ $getQuoteAddtionalDetails[0]->cod_charges }} %</span>
                                                      </div>
                                                      <div class="col-md-2 padding-left-none">
                                                         <span class="data-value">Freight Collect: {{ $getQuoteAddtionalDetails[0]->freight_charges }} /-</span>
                                                      </div>
                                                      <div class="col-md-2 padding-left-none">
                                                         <span class="data-value">ARC: {{ $getQuoteAddtionalDetails[0]->arc_charges }} %</span>
                                                      </div>
                                                      <div class="col-md-2 padding-left-none">
                                                         <span class="data-value">Maximum Value: {{ $getQuoteAddtionalDetails[0]->max_value }} /-</span>
                                                      </div>
                                    </div>
									
								</div>	

								<!-- Table Starts Here -->
							</div>

							
							<div class="clearfix"></div>

				
					</div>
				</div>
                   
                    @if($totalsellerquantity == 0)
	                    @if($check_final_ltl==1)
	                    <button type="button" value="{{ $sellerdata->seller_id}}" name="submit" rel="{{ $sellerdata->seller_id}}" class="btn post-btn margin-top pull-right margin-bottom termgeneratecontract" id="termgeneratecontract1">Generate Contract</button>
	                    @endif
                    @else
                    <button type="button" class="btn post-btn margin-top pull-right margin-bottom">Contract Generated</button>
                    @endif
                    </form>
                    @endif
                </div>
            </div>


            </div>
            
        
        <div class="clearfix"></div>
        @endforeach
        </div>
        @endif
    </div>
</div>
</div>
</div>
@endsection