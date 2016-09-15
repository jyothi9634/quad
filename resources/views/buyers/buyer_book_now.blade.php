@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')

{{--*/ $serviceId = Session::get('service_id') /*--}}

@if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
    {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
@else
    {{--*/ $countQuotes = 0 /*--}}
@endif

{{-- Start : Default Variables --}}
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $buyer_quote_id = '' /*--}}
    {{--*/ $loadType = '' /*--}}
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $priceType = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $quantity = 'NA' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $lease_term = '' /*--}}
    {{--*/ $units = '' /*--}}
{{-- END : Default Variables --}}

@if(isset($arrayBuyerCounterOffer) && !empty($arrayBuyerCounterOffer))
    @foreach ($arrayBuyerCounterOffer as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $buyer_quote_id = $data->buyer_quote_id /*--}}
        
        @if(isset($data->load_type))
            {{--*/ $loadType = $data->load_type /*--}}
        @endif

        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $priceType = $data->price_type /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
        
        @if(isset($data->quantity))    
            {{--*/ $quantity = $data->quantity /*--}}
        @endif    
        
        @if(isset($data->units))    
            {{--*/ $units = $data->units /*--}}
        @endif    
        
        @if(isset($data->lease_term))
            {{--*/ $lease_term = $data->lease_term /*--}}
        @endif

        @if(isset($data->is_dispatch_flexible) && $data->is_dispatch_flexible==1)
            {{--*/ $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :date("Y-m-d", strtotime($data->dispatch_date . ' -3 day')) /*--}}
        @else
            {{--*/ $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :$data->dispatch_date /*--}}
        @endif
        {{--*/ $exactDispatchDate = ($data->dispatch_date == '0000-00-00') ? '' : $data->dispatch_date /*--}}
        
        @if(isset($data->delivery_date))    
            {{--*/ $exactDeliveryDate = ($data->delivery_date == '0000-00-00') ? '' : $data->delivery_date /*--}}
        @endif

    @endforeach
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
@if(isset($toLocationid) && !empty($toLocationid))
    {{--*/ $toCityid = $toLocationid /*--}}
@else
    {{--*/ $toCityid = '' /*--}}
@endif
@if(isset($deliveryDate) && !empty($deliveryDate))
    {{--*/ $deliveryDate = $deliveryDate /*--}}
@else
    {{--*/ $deliveryDate = 'NA' /*--}}
@endif
@if(isset($dispatchDate) && !empty($dispatchDate))
    {{--*/ $dispatchDate = $dispatchDate /*--}}
@else
    {{--*/ $dispatchDate = '' /*--}}
@endif

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">

			<div class="container">

				<span class="pull-left"><h1 class="page-title">Spot Transaction - {!! $transactionId !!}</h1></span>
				{{-- Commented for Post / view count / Delete / Back to posts
                    <span class="pull-right">
                        @include('partials.content_top_navigation_links')
                    <div class="clearfix"></div>
    					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
                        @if($postStatus == '2')
                            @if(isset($quoteAccessType) && $quoteAccessType=='Private' )
                                <a href="{{ url('editbuyerquote/'. $buyer_quote_id .'/'. $id) }}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
                            @endif
                            <a href="#" class="delete-icon" data-id="{!! $id !!}" id="cancel_buyer_counter_offer_enquiry"><i class="fa fa-trash red" title="Delete"></i></a>
                        @endif
    					<a href="{{ url('buyerposts/') }}" class="back-link1">Back to Posts</a>
    				</span>
                --}}
				<!-- Search Block Starts Here -->

				<div class="filter-expand-block">
                    <div class="search-block inner-block-bg margin-bottom-less-1">
                        <div class="from-to-area">
                            <span class="search-result">
                                <i class="fa fa-map-marker"></i>
                                <span class="location-text" id="fromtolocations">{!! $fromCity !!} @if($toCity) to {!! $toCity !!} @endif</span>
                            </span>
                        </div>
                        <div class="date-area">
                            <div class="col-md-6 padding-none">
                                <p class="search-head">
                                    @if($serviceId!=ROAD_TRUCK_LEASE)    
                                        Dispatch Date
                                    @else
                                        From Date
                                    @endif    
                                </p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
                                    {!! $dispatchDate !!}
                                </span>
                            </div>
                            <div class="col-md-6 padding-none">
                                <p class="search-head">
                                    @if($serviceId!=ROAD_TRUCK_LEASE)    
                                        Delivery Date
                                    @else
                                        To Date
                                    @endif    
                                </p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
                                    {!! $deliveryDate !!}
                                </span>
                            </div>
                        </div>
                        @if($serviceId!=ROAD_TRUCK_LEASE )
                            <div>
                                <p class="search-head">Load Type</p>
                                <span class="search-result" id="load_post">{!! $loadType !!}</span>
                            </div>
                            <div>
                                <p class="search-head">Quantity</p>
                                <span class="search-result" id="quantity_post">{!! $quantity !!} {!! $units !!}</span>
                            </div>
                        @endif    
                        <div>
                            <p class="search-head">Vehicle Type</p>
                            <span class="search-result" id="vehicle_post">{!! $vehicleType !!}</span>
                        </div>
                        @if($serviceId==ROAD_TRUCK_LEASE )
                            <div>
                                <p class="search-head">Lease Term</p>
                                <span class="search-result">{!! $lease_term !!}</span>
                            </div>
                        @endif    
                        <div>
                            <p class="search-head">Price</p>
                            <span class="search-result" id="price_post">{!! $priceType !!}</span>
                        </div>
                        <div class="text-right filter-details">
                            @if(isset($privateSellerNames) && !empty($privateSellerNames[0]->username))
                                <div class="info-links">
                                    <a class="transaction-details-expand"><span class="show-icon">+</span>
                                        <span class="hide-icon">-</span> Details
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12 show-data-div"></div>
                    <!-- Search Block Ends Here -->
                    <!--toggle div starts-->
                    <div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
                        <div class="expand-block">
                            <div class="col-md-12">
                                <div class="col-md-2 padding-left-none data-fld">
                                    <span class="data-head">Post private</span>
                                    @if(isset($privateSellerNames) && !empty($privateSellerNames))
                                        @foreach($privateSellerNames as $key=>$privateSellerName)
                                            <span class="data-value">{!! $privateSellerName->username !!}</span><br/>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

				<!-- Search Block Ends Here -->
                @if(isset($sellerDetailsLeads) && !empty($sellerDetailsLeads))
                    @foreach ($sellerDetailsLeads as $sellerData)
                    {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                        @if($buyerQuoteForLeadId == $buyerQuoteSellerPriceId)
                            <div class="search-block inner-block-bg margin-bottom-less-1">
                                <div class="from-to-area">
                                    <p class="search-head">Vendor Name</p>
                                    <span class="search-result">
                                        {!! $sellerData->username !!}
                                        {{--*/ $seller_id = $sellerData->seller_id /*--}}
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
                                @if($serviceId!=ROAD_TRUCK_LEASE)
                                    <div>
                                        <p class="search-head">Transit Time</p>
                                        <span class="search-result">
                                                {!! $sellerData->transitdays !!} {!! $sellerData->units !!}
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        <p class="search-head">Post Status</p>
                                        <span class="search-result">
                                            @if(isset($buyerQuoteSellersQuotesDetails->lkp_access_id) && $buyerQuoteSellersQuotesDetails->lkp_access_id==1 )
                                                Public
                                            @else
                                                Private
                                            @endif
                                        </span>
                                    </div>
                                @endif    
                                <div>
                                    <p class="search-head">Price</p>
                                    <span class="search-result">
                                        <i class="fa fa-rupee"></i> 
										@if($serviceId==ROAD_TRUCK_HAUL || $serviceId==ROAD_TRUCK_LEASE )                                        
											{{--*/   $noofloads = DEFAULT_NOOFLOADS /*--}}
                                        @else
                                            {{--*/   $noofloads = $commonComponent->ftlNoofLoads($sellerData->lkp_vehicle_type_id,$quantity) /*--}}
                                        @endif    
				                        {{--*/ $price = $noofloads*$sellerData->price /*--}}
                                        {{ $commonComponent->number_format($price) }}
                                    </span>
								</div>
                                <div>

                                    {!! Form::hidden('seller_post_item_id_'.$buyerQuoteForLeadId, $sellerData->id, array('id' => 'seller_post_item_id_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteForLeadId, $sellerData->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteForLeadId, Auth::User()->id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId,
                                                $exactDispatchDate, array('id' =>'buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId,
                                                $exactDeliveryDate, array('id' =>'buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId,
                                                $sellerData->from_date, array('id' =>'buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId,
                                                $sellerData->to_date, array('id' =>'buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteForLeadId,
                                                    $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteForLeadId)) !!}
                                    {!! Form::hidden('buyer_post_price_'.$buyerQuoteForLeadId,
                                                    $price, array('id' =>'buyer_post_price_'.$buyerQuoteForLeadId,'data-price'=>$price)) !!}
                                    {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteForLeadId,
                                                    $id, array('id' =>'buyer_quote_item_id_'.$buyerQuoteForLeadId,'data-id' =>$id)) !!}                
                                </div>
                            </div>
                        @endif
                    @endforeach
                @elseif(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
                    @foreach($arrayBuyerQuoteSellersQuotesPrices as $key=>$buyerQuoteSellersQuotesDetails)
                        {{--*/ $buyerQuoteId = $buyerQuoteSellersQuotesDetails->id /*--}}
                        {{--*/ $sellerpost  =   $commonComponent->getSellersQuotesFromId($buyerQuoteSellersQuotesDetails->private_seller_quote_id) /*--}}
                        @if($buyerQuoteId == $buyerQuoteSellerPriceId)
                            @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000')
                                {{--*/ $price = $buyerQuoteSellersQuotesDetails->final_quote_price /*--}}
                                {{--*/ $priceval = 'final_quote_price' /*--}}
                            @elseif($buyerQuoteSellersQuotesDetails->firm_price != '0.0000')
                                {{--*/ $price = $buyerQuoteSellersQuotesDetails->firm_price /*--}}
                                {{--*/ $priceval = 'firm_price' /*--}}
                            @elseif($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.0000')
                                {{--*/ $price = $buyerQuoteSellersQuotesDetails->counter_quote_price /*--}}
                                {{--*/ $priceval = 'counter_quote_price' /*--}}
                            @elseif($buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                {{--*/ $price = $buyerQuoteSellersQuotesDetails->initial_quote_price /*--}}
                                {{--*/ $priceval = 'initial_quote_price' /*--}}
                            @endif
                            <div class="search-block inner-block-bg margin-bottom-less-1">
                                <div class="from-to-area">
                                    <p class="search-head">Vendor Name</p>
                                    <span class="search-result">
                                        {!! $buyerQuoteSellersQuotesDetails->username !!}
                                        {{--*/ $seller_id = $buyerQuoteSellersQuotesDetails->seller_id /*--}}
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
                                        {!! $buyerQuoteSellersQuotesDetails->vehicle_type !!}
                                    </span>
                                </div>

                                @if($serviceId!=ROAD_TRUCK_LEASE)
                                    <div>
                                        <p class="search-head">Transit Time</p>
                                        <span class="search-result">
                                        	@if(isset($buyerQuoteSellersQuotesDetails->final_transit_days) && $buyerQuoteSellersQuotesDetails->final_transit_days!=0)
                                            {!! $buyerQuoteSellersQuotesDetails->final_transit_days !!} Days
                                            @elseif(isset($buyerQuoteSellersQuotesDetails->initial_transit_days) && $buyerQuoteSellersQuotesDetails->initial_transit_days!=0)
                                            {{ $buyerQuoteSellersQuotesDetails->initial_transit_days }} Days
                                            @else
                                                @if($serviceId!=ROAD_TRUCK_LEASE )    
                                                    {!! $buyerQuoteSellersQuotesDetails->transitdays !!} {!! $buyerQuoteSellersQuotesDetails->units !!}
                                                @endif    
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        <p class="search-head">Post Status</p>
                                        <span class="search-result">
                                            @if(isset($buyerQuoteSellersQuotesDetails->lkp_access_id) && $buyerQuoteSellersQuotesDetails->lkp_access_id==1 )
                                            Public
                                            @else
                                            Private
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <p class="search-head">Price</p>
                                    <span class="search-result" data-price="{!! $price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                                        <i class="fa fa-rupee"></i> {{ $commonComponent->number_format($price) }}
                                    </span>
                                </div>
                                <div>
                                    {!! Form::hidden('buyer_post_counter_offer_id', $arrayBuyerQuoteSellersQuotesPrices[0]->id, array('id' =>'buyer_post_counter_offer_id')) !!}
                                    <input type="hidden" name="priceval" id="priceval" value="{!! $priceval !!}">
                                    {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->buyer_quote_item_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('seller_post_item_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->seller_post_item_id, array('id' => 'seller_post_item_id_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId,
                                                $exactDispatchDate, array('id' =>'buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId,
                                                $exactDeliveryDate, array('id' =>'buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_counter_offer_seller_from_date_'.$buyerQuoteId,
                                                $sellerpost->from_date, array('id' =>'buyer_counter_offer_seller_from_date_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('buyer_counter_offer_seller_to_date_'.$buyerQuoteId,
                                                $sellerpost->to_date, array('id' =>'buyer_counter_offer_seller_to_date_'.$buyerQuoteId)) !!}
                                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteId,$Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
				<div class="clearfix"></div>
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            {{--*/ $buyerQuoteId = $buyerQuoteSellerPriceId /*--}}
                            
                            @if($serviceId==ROAD_TRUCK_HAUL || $serviceId==ROAD_TRUCK_LEASE )
                                {{--*/ $form_ext = '_th' /*--}}
                                {{--*/ $form_partial_name = 'buyer_truckhaul_booknow' /*--}}
                            @else
                                {{--*/ $form_ext = '' /*--}}
                                {{--*/ $form_partial_name = 'buyer_booknow' /*--}}
                            @endif

                            {!! Form::open(array('url' => '#', 'id' => 'addbuyerpostcounteroffer'.$form_ext, 'name' => 'addbuyerpostcounteroffer'.$form_ext)) !!}
                                @include('partials.'.$form_partial_name)
                            {!! Form::close() !!}
                            <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                            <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                        </div>
                    </div>
                </div>
			</div>
		</div>
		
		{{--*/ $commercial=1 /*--}}
		<input type="hidden" name="commerical_type" id="commerical_type" value="{{$commercial}}">
		@if($commercial==1)
		@include('partials.gsa_booknow')
		@endif
	@include('partials.footer')

@endsection