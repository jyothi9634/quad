@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content')
{{--*/ $serviceId = Session::get('service_id') /*--}}
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
        {{--*/ $name = $data->username /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        @if(isset($data->is_door_pickup) && isset($data->is_door_delivery))
        {{--*/ $isDoorPickup = ($data->is_door_pickup == 1) ? 'Yes' : 'No' /*--}}
        {{--*/ $idDoorDelivery = ($data->is_door_delivery == 1) ? 'Yes' : 'No' /*--}}
        @endif
        {{--*/ $isCancelled = $data->lkp_post_status_id /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
        @if(isset($data->is_dispatch_flexible) && $data->is_dispatch_flexible==1)
        <?php  $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :date("Y-m-d", strtotime($data->dispatch_date . ' -3 day')); ?>
        @else
        <?php  $Dispatch_Date = ($data->dispatch_date == '0000-00-00') ? '' :$data->dispatch_date; ?>
        @endif
        {{--*/ $exactDispatchDate = ($data->dispatch_date == '0000-00-00') ? '' : $data->dispatch_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->delivery_date == '0000-00-00') ? '' : $data->delivery_date /*--}}

        @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
        {{--*/ $product_made= $data->product_made /*--}}
        {{--*/ $shipment_type = $data->shipment_type /*--}}
        {{--*/ $sender_identity = $data->sender_identity /*--}}
        {{--*/ $ie_code = $data->ie_code /*--}}
        @endif

        @if($serviceId == COURIER)
        {{--*/ $courier_delivery_type= $data->courier_delivery_type /*--}}
        {{--*/ $courier_type = $data->courier_type /*--}}
        @endif
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $buyer_quote_id = '' /*--}}
    {{--*/ $name = '' /*--}}
    {{--*/ $quoteAccessType = '' /*--}}
    {{--*/ $isDoorPickup = '' /*--}}
    {{--*/ $idDoorDelivery = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $product_made= '' /*--}}
    {{--*/ $shipment_type = '' /*--}}
    {{--*/ $sender_identity = '' /*--}}
    {{--*/ $ie_code = '' /*--}}
    {{--*/ $courier_delivery_type = '' /*--}}
    {{--*/ $courier_type = '' /*--}}
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
    {{--*/ $deliveryDate = '' /*--}}
@endif
@if(isset($dispatchDate) && !empty($dispatchDate))
    {{--*/ $dispatchDate = $dispatchDate /*--}}
@else
    {{--*/ $dispatchDate = '' /*--}}
@endif

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
		<!-- Inner Menu Ends Here -->
    <div class="main">
        <div class="container">
            @if (Session::has('cancelsuccessmessage'))
                <div class="flash alert-info">
                    <p class="text-success col-sm-12 text-center flash-txt-counterofer">{{
                        Session::get('cancelsuccessmessage') }}</p>
                </div>
            @endif
            {{--*/ $str='' /*--}}
            {{--*/ $str_service='' /*--}}
            @if($serviceId == ROAD_PTL)
                {{--*/ $str_service="Lessthan Truck Load" /*--}}
            @elseif($serviceId == RAIL)
                {{--*/ $str_service="Rail" /*--}}
            @elseif($serviceId == AIR_DOMESTIC)
                {{--*/ $str_service="Air Domestic" /*--}}
            @elseif($serviceId == AIR_INTERNATIONAL)
                {{--*/ $str_service="Air International" /*--}}
            @elseif($serviceId == OCEAN)
                {{--*/ $str_service="Ocean" /*--}}
            @elseif($serviceId == COURIER)
                {{--*/ $str_service="Courier" /*--}}
            @endif
            <!-- Page top navigation Starts Here-->
            
            <div class="clearfix"></div>
            <span class="pull-left"><h1 class="page-title">Spot Transaction - {{ $transactionId }}</h1></span>
<!--             <span class="pull-right"> -->
<!--                 <a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a> -->
<!--                 @if($postStatus == '2') -->
<!--                     @if(isset($quoteAccessType) && $quoteAccessType=='Private' ) -->
<!--                         <a href="{{ url('editseller/'. $buyer_quote_id) }}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a> -->
<!--                     @endif -->
<!--                     <a class="delete-icon" id="ptl_cancel_buyer_counter_offer_enquiry" data-id="{!! $id !!}"  href="#"><i class="fa fa-trash red" title="Delete"></i></a> -->
<!--                 @endif -->
<!--                 <a href="{{ url('buyerposts/') }}" class="back-link1">Back to Posts</a> -->
<!--             </span> -->
            <!-- Search Block Starts Here -->
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
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Delivery Date</p>
                            <span class="search-result">
                                <i class="fa fa-calendar-o"></i>
                                @if($deliveryDate== "0000-00-00" || $deliveryDate== "" )
                                    NA
                                @else
                                    {!! $deliveryDate !!}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="search-head">Buyer Name</p>
                        <span class="search-result">{!! $name !!}</span>
                    </div>
                    @if($serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN && $serviceId!=COURIER)
                        <div>
                            <p class="search-head">Door Pickup</p>
                            <span class="search-result">{!! $isDoorPickup !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Door Delivery</p>
                            <span class="search-result">{!! $idDoorDelivery !!}</span>
                        </div>
                    @endif
                    @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                        <div>
                            <p class="search-head">Shipment Type</p>
                            <span class="search-result">
                                @if($shipment_type)
                                    {!! $shipment_type!!}
                                @else 
                                    N.A.
                                @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Sender Identity</p>
                            <span class="search-result">
                                @if($sender_identity)
                                    {!! $sender_identity!!}
                                @else 
                                    N.A.
                                @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">IE Code</p>
                            <span class="search-result">
                                @if($ie_code)
                                    {!! $ie_code!!}
                                @else 
                                    N.A.
                                @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Product Made</p>
                            <span class="search-result">
                                @if($product_made)
                                    {!! $product_made!!}
                                @else
                                    N.A.
                                @endif
                            </span>
                        </div>
                    @endif
                    @if($serviceId == COURIER)
                        <div>
                            <p class="search-head">Destination Type</p>
                            <span class="search-result">
                                @if($courier_delivery_type)
                                    {!! $courier_delivery_type!!}
                                @else 
                                    N.A.
                                @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Courier Type</p>
                            <span class="search-result">
                                @if($courier_type)
                                    {!! $courier_type!!}
                                @else 
                                    N.A.
                                @endif
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="search-head">Status</p>
                        <span class="search-result">{!! $quoteAccessType !!}</span>
                    </div>
                    <div>
                        <p class="search-head">Documents</p>
                        <span class="search-result">0</span>
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
            @if(isset($sellerDetailsLeads) && !empty($sellerDetailsLeads))
                @foreach ($sellerDetailsLeads as $sellerData)
                    {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                     @if($buyerQuoteForLeadId == $buyerQuoteSellerPriceId)
                     <div class="search-block inner-block-bg margin-bottom-less-1">
                        <div>
                            <p class="search-head">Vendor Name</p>
                            <span class="search-result">
                                {!! $sellerData->username !!}
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
                            <span class="search-result" data-price="{!! $_REQUEST['price'] !!}" id="ptl_buyer_leads_post_price_{!! $buyerQuoteForLeadId !!}">
                                <i class="fa fa-rupee"></i> 
                                @if(isset($_REQUEST['price'])){{$_REQUEST['price']}}@endif
                                
                                @if(isset($_REQUEST['price']))
                                    /-
                                @endif
                            </span>
                        </div>

                        <div class="empty-div"></div>
                    </div>
                    {!! Form::hidden('ptl_buyer_leads_seller_post_item_id_'.$buyerQuoteForLeadId, $sellerData->id, array('id' => 'ptl_buyer_leads_seller_post_item_id_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_buyer_leads_post_seller_id_'.$buyerQuoteForLeadId, $sellerData->seller_id, array('id' => 'ptl_buyer_leads_post_seller_id_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId, Auth::User()->id, array('id' => 'ptl_buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId)) !!}

                    {!! Form::hidden('ptl_buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId, 
                                $exactDispatchDate, array('id' =>'ptl_buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId, 
                                    $exactDeliveryDate, array('id' =>'ptl_buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId,
                                        $sellerData->from_date, array('id' =>'ptl_buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId,
                                        $sellerData->to_date, array('id' =>'ptl_buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteForLeadId,
                                    $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteForLeadId)) !!}
                    {!! Form::hidden('ptl_cancel_buyer_counter_offer_enquiry', $id, array('id' => 'ptl_cancel_buyer_counter_offer_enquiry','data-id'=>$id)) !!}                
                @endif
                @endforeach
            @endif
            <!-- Search Block Ends Here -->

            <div class="col-md-12 padding-none">
                <div class="main-inner"> 
                    <!-- Right Section Starts Here -->
                    <div class="main-right">
                        {{--*/ $buyerQuoteId = $buyerQuoteSellerPriceId /*--}}
                        {!! Form::open(array('url' => '#', 'id' => 'ltl-buyer-leads-booknow', 'name' => 'ltl-buyer-leads-booknow')) !!}
                            @include('partials.buyer_booknow')
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