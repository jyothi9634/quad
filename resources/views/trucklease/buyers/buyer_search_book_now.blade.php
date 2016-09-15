@extends('app') @section('content')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
        {{--*/ $postStatusName = $data->lkp_post_status_id /*--}}
        {{--*/ $price = $data->price /*--}}
        {{--*/ $lease_term = $data->lease_term /*--}}
        {{--*/ $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :$data->from_date /*--}}
    
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $postStatusName = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $price = '' /*--}}
@endif
@if(isset($fromLocation) && !empty($fromLocation))
    {{--*/ $fromCity = $fromLocation /*--}}
@else
    {{--*/ $fromCity = '' /*--}}
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
{{--*/ $validFrom='' /*--}}{{--*/ $validTo='' /*--}}
{{--*/ $fdispatch='' /*--}}{{--*/ $fdelivery='' /*--}}
@if(Session::get('session_dispatch_date_buyer')!="") 
<?php  $validFrom = str_replace('/','-',Session::get('session_dispatch_date_buyer')) ?>
{{--*/ $validFrom = date('Y-m-d', strtotime($validFrom)) /*--}}
@endif
@if(Session::get('session_delivery_date_buyer')!="") 
<?php $validTo = str_replace("/","-",Session::get('session_delivery_date_buyer')) ?>
{{--*/ $validTo = date('Y-m-d', strtotime($validTo)) /*--}}
@endif
@if(Session::get('session_fdispatch_date_buyer')== 1) 
    {{--*/ $fdispatch = $buyerCommonComponent->getPreviousNextThreeDays($validFrom) /*--}}
    {{--*/ $Dispatch_Date = ($validFrom) ?date("Y-m-d", strtotime($validFrom . ' -3 day')):'' /*--}}
@else 
    {{--*/ $fdispatch = $commonComponent->checkAndGetDate($validFrom) /*--}}
    {{--*/ $Dispatch_Date =date("Y-m-d", strtotime($validFrom)) /*--}}
@endif
@if(Session::get('session_fdelivery_date_buyer')== 1 && Session::get('session_delivery_date_buyer')!='') 
    {{--*/ $fdelivery = $buyerCommonComponent->getPreviousNextThreeDays($validTo) /*--}}
@else 
    {{--*/ $fdelivery = $commonComponent->checkAndGetDate($validTo) /*--}}
@endif
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
							<span class="location-text">{!! $fromCity !!}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">From Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
<!--								15 Jul 2015-->{{$fdispatch}}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">To Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
@if(Session::get('session_delivery_date_buyer') == "0000-00-00" || Session::get('session_delivery_date_buyer') == "" )
                                    N / A
                                @else
                                  {{ $fdelivery }}
                                @endif
                                
                          
							</span>
						</div>
					</div>
					
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">{!! $commonComponent->getVehicleType(Session::get('session_vehicle_type_buyer')) !!}</span>
					</div>	
                    <div>
                        <p class="search-head">Lease Term</p>
                        <span class="search-result">{!! $lease_term !!}</span>
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
                                <span class="search-result">
                                    {!! $sellerData->username !!}
                                </span>
                                <div class="red">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                            </div>
                             
                            <div>
                                <p class="search-head">Vehicle Type</p>
                                <span class="search-result">
                                    {!! $sellerData->vehicle_type !!}
                                </span>
                            </div>

                            <div>
                                <p class="search-head">Driver Cost</p>
                                <span class="search-result">{{ $commonComponent->getPriceType($sellerData->driver_charges) }}</span>
                            </div>
                            
                            <div>
                                <p class="search-head">Permit</p>
                                <span class="search-result">{{ rtrim($commonComponent->checkPermit($sellerData->id),', ') }}</span>
                            </div>

                            <div>
                                    <p class="search-head">Post Status</p>
                                    <span class="search-result">
                                    	@if(isset($sellerData->lkp_access_id) && $sellerData->lkp_access_id==1 )
                                        Public
                                        @else
                                        Private
                                        @endif
                                    </span>
                                </div>
                            <div>
                                {{--*/ $noofloads=1 /*--}}
                                {{--*/ $price=$sellerData->price /*--}}
                                <p class="search-head">Price</p>
                                <span class="search-result">
                                @if(Session::has('session_lease_price') && Session::get('session_lease_price')!="")
                                   <i class="fa fa-rupee"></i> {!! Session::get('session_lease_price') !!} /-
                                 @else
                                  <i class="fa fa-rupee"></i> {!! $price !!} /-
                                
                                 @endif   
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('buyersearch_booknow_buyer_id_'.$id, Auth::User()->id, array('id' => 'buyersearch_booknow_buyer_id_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_id_'.$id, $sellerData->seller_id, array('id' => 'buyersearch_booknow_seller_id_'.$id)) !!}
                                @if(Session::has('session_lease_price'))
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$id, Session::get('session_lease_price'), array('id' => 'buyersearch_booknow_seller_price_'.$id)) !!}
                                @else
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$id, $price, array('id' => 'buyersearch_booknow_seller_price_'.$id)) !!}
                                @endif
                                {!! Form::hidden('buyersearch_booknow_from_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'buyersearch_booknow_from_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_to_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDeliveryDate), array('id' =>'buyersearch_booknow_to_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_dispatch_date_'.$id,
                                            $commonComponent->convertDateForDatabase(Session::get('session_dispatch_date_buyer')), array('id' =>'buyersearch_booknow_dispatch_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_delivery_date_'.$id,
                                            $commonComponent->convertDateForDatabase(Session::get('session_delivery_date_buyer')), array('id' =>'buyersearch_booknow_delivery_date_'.$id)) !!}
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
                            {{--*/ $buyerQuoteId = $id /*--}}
                            {{--*/ $booknow_flag = 1 /*--}}
                            {!! Form::open(array('url' => '#', 'id' => 'TH-buyer-search-booknow', 'name' => 'ftl-buyer-leads-booknow')) !!}
                                @include('partials.buyer_truckhaul_booknow')
                            {!! Form::close() !!}
                            <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                            <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<input type="hidden" name="commerical_type" id="commerical_type" value="1">
	@include('partials.gsa_booknow')
	@include('partials.footer')

@endsection