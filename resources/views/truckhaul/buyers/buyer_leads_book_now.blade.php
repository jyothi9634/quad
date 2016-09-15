@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')


@if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
    {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
@else
    {{--*/ $countQuotes = 0 /*--}}
@endif
{{--*/ $units = 'MT' /*--}}
@if(isset($arrayBuyerCounterOffer) && !empty($arrayBuyerCounterOffer))
    @foreach ($arrayBuyerCounterOffer as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $buyer_quote_id = $data->buyer_quote_id /*--}}
        {{--*/ $loadType = $data->load_type /*--}}
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $priceType = $data->price_type /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
        {{--*/ $quantity = $data->quantity /*--}}
        {{--*/ $units = $data->units /*--}}
        @if(isset($data->is_dispatch_flexible) && $data->is_dispatch_flexible==1)
        <?php  $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :date("Y-m-d", strtotime($data->dispatch_date . ' -3 day')); ?>
        @else
        <?php  $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :$data->dispatch_date; ?>
        @endif
            {{--*/ $exactDispatchDate = ($data->dispatch_date == '0000-00-00') ? '' : $data->dispatch_date /*--}}
            
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $buyer_quote_id = '' /*--}}
    {{--*/ $loadType = '' /*--}}
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $priceType = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $quantity = 'NA' /*--}}
    {{--*/ $units = 'MT' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    
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
                 <!-- Page top navigation Starts Here-->
               
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Transaction - {!! $transactionId !!}</h1></span>

				<div class="filter-expand-block">
                    <div class="search-block inner-block-bg margin-bottom-less-1">
                        <div class="from-to-area">
                            <span class="search-result">
                                <i class="fa fa-map-marker"></i>
                                <span class="location-text">{!! $fromCity !!} to {!! $toCity !!}</span>
                            </span>
                        </div>
                        <div class="date-area">
                            <div class="col-md-6 padding-none">
                                <p class="search-head">Dispatch Date</p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
                                    {!! $dispatchDate !!}
                                </span>
                            </div>
                            
                        </div>
                        <div>
                            <p class="search-head">Load Type</p>
                            <span class="search-result">{!! $loadType !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Quantity</p>
                            <span class="search-result">{!! $quantity !!} {!! $units !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Vehicle Type</p>
                            <span class="search-result">{!! $vehicleType !!}</span>
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
                @if(isset($sellerDetailsLeads) && !empty($sellerDetailsLeads))
                    @foreach ($sellerDetailsLeads as $sellerData)
                    {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                    @if($buyerQuoteForLeadId == $buyerQuoteSellerPriceId)
                        <div class="search-block inner-block-bg margin-bottom-less-1">
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
                                <p class="search-head">Transit Time</p>
                                <span class="search-result">
                                    {!! $sellerData->transitdays !!} {!! $sellerData->units !!}
                                </span>
                            </div>
                            <div>
                                <p class="search-head">Price</p>
                                <span class="search-result">
                                    <i class="fa fa-rupee"></i> 
                                    {{--*/   $noofloads = 1 /*--}}
									{!! $price = $sellerData->price !!}   /-                                    
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('leads_seller_post_item_id_'.$buyerQuoteForLeadId, $sellerData->id, array('id' => 'leads_seller_post_item_id_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_post_seller_id_'.$buyerQuoteForLeadId, $sellerData->seller_id, array('id' => 'buyer_leads_post_seller_id_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId, Auth::User()->id, array('id' => 'buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId,
                                            $exactDispatchDate, array('id' =>'buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId,
                                            $sellerData->from_date, array('id' =>'buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId,
                                            $sellerData->to_date, array('id' =>'buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('fdispatch-date_'.$buyerQuoteForLeadId,
                                                $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('buyer_leads_price'.$buyerQuoteForLeadId,
                                                $sellerData->price, array('id' =>'buyer_leads_price'.$buyerQuoteForLeadId)) !!}
                                {!! Form::hidden('cancel_buyer_counter_offer_enquiry',
                                                $id, array('id' =>'cancel_buyer_counter_offer_enquiry','data-id' =>$id)) !!}                
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
                            {!! Form::open(array('url' => '#', 'id' => 'TH-buyer-leads-booknow', 'name' => 'ftl-buyer-leads-booknow')) !!}
                                @include('partials.buyer_truckhaul_booknow')
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