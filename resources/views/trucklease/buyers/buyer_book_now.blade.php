@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')


@if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
    {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
@else
    {{--*/ $countQuotes = 0 /*--}}
@endif

@if(isset($arrayBuyerCounterOffer) && !empty($arrayBuyerCounterOffer))
    @foreach ($arrayBuyerCounterOffer as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $buyer_quote_id = $data->buyer_quote_id /*--}}
        
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $priceType = $data->price_type /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
        {{--*/ $lease_term = $data->lease_term /*--}}
        
        {{--*/ $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :$data->from_date /*--}}
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $buyer_quote_id = '' /*--}}
    
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $priceType = '' /*--}}
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

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">

			<div class="container">
				@include('partials.content_top_navigation_links')
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Transaction - {!! $transactionId !!}</h1></span>
				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
                    @if($postStatus == '2')
                        @if(isset($quoteAccessType) && $quoteAccessType=='Private' )
                            <a href="{{ url('editbuyerquote/'. $buyer_quote_id .'/'. $id) }}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
                        @endif
                        <a href="#" class="delete-icon" data-id="{!! $id !!}" id="cancel_buyer_counter_offer_enquiry"><i class="fa fa-trash red" title="Delete"></i></a>
                    @endif
					<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
				</span>

				<!-- Search Block Starts Here -->

				<div class="filter-expand-block">
                    <div class="search-block inner-block-bg margin-bottom-less-1">
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
                                    {!! $dispatchDate !!}
                                </span>
                            </div>
                            <div class="col-md-6 padding-none">
                                <p class="search-head">To Date</p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
    <!--								05 Oct 2015-->{!! $deliveryDate !!}
                                </span>
                            </div>
                        </div>
                        
                        
                        <div>
                            <p class="search-head">Vehicle Type</p>
                            <span class="search-result">{!! $vehicleType !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Lease Term</p>
                            <span class="search-result">{!! $lease_term !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Price</p>
                            <span class="search-result">{!! $priceType !!}</span>
                        </div>
                        <div class="text-right filter-details">
                            @if(isset($privateSellerNames) && !empty($privateSellerNames[0]->username))
                                <!--<a href="#" class="transaction-details-expand">+ Details</a>-->
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
                @if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
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
                                <div>
                                    <p class="search-head">Price</p>
                                    <span class="search-result" data-price="{!! $price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                                        <i class="fa fa-rupee"></i> {!! $price !!}/-
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
                                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteId,
                                                $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}
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
                            {!! Form::open(array('url' => '#', 'id' => 'addbuyerpostcounteroffer_th', 'name' => 'addbuyerpostcounteroffer')) !!}
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