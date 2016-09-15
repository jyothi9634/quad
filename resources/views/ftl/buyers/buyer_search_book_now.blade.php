@extends('app') @section('content')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')

{{--*/ $serviceId = Session::get('service_id') /*--}}
 	{{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $loadType = '' /*--}}
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $postStatusName = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $price = '' /*--}}
    {{--*/ $deliveryDate = '' /*--}}
@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
    
    @if(isset($data->id))
       {{--*/ $id = $data->id /*--}}
    @endif
    @if(isset($data->transaction_id))   
       {{--*/ $transactionId = $data->transaction_id /*--}}
    @endif
    @if(isset($data->load_type))   
        {{--*/ $loadType = $data->load_type /*--}}
    @endif
    @if(isset($data->vehicle_type))    
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
    @endif
    @if(isset($data->is_cancelled))    
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
    @endif
    @if(isset($data->lkp_post_status_id))   
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
    @endif
    @if(isset($data->lkp_post_status_id))    
        {{--*/ $postStatusName = $data->lkp_post_status_id /*--}}
    @endif
    @if(isset($data->lkp_post_status_id))     
        {{--*/ $price = $data->price /*--}}
    @endif 
    @if(isset($data->lease_term))
    {{--*/ $lease_term = $data->lease_term /*--}}  
    @endif 
    @if(isset($data->is_dispatch_flexible) && $data->is_dispatch_flexible==1)
    <?php  $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :date("Y-m-d", strtotime($data->from_date . ' -3 day')); ?>
    @else
    <?php  $Dispatch_Date = ($data->to_date == '0000-00-00') ? '' :$data->to_date; ?>
    @endif
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach

@endif

{{-- Search Varaibles start --}}

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
@if(isset($toLocationid) && !empty($toLocationid))
    {{--*/ $toCityid = $toLocationid /*--}}
@else
    {{--*/ $toCityid = '' /*--}}
@endif

@if(Session::has('searchMod.delivery_date_buyer') && !empty(Session::get('searchMod.delivery_date_buyer')))
    {{--*/ $deliveryDate = Session::get('searchMod.delivery_date_buyer') /*--}}
@else
    {{--*/ $deliveryDate = 'N/A' /*--}}
@endif
@if(Session::has('searchMod.dispatch_date_buyer') && !empty(Session::get('searchMod.dispatch_date_buyer')))
    {{--*/ $dispatchDate = Session::get('searchMod.dispatch_date_buyer') /*--}}
@else
    {{--*/ $dispatchDate = 'N/A' /*--}}
@endif


{{--*/ $validFrom='' /*--}}{{--*/ $validTo='' /*--}}
{{--*/ $fdispatch='' /*--}}{{--*/ $fdelivery='' /*--}}
@if($dispatchDate!="N/A") 
{{--*/ $validFrom = str_replace('/','-',$dispatchDate) /*--}}
{{--*/ $validFrom = date('Y-m-d', strtotime($validFrom)) /*--}}
@endif
@if($deliveryDate!="N/A") 
{{--*/ $validTo = str_replace("/","-",$deliveryDate) /*--}}
{{--*/ $validTo = date('Y-m-d', strtotime($validTo)) /*--}}
@endif
@if(Session::get('searchMod.fdispatch_date_buyer')== 1) 
    {{--*/ $fdispatch = $buyerCommonComponent->getPreviousNextThreeDays($validFrom) /*--}}
    {{--*/ $Dispatch_Date = ($validFrom) ?date("Y-m-d", strtotime($validFrom . ' -3 day')):'' /*--}}
@else 
    {{--*/ $fdispatch = $commonComponent->checkAndGetDate($validFrom) /*--}}
    {{--*/ $Dispatch_Date =date("Y-m-d", strtotime($validFrom)) /*--}}
@endif
@if(Session::get('searchMod.fdelivery_date_buyer')== 1 && Session::get('searchMod.delivery_date_buyer')!='') 
    {{--*/ $fdelivery = $buyerCommonComponent->getPreviousNextThreeDays($validTo) /*--}}
@else 
    @if($validTo!='' && $validTo!="0000-00-00")
        {{--*/ $validTo = $commonComponent->checkAndGetDate($validTo) /*--}}
    @else
        {{--*/ $validTo = "" /*--}}
    @endif    
    
@endif


{{-- Search Varaibles end --}}
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">

            <div class="container">
				
				<div class="clearfix"></div>

				
<!-- 				<span class="pull-right"> -->
<!-- 					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a> -->
<!--                     @if($postStatus == '2') -->
<!--                         <a href="#" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a> -->
<!--                         <a href="#" class="delete-icon" data-id="{!! $id !!}" id="cancel_buyer_counter_offer_enquiry"><i class="fa fa-trash red" title="Delete"></i></a> -->
<!--                     @endif -->
<!-- 					<a href="{{ url('buyerposts/') }}" class="back-link1">Back to Posts</a> -->
<!-- 				</span> -->

				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text" id="fromtolocations">{!! $fromCity !!} 
							@if($serviceId!=ROAD_TRUCK_LEASE)
							to {!! $toCity !!}
							@endif
							</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if($fdispatch!="")
								{{$fdispatch}}
								@else
								{{$dispatchDate}}
								@endif
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
                                @if($fdelivery!="")
								{{$fdelivery}}
								@else
								{{$deliveryDate}}
								@endif    
                                
                          
							</span>
						</div>
					</div>
					@if($serviceId!=ROAD_TRUCK_LEASE)
					<div>
						<p class="search-head">Load Type</p>
						<span class="search-result">{!! $commonComponent->getLoadType(Session::get('searchMod.load_type_buyer')) !!}</span>
					</div>
					@endif
					@if($serviceId==ROAD_TRUCK_LEASE)
					<div>
                        <p class="search-head">Lease Term</p>
                        <span class="search-result">{!! $lease_term !!}</span>
                    </div>
                    @else
					<div>
						<p class="search-head">Quantity</p>
						<span class="search-result">{!! Session::get('searchMod.quantity_buyer') !!}</span>
					</div>
					@endif
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">{!! $commonComponent->getVehicleType(Session::get('searchMod.vehicle_type_buyer')) !!}</span>
					</div>					
					<div class="text-right filter-details">
						<!--<a href="#">+ Details</a>-->
					</div>
				</div>
                <!-- Search Block Ends Here -->
                @if(isset($seller_post) && !empty($seller_post))
                    @foreach ($seller_post as $sellerData)
                    
                    {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                        <div class="search-block inner-block-bg">
                            <div class="from-to-area">
                                <p class="search-head">Vendor Name</p>
                                <span class="search-result" id="seller_name">
                                    {!! $sellerData->username !!}
                                    {{--*/ $seller_id = $sellerData->seller_id /*--}}
                                </span>
                                <div class="red">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                            </div>
                            @if($serviceId!=ROAD_TRUCK_LEASE)
                             <div>
                                <p class="search-head">Load Type</p>
                                <span class="search-result" id="load_post">
                                    {!! $sellerData->load_type !!}
                                </span>
                            </div>
                            @endif
                            <div>
                                <p class="search-head">Vehicle Type</p>
                                <span class="search-result" id="vehicle_post">
                                    {!! $sellerData->vehicle_type !!}
                                </span>
                            </div>
                             @if($serviceId!=ROAD_TRUCK_LEASE)
                            <div>
                                <p class="search-head">Transit Time</p>
                                <span class="search-result">
                                    {!! $sellerData->transitdays !!} {!! $sellerData->units !!}
                                </span>
                            </div>
                            @endif
                            
                            @if($serviceId==ROAD_TRUCK_LEASE)
                             <div>
                                <p class="search-head">Driver Cost</p>
                                <span class="search-result">{{ $commonComponent->getPriceType($sellerData->driver_charges) }}</span>
                            </div>
                            
                            <div>
                                <p class="search-head">Permit</p>
                                <span class="search-result">{{ rtrim($commonComponent->checkPermit($sellerData->id),', ') }}</span>
                            </div>
                             @endif
                             
                            <div>
                             @if($serviceId==ROAD_TRUCK_LEASE)
                               @if(Session::has('session_lease_price') && Session::get('session_lease_price')!="")
                                {{--*/ $price=Session::get('session_lease_price') /*--}}
                               @endif
                             @else
                                {{--*/ $noofloads=$commonComponent->ftlNoofLoads($sellerData->lkp_vehicle_type_id) /*--}}
                                {{--*/ $price=$noofloads*$sellerData->price /*--}}
                             @endif   
                                <p class="search-head">Price</p>
                                <span class="search-result" id="price_post">
                                    <i class="fa fa-rupee"></i> {!! $price !!} /-
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('buyersearch_booknow_buyer_id_'.$id, Auth::User()->id, array('id' => 'buyersearch_booknow_buyer_id_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_id_'.$id, $sellerData->seller_id, array('id' => 'buyersearch_booknow_seller_id_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$id, $price, array('id' => 'buyersearch_booknow_seller_price_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_from_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'buyersearch_booknow_from_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_to_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDeliveryDate), array('id' =>'buyersearch_booknow_to_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_dispatch_date_'.$id,
                                            $commonComponent->convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer')), array('id' =>'buyersearch_booknow_dispatch_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_delivery_date_'.$id,
                                            $commonComponent->convertDateForDatabase(Session::get('searchMod.delivery_date_buyer')), array('id' =>'buyersearch_booknow_delivery_date_'.$id)) !!}
                                {!! Form::hidden('fdispatch-date_'.$id,
                                                $Dispatch_Date, array('id' =>'fdispatch-date_'.$id,'class'=>'flexyDispatch')) !!}            
                            </div>
                        </div>
                    @endforeach
                @endif
				<div class="clearfix"></div>
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                        
                        @if($serviceId==ROAD_TRUCK_HAUL || $serviceId==ROAD_TRUCK_LEASE )
                                {{--*/ $form_ext = 'TH-buyer-search-booknow' /*--}}
                                {{--*/ $form_partial_name = 'buyer_truckhaul_booknow' /*--}}
                            @else
                                {{--*/ $form_ext = 'ftl-buyer-search-booknow' /*--}}
                                {{--*/ $form_partial_name = 'buyer_booknow' /*--}}
                            @endif

                            
                            {{--*/ $buyerQuoteId = $id /*--}}
                            {{--*/ $booknow_flag = 1 /*--}}
                            {!! Form::open(array('url' => '#', 'id' => $form_ext, 'name' => 'ftl-buyer-leads-booknow')) !!}
                                @include('partials.'.$form_partial_name)
                            {!! Form::close() !!}
                            <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                            <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                        </div>
                    </div>
                </div>
			</div>
		</div>
		
	{{--*/ $commercial=Session::get('session_is_commercial_date_buyer') /*--}}
		<input type="hidden" name="commerical_type" id="commerical_type" value="1">
		
		@include('partials.gsa_booknow')
			
    
	@include('partials.footer')

@endsection