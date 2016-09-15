@inject('commonComponent', 'App\Components\CommonComponent')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@extends('app')
@section('content')
{{--*/ $serviceId = Session::get('service_id') /*--}}
@if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
    {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
@else
    {{--*/ $countQuotes = 0 /*--}}
@endif

@if(!empty($_REQUEST) && isset($_REQUEST['type']) )
    {{--*/ $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'quotes' /*--}}
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
{{--*/ $url = url().'/buyerbooknow/' /*--}}
{{--*/ $urlForLeads = url().'/buyerbooknowforleads/' /*--}}
        <!-- Header Starts Here -->
@include('partials.page_top_navigation')

@if(isset($arrayBuyerCounterOffer[0]->from_location_id) && !empty($arrayBuyerCounterOffer[0]->from_location_id))
 {{--*/  $fromLocationId = $arrayBuyerCounterOffer[0]->from_location_id /*--}}
@else
 {{--*/  $fromLocationId = ''/*--}}
@endif

@if(isset($arrayBuyerCounterOffer[0]->to_location_id) && !empty($arrayBuyerCounterOffer[0]->to_location_id))
 {{--*/  $toLocationId = $arrayBuyerCounterOffer[0]->to_location_id /*--}}
@else
 {{--*/  $toLocationId = ''/*--}}
@endif

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@elseif(!str_contains("buyerposts",URL::previous()))
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif  

{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$id,$fromLocationId,$toLocationId,$arrayBuyerCounterOffer[0]->is_commercial); /*--}}
{{--*/ $docCount = count($docs_buyer) /*--}}
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
        @include('partials.content_top_navigation_links')
            <div class="clearfix"></div>
            <span class="pull-left"><h1 class="page-title">Spot Transaction - {{ $transactionId }}</h1></span>
            <span class="pull-right">
                <a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
                @if($postStatus == '2')
                    @if(isset($quoteAccessType) && $quoteAccessType=='Private' )
                        <a href="{{ url('editseller/'. $buyer_quote_id) }}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
                    @endif
                    <!-- a class="delete-icon" id="ptl_cancel_buyer_counter_offer_enquiry" data-id="{!! $id !!}"  href="#"><i class="fa fa-trash red" title="Delete"></i></a -->
                    <a href="#" class="delete-icon" data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid({!! $id !!})'><i class="fa fa-trash red" title="Delete"></i></a>
                @endif
                <a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
            </span>
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
                    @if($serviceId!=COURIER)
                    <div>
                        <p class="search-head">Buyer Name</p>
                        <span class="search-result">{!! $name !!}</span>
                    </div>
                    @endif
                    @if($serviceId == COURIER)
                    <div>
                        <p class="search-head">Destinaion Type</p>
                        <span class="search-result">{!! $PostDeliveryType !!}</span>
                    </div>
                    <div>
                        <p class="search-head">Courier Type</p>
                        <span class="search-result">{!! $PostCourierType !!}</span>
                    </div>
                    @endif
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
                    @endif
                    <div class="text-right filter-details">
                            <div class="info-links">
                                <a class="transaction-details-expand"><span class="show-icon">+</span>
                                    <span class="hide-icon">-</span> Details
                                </a>
                            </div>
                    </div>
                </div>
                <div class="col-md-12 show-data-div"></div>
                <!-- Search Block Ends Here -->
                <!--toggle div starts-->
                <div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
                    <div class="expand-block">
                        <div class="col-md-12">
                        @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                            <div class="col-md-3 form-control-fld padding-left-none">
                                <p class="search-head">Sender Identity</p>
                                <span class="search-result">
                                    @if($sender_identity)
                                        {!! $sender_identity!!}
                                    @else 
                                        N.A.
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-2 form-control-fld padding-left-none">
                                <p class="search-head">IE Code</p>
                                <span class="search-result">
                                    @if($ie_code)
                                        {!! $ie_code!!}
                                    @else 
                                        N.A.
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-2 form-control-fld padding-left-none">
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
                        <div class="col-md-2 form-control-fld padding-left-none">
                            <p class="search-head">Posted For</p>
                            <span class="search-result">{!! $quoteAccessType !!}</span>
                        </div>
                        <div class="col-md-2 form-control-fld padding-left-none">
                            <p class="search-head">Documents</p>
                            <span class="search-result">0</span>
                        </div>
                        @if(isset($privateSellerNames) && !empty($privateSellerNames[0]->username))
                            <div class="col-md-12 padding-left-none data-fld">
                                <span class="data-head">Post Private</span>
                                @if(isset($privateSellerNames) && !empty($privateSellerNames))
                                    @foreach($privateSellerNames as $key=>$privateSellerName)
                                        <span class="data-value margin-bottom-5">{!! $privateSellerName->username !!}</span>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!--toggle div ends-->
            </div>

            <!-- Search Block Ends Here -->

            <div class="col-md-12 padding-none">
                <div class="main-inner"> 
                    <!-- Right Section Starts Here -->
                    <div class="main-right">
                        <!-- Table Starts Here -->
                        <div class="table-div table-style1 padding-none">
                            <!-- Table Head Starts Here -->
                            <div class="table-heading inner-block-bg">
                            @if($serviceId!=COURIER)
                                <div class="col-md-3 padding-left-none">Load Type<i class="fa fa-caret-down"></i></div>
                                <div class="col-md-3 padding-left-none">Package<i class="fa fa-caret-down"></i></div>
                                @endif
                                <div class="col-md-2 padding-left-none">Weight<i class="fa fa-caret-down"></i></div>
                                <div class="col-md-2 padding-left-none">Volume<i class="fa fa-caret-down"></i></div>
                                <div class="col-md-2 padding-left-none">No of packages<i class="fa fa-caret-down"></i></div>
                                @if($serviceId == COURIER)
                                <div class="col-md-2 padding-left-none">Package Value<i class="fa fa-caret-down"></i></div>
                                @endif
                                <!--<div class="col-md-1 padding-left-none"></div>-->
                            </div>
                            <!-- Table Head Ends Here -->
                            {!! Form::hidden('service_id',$serviceId ,['id' =>'service_id', 'class' => 'service_id']) !!}
                            @if($serviceId==ROAD_PTL || $serviceId==RAIL)
                                {{--*/ $str=' CFT' /*--}}
                            @elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==COURIER)
                                {{--*/ $str=' CCM' /*--}}
                            @elseif($serviceId==OCEAN)
                                {{--*/ $str=' CBM' /*--}}
                            @endif
                            <div class="table-data">
                                <!-- Table Row Starts Here -->
                                @if(isset($arraySellerDetails) && !empty($arraySellerDetails))
                                    @foreach($arraySellerDetails as $key=>$sellersQuotesDetails)
                                        <div class="table-row inner-block-bg">
                                        @if($serviceId!=COURIER)
                                            <div class="col-md-3 padding-left-none">{!! $sellersQuotesDetails->load_type !!}</div>
                                            <div class="col-md-3 padding-left-none">{!! $sellersQuotesDetails->packaging_type_name !!}</div>
                                            @endif
                                            <div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->buyerQuoteUnits !!}<br class="hidden-lg hidden-md hidden-sm"> {{ $sellersQuotesDetails->weight_type }}</div>
                                            <div class="col-md-2 padding-left-none">
                                            @if($sellersQuotesDetails->calculated_volume_weight == 0)
                                            NA
                                            @else
                                            {!! round($sellersQuotesDetails->calculated_volume_weight,4) !!}{!! $str !!}
                                             @endif
                                            </div>
                                            <div class="col-md-2 padding-left-none">{!! $commonComponent->number_format($sellersQuotesDetails->number_packages,false) !!}</div>
                                            @if($serviceId==COURIER)
                                            <div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->package_value !!}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Table Row Ends Here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 padding-none">
                <div class="main-inner"> 
                    <!-- Right Section Starts Here -->
                    <div class="main-right">
                        <div class="pull-left">
                            <div class="info-links">
                                <a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ltl-buyer-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
                                <a href="#" class="{{($type=="quotes")?"active":""}} active tabs-showdiv" data-showdiv="ltl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes<span class="badge">{!! $countQuotes !!}</span></a>
                                <a href="#" class="{{($type=="leads")?"active":""}} tabs-showdiv" data-showdiv="ltl-buyer-leads"><i class="fa fa-thumbs-o-up"></i> Leads<span class="badge">{!! count($sellerDetailsLeads) !!}</span></a>
                                <a href="#" class="tabs-showdiv" data-showdiv="ltl-buyer-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
                                <a href="#" class="{{($type=="documentation")?"active":""}} tabs-showdiv" data-showdiv="ltl-buyer-documentation"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">{{$docCount}}</span></a>
                            </div>
                        </div>
                        <div class="col-md-3 pull-right compare-fld">
                            @if($countQuotes!=0)
                                <div class="normal-select comparision_types_div" data-buyerquoteid = "{{ $id }}">
                                    {!! Form::select('buyer_post_counter_offer_comparision_types', $buyerPostCounterOfferComparisonTypes,
                                        $comparisonType, ['id' => 'buyer_post_counter_offer_comparision_types', 'class' => 'selectpicker'])!!}
                                </div>
                            @else
                                <div class="pull-right">No Quotes to Compare</div>
                            @endif
                        </div>
                        <!-- Table Starts Here -->
                        
                        <div class="table-div margin-none">
                            {{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
                            <div id="ltl-buyer-messages" class="tabs-group" {{$msg_style}}>
                               {!! $allMessagesList['grid'] !!}
                            </div>
                            <div id="ltl-buyer-marketanalytics" class="tabs-group" style="display: none">
                                <div class="table-heading inner-block-bg">
                                    No data available
                                </div>
                            </div>
                            {{--*/ $docu_style   =($type=="documentation")?"style=display:block":"style=display:none" /*--}}                                                         
                            <div id="ltl-buyer-documentation" class="tabs-group" {{$docu_style}}>
                                <div class="table-heading inner-block-bg">
                                   @if($docCount>0)                                     
                                        <div class="col-sm-4 padding-right-none">
                                            <h3>List of documents </h3> 
                                            <ul class="popup-list">                                               
                                                
                                                @foreach($docs_buyer as $doc)
                                                <li>{{$doc}}</li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                       @else
                                       No Documents Found
                                       @endif
                                </div>
                            </div>
                            {{--*/ $quotes_style   =($type=="quotes")?"style=display:block":"style=display:none" /*--}}
                            <div id="ltl-buyer-quotes" class="tabs-group" {{$quotes_style}}>
                                <!-- Table Head Starts Here -->
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-3 padding-left-none">
                                        <input type="checkbox" id="ptl_select_all_name"/><span class="lbl padding-8"></span>
                                        Vendor Name<i class="fa fa-caret-down"></i>
                                    </div>
                                    <div class="col-md-3 padding-left-none">Transit Time<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-2 padding-left-none">Price (<i class="fa fa-inr fa-1x"></i>)<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-2 padding-left-none">
                                        @if(!empty($comparisonType))
                                            Ranking
                                        @endif
                                    </div>
                                    <div class="col-md-2 padding-left-none"></div>
                                </div>
                                <!-- Table Head Ends Here -->
                                <div class="table-data">
                                    {!! Form::open(array('url' => '#', 'id' => 'addptlbuyerpostcounteroffer', 'name' => 'addptlbuyerpostcounteroffer')) !!}
                                        @if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
                                            {!! Form::hidden('ptl_buyer_post_counter_offer_id', $arrayBuyerQuoteSellersQuotesPrices[0]->id, array('id' =>'buyer_post_counter_offer_id')) !!}
                                            @foreach($arrayBuyerQuoteSellersQuotesPrices as $key=>$buyerQuoteSellersQuotesDetails)
                                                {{--*/ $buyerQuoteId = $buyerQuoteSellersQuotesDetails->id /*--}}
                                                {{--*/ $sp_item_id = $buyerQuoteSellersQuotesDetails->seller_post_item_id /*--}}
                                                 @if($serviceId!=COURIER)
                                                @if(!empty($buyerQuoteSellersQuotesDetails->load_type))
                                                    {{--*/ $loadType = $buyerQuoteSellersQuotesDetails->load_type /*--}}
                                                @else
                                                    {{--*/ $loadType = '' /*--}}
                                                @endif
                                                @endif
                                                {{--*/ $priceval = 'initial_quote_price' /*--}}
                                                @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->final_quote_price /*--}}
                                                     {{--*/ $priceval = 'final_quote_price' /*--}}
                                                @elseif($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->counter_quote_price /*--}}
                                                     {{--*/ $priceval = 'counter_quote_price' /*--}}
                                                @elseif($buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->initial_quote_price /*--}}
                                                    {{--*/ $priceval = 'initial_quote_price' /*--}}
                                                @else
                                                    {{--*/ $price = '' /*--}}
                                                @endif
                                                <!-- Table Row Starts Here -->
                                                <div class="table-row inner-block-bg">
                                                    <div class="col-md-3 padding-left-none">
                                                        <input class="ptl_select_name quotecheck" id="ptl_select_name_{!! $buyerQuoteId !!}" type="checkbox" value="{!! $buyerQuoteId !!}">
                                                        <span class="lbl padding-8"></span>
                                                        {!! $buyerQuoteSellersQuotesDetails->username !!}
                                                        {{--*/ $seller_id = $buyerQuoteSellersQuotesDetails->seller_id /*--}}
                                                        <div class="red rating-margin">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 padding-none">{!! $buyerQuoteSellersQuotesDetails->initial_transit_days !!} {!! $buyerQuoteSellersQuotesDetails->units !!}</div>
                                                    <div class="col-md-2 padding-none" data-price="{!! $price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                                                        {!! $commonComponent->moneyFormat($price,true) !!}
                                                        @if(!empty($price))
                                                            /-
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 padding-none">
                                                    @if(!empty($comparisonType))
                                                        {!! $buyerQuoteSellersQuotesDetails->rank !!}
                                                    @endif
                                                    </div>
                                                    <input type="hidden" name="priceval" id="priceval" value="{!! $priceval !!}">
                                                    <div class="col-md-2 padding-none">
                                                        @if($commonComponent->CheckCartItem($id)==1)
                                                            @if($buyerQuoteSellersQuotesDetails->lkp_post_status_id != CANCELLED && $buyerQuoteSellersQuotesDetails->lkp_post_status_id != CLOSED && $buyerQuoteSellersQuotesDetails->lkp_post_status_id != BOOKED)
                                                                @if($buyerQuoteSellersQuotesDetails->seller_acceptence == 1 || 
                                                                        ($buyerQuoteSellersQuotesDetails->counter_quote_price == '0.00' && $buyerQuoteSellersQuotesDetails->initial_quote_price != '0.00'))
                                                                    <input type="button" class="btn red-btn pull-right submit-data ptl_booknow_slide buyer_book_now" data-url="{{ $url.$id.'/'.$buyerQuoteId }}"
                                                                         data-booknow_list="{!! $buyerQuoteId !!}" id = "buyer_book_now_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}" value="Book Now" />
                                                                @endif
                                                                @if($buyerQuoteSellersQuotesDetails->initial_quote_price != '0.00' && $buyerQuoteSellersQuotesDetails->counter_quote_price == '0.00')
                                                                    <input type="button" class="btn red-btn pull-right ptl_buyer_submit_counter_offer"
                                                                        id = "buyer_submit_counter_offer_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}" value="Counter Offer"/>
                                                                @endif
                                                                @if($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.00' && $buyerQuoteSellersQuotesDetails->final_quote_price == '0.00')
                                                                    <input type="button" class="btn red-btn pull-right ptl_buyer_submit_counter_offer"
                                                                        id = "buyer_submit_counter_offer_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}" value="Counter Offer Submitted"/>
                                                                @endif
                                                                @if($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.00' && $buyerQuoteSellersQuotesDetails->final_quote_price != '0.00')
                                                                    <input type="button" class="btn red-btn pull-right ptl_buyer_submit_counter_offer"
                                                                        id = "buyer_submit_counter_offer_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}" value="Final Quote Received"/>
                                                                @endif
                                                            @endif
                                                        @elseif($commonComponent->CheckCart($id,$sp_item_id)==1)
                                                                <button class="btn red-btn pull-right buyer_submit_counter_offer">Booked</button>
                                                        @endif
                                                    </div>
                                                    <div class="pull-right text-right">
                                                        <div class="info-links">
                                                            <a href="#" class="red underline_link new_message" data-transaction_no="{{$buyerQuoteSellersQuotesDetails->transaction_no}}" data-userid="{{$buyerQuoteSellersQuotesDetails->seller_id}}" data-id="{{$id}}" data-buyerquoteitemid="{{ $buyerQuoteSellersQuotesDetails->seller_post_item_id }}"><i class="fa fa-envelope-o"></i></a>
                                                        </div>
                                                    </div>

<!--                                                    <div class="col-md-12 padding-none show-data-div">-->
                                                    <div class="col-md-12 padding-none" id="ptl_counter_offer_details_{{ $buyerQuoteId }}" style='display:none'>
                                                        <!-- Table Starts Here -->
                                                        <div class="table-div table-style1 padding-none">
                                                            <!-- Table Head Starts Here -->
                                                            <div class="table-heading inner-block-bg">
                                                                @if($serviceId!=COURIER)
                                                                    <div class="col-md-3 padding-left-none">Load Type<i class="fa fa-caret-down"></i></div>
                                                                    <div class="col-md-2 padding-left-none">Package<i class="fa fa-caret-down"></i></div>
                                                                @else
                                                                    <div class="col-md-3 padding-left-none">Courier Type<i class="fa fa-caret-down"></i></div>
                                                                    <div class="col-md-2 padding-left-none">Courier Delivery Types<i class="fa fa-caret-down"></i></div>
                                                                @endif
                                                                <div class="col-md-2 padding-left-none">Weight {{ $buyerQuoteSellersQuotesDetails->weight_type }}<i class="fa fa-caret-down"></i></div>
                                                                <div class="col-md-2 padding-left-none">Volume<i class="fa fa-caret-down"></i></div>
                                                                <div class="col-md-2 padding-left-none">No of packages<i class="fa fa-caret-down"></i></div>
                                                                <div class="col-md-1 padding-left-none"></div>
                                                            </div>
                                                            <!-- Table Head Ends Here -->
                                                            <div class="table-data">
                                                                <!-- Table Row Starts Here -->
                                                                @if(isset($arraySellerDetails) && !empty($arraySellerDetails))
                                                                    @foreach($arraySellerDetails as $key=>$buyerQuoteSellersQuotesDetails1)
                                                                        <div class="table-row inner-block-bg">
                                                                            @if($serviceId!=COURIER)
                                                                                <div class="col-md-3 padding-left-none">{{ $buyerQuoteSellersQuotesDetails1->load_type }}</div>
                                                                                <div class="col-md-2 padding-left-none">{{ $buyerQuoteSellersQuotesDetails1->packaging_type_name }}</div>
                                                                            @else
                                                                                <div class="col-md-3 padding-left-none">{{ $buyerQuoteSellersQuotesDetails1->courier_type }}</div>
                                                                                <div class="col-md-2 padding-left-none">{{ $buyerQuoteSellersQuotesDetails1->courier_delivery_type }}</div>
                                                                            @endif
                                                                            <div class="col-md-2 padding-left-none">{{ $buyerQuoteSellersQuotesDetails1->buyerQuoteUnits }} {{ $buyerQuoteSellersQuotesDetails1->weight_type }}</div>
                                                                            <div class="col-md-2 padding-left-none">{{ round($buyerQuoteSellersQuotesDetails1->calculated_volume_weight,4) }} {{ $str }}</div>
                                                                            <div class="col-md-2 padding-left-none">{!! $buyerQuoteSellersQuotesDetails1->number_packages !!}</div>
                                                                            <div class="col-md-1 padding-none text-center"><a href="#"><i class="fa fa-trash red" title="Delete"></i></a></div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                                <!-- Table Row Ends Here -->
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <!-- Table Starts Here -->
                                                        @if($serviceId != COURIER)
                                                        	<div class="col-md-12 inner-block-bg inner-block-bg1">
                                                        @else
	                                                        @if(empty($buyerQuoteSellersQuotesDetails->counter_conversion_factor))
	                                                        	<div class="col-md-12 inner-block-bg inner-block-bg1">
	                                                        @else
	                                                        	<div>
	                                                        @endif
                                                        @endif
                                                            <!--div class="col-md-3 padding-left-none form-control-fld">
                                                                <span class="padding-top-ext" >Rate per KG: <span id="final_rate_per_kg_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_rate_per_kg, 
                                                                                          $buyerQuoteSellersQuotesDetails->counter_rate_per_kg, 
                                                                                          $buyerQuoteSellersQuotesDetails->final_rate_per_kg) !!}">{!! $commonComponent->moneyFormat(
                                                                                      $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_rate_per_kg, $buyerQuoteSellersQuotesDetails->counter_rate_per_kg, $buyerQuoteSellersQuotesDetails->final_rate_per_kg
                                                                                          )) !!}</span>
                                                                </span>
                                                            </div-->
                                                            @if($serviceId!=COURIER)                                                            
                                                            <!--div class="col-md-3 form-control-fld">
                                                                <span class="padding-top-ext" >
                                                                @if($serviceId!=COURIER)
                                                                    Conversion KG / {{ $str }}
                                                                @else
                                                                    Conversion Factor CCM/KG 
                                                                @endif
                                                                : <span id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_kg_per_cft, 
                                                                                            $buyerQuoteSellersQuotesDetails->counter_kg_per_cft, 
                                                                                            $buyerQuoteSellersQuotesDetails->final_kg_per_cft) !!}">
                                                                                            {!! $commonComponent->moneyFormat($buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_kg_per_cft, 
                                                                                                $buyerQuoteSellersQuotesDetails->counter_kg_per_cft, 
                                                                                                $buyerQuoteSellersQuotesDetails->final_kg_per_cft
                                                                                            )) !!}</span>
                                                                </span>
                                                            </div-->
                                                            @else
                                                            <!--div class="col-md-3 form-control-fld">
                                                                <span class="padding-top-ext" >Conversion Factor : <span id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_conversion_factor, 
                                                                                            $buyerQuoteSellersQuotesDetails->counter_conversion_factor, 
                                                                                            $buyerQuoteSellersQuotesDetails->final_conversion_factor) !!}">
                                                                                            {!! $commonComponent->moneyFormat($buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_conversion_factor, 
                                                                                                $buyerQuoteSellersQuotesDetails->counter_conversion_factor, 
                                                                                                $buyerQuoteSellersQuotesDetails->final_conversion_factor
                                                                                            )) !!}</span>
                                                                </span>
                                                            </div-->
                                                            @endif
                                                           
                                                            @if($serviceId==COURIER)
                                                                {{--*/ $classCounteroffer = 'fivedigitstwodecimals_deciVal'  /*--}}
                                                            @else
                                                                {{--*/ $classCounteroffer = 'fourdigitstwodecimals_deciVal'  /*--}}
                                                            @endif
                                                            
                                                            @if(empty($buyerQuoteSellersQuotesDetails->counter_rate_per_kg) && empty($buyerQuoteSellersQuotesDetails->final_rate_per_kg))
                                                                <div class="col-md-3 form-control-fld">
                                                                    {!! Form::text('ptl_counter_rate_per_kg_'.$buyerQuoteId,$buyerQuoteSellersQuotesDetails->counter_rate_per_kg,
                                                                            array('id'=>'ptl_counter_rate_per_kg_'.$buyerQuoteId, 'class'=>'form-control form-control1 ptl_counter_rate_per_kg numberVal '.$classCounteroffer.'', 
                                                                            'placeholder'=>'Counter Offer Rate per KG')) !!}
                                                                </div>
                                                            @endif
                                                            
                                                            
                                                            @if(empty($buyerQuoteSellersQuotesDetails->final_kg_per_cft) && empty($buyerQuoteSellersQuotesDetails->counter_kg_per_cft))
                                                                <div class="col-md-3 padding-right-none form-control-fld">
                                                                    @if($serviceId != COURIER)
                                                                    	@if($serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL)
                                                                                @if(Session::get('service_id') == AIR_DOMESTIC)
                                                                                {{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                                                @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                                                @endif
                                                                    		{!! Form::text('ptl_conversion_kg_cft_'.$buyerQuoteId, '',
                                                                            	array('id'=>'ptl_conversion_kg_cft_'.$buyerQuoteId, 'class'=>'form-control form-control1 ptl_conversion_kg_cft numberVal fourdigitsfourdecimals_deciVal '.$cls, 
                                                                                    'placeholder'=>'Counter Offer Conversion Kg/'.$str)) !!}
                                                                        @else
                                                                        	{!! Form::text('ptl_conversion_kg_cft_'.$buyerQuoteId, '',
                                                                            	array('id'=>'ptl_conversion_kg_cft_'.$buyerQuoteId, 'class'=>'form-control form-control1 ptl_conversion_kg_cft numberVal fourdigitsthreedecimals_deciVal', 
                                                                                    'placeholder'=>'Counter Offer Conversion Kg/'.$str)) !!}
                                                                        @endif
                                                                                    
                                                                    @else
	                                                                    @if(empty($buyerQuoteSellersQuotesDetails->counter_conversion_factor))
	                                                                    	{!! Form::text('ptl_conversion_kg_cft_'.$buyerQuoteId, '',
	                                                                            array('id'=>'ptl_conversion_kg_cft_'.$buyerQuoteId, 'class'=>'form-control form-control1  numberVal fivedigitstwodecimals_deciVal', 
	                                                                                    'placeholder'=>'Counter Offer Conversion CCM/KG')) !!}
	                                                                    @endif
                                                                    
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            </div>
                                                            
                                                            
                                                                       



                                                            <div class="col-md-3 padding-none text-right pull-right">
                                                                @if(empty($buyerQuoteSellersQuotesDetails->counter_rate_per_kg) || $buyerQuoteSellersQuotesDetails->counter_rate_per_kg == 0)
                                                                    <input type="button" class="btn add-btn ptl_add_buyer_counter_offer_details" 
                                                                       id="counter_offer_submit_button_{{ $buyerQuoteId }}" data-ptl_seller_post_item_id = "{{ $buyerQuoteSellersQuotesDetails->seller_post_item_id }}" data-ptl_booknow_buyer_quoteid = "{{ $buyerQuoteId }}" value="&nbsp; Submit &nbsp;">
                                                                @endif
                                                            </div>

                
                                                        
                    
                                                        @if($buyerQuoteSellersQuotesDetails->initial_quote_price!='0.00')
                                                        
                                                        <div class="col-md-12 form-control-fld margin-top"><b> Seller Quote </b></div>

                                                       
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >Rate per KG:</span> <span class="data-value" id="final_rate_per_kg_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->initial_rate_per_kg !!}">Rs {!! $commonComponent->moneyFormat(
                                                                                      $buyerQuoteSellersQuotesDetails->initial_rate_per_kg) !!} /-</span>
                                                                
                                                            </div>
                                                            @if($serviceId!=COURIER)                                                            
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >
                                                                @if($serviceId!=COURIER)
                                                                    Conversion KG / {{ $str }}
                                                                @else
                                                                    Conversion Factor CCM/KG 
                                                                @endif
                                                                :</span>
                                                                 <span  class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->initial_kg_per_cft !!}">
                                                                                           {!! $buyerQuoteSellersQuotesDetails->initial_kg_per_cft !!}</span>
                                                                
                                                            </div>
                                                            @else
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >Conversion Factor (CCM per KG ): </span><span class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->initial_conversion_factor !!}"> {!! $buyerQuoteSellersQuotesDetails->initial_conversion_factor !!}</span>
                                                            </div>
                                                            @endif
                                                       
        
                                                        <div class="clearfix"></div>
                                                        <div>
                                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 form-control-fld">
                                                                    <span class="data-head"> Pickup (Rs) :</span> 
                                                                    <span class="data-value" id="pick_up_rate_{!! $buyerQuoteId !!}" 
                                                                      data-pickuprateperkg="{!! $buyerQuoteSellersQuotesDetails->initial_pick_up_rupees !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_pick_up_rupees) !!} /-</span>
                                                                </div>
                                                            @endif
                                                            
                                                            
                                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 form-control-fld">
                                                                    <span class="data-head">Delivery (Rs) :</span> 
                                                                    <span class="data-value" id="delivery_charges_{!! $buyerQuoteId !!}" 
                                                                      data-deliverycharges="{!! $buyerQuoteSellersQuotesDetails->initial_delivery_rupees !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_delivery_rupees) !!} /-</span>
                                                                </div>
                                                            @endif
                                                           
                                                            
                                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 padding-right-none form-control-fld">
                                                                    <span class="data-head">ODA (Rs) :</span>
                                                                    <span class="data-value" id="oda_charges_{!! $buyerQuoteId !!}" 
                                                                      data-odacharges="{!! $buyerQuoteSellersQuotesDetails->initial_oda_rupees !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_oda_rupees) !!} /-</span>
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head">Transit Days : </span>
                                                                <span class="data-value">{!! $buyerQuoteSellersQuotesDetails->initial_transit_days !!}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-3 padding-left-none form-control-fld">
                                                                <span class="data-head">Freight Amount (Rs) : </span>
                                                                <span class="data-value" id="freight_charges_{!! $buyerQuoteId !!}" 
                                                                    data-freightcharges="{!! $buyerQuoteSellersQuotesDetails->initial_freight_amount !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_freight_amount) !!} /-
                                                                </span>
                                                            </div>
                                                           
                                                            <div class="col-md-3 padding-left-none form-control-fld margin-bottom-none">
                                                                <span class="data-head">Total Amount (Rs) :</span> 
                                                                <span class="data-value" id="total_charges_{!! $buyerQuoteId !!}" 
                                                                      data-totalcharges="{!! $buyerQuoteSellersQuotesDetails->initial_quote_price !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_quote_price) !!} /-</span>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        @if($buyerQuoteSellersQuotesDetails->counter_quote_price!='0.00')
                                                        <div class="clearfix"></div>
                                                        <div class="col-md-12 padding-left-none form-control-fld margin-top"><b> Counter Offer </b></div>

                                                        <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >Rate per KG:</span> <span class="data-value" id="counter_rate_per_kg_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->counter_rate_per_kg !!}">Rs {!! $commonComponent->moneyFormat(
                                                                                      $buyerQuoteSellersQuotesDetails->counter_rate_per_kg) !!} /-</span>
                                                                
                                                            </div>
                                                            @if($serviceId!=COURIER)                                                            
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >
                                                                @if($serviceId!=COURIER)
                                                                    Conversion KG / {{ $str }}
                                                                @else
                                                                    Conversion Factor CCM/KG 
                                                                @endif
                                                                : </span>
                                                                <span  class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->counter_kg_per_cft !!}">
                                                                                            {!! $buyerQuoteSellersQuotesDetails->counter_kg_per_cft !!} </span>
                                                                
                                                            </div>
                                                            @else
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >Conversion Factor (CCM per KG ): </span><span class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerQuoteSellersQuotesDetails->counter_conversion_factor !!}"> {!! $buyerQuoteSellersQuotesDetails->counter_conversion_factor !!}</span>
                                                                
                                                            </div>
                                                            @endif
                                                       
        
                                                        <div class="clearfix"></div>

                                                        <div>                                                            

                                                            <div class="col-md-3 padding-left-none form-control-fld">
                                                                <span class="data-head">Freight Amount (Rs) : </span>
                                                                <span class="data-value" id="freight_charges_{!! $buyerQuoteId !!}" 
                                                                    data-freightcharges="{!! $buyerQuoteSellersQuotesDetails->counter_freight_amount !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->counter_freight_amount) !!} /-
                                                                </span>
                                                            </div>
                                                           
                                                            <div class="col-md-3 padding-left-none form-control-fld margin-bottom-none">
                                                                <span class="data-head">Total Amount (Rs) :</span> 
                                                                <span class="data-value" id="total_charges_{!! $buyerQuoteId !!}" 
                                                                      data-totalcharges="{!! $buyerQuoteSellersQuotesDetails->counter_quote_price !!}">{!! $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->counter_quote_price) !!} /-</span>
                                                            </div>                                                            
                                                        </div>
                                                        @endif

                                                        @if($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.00' && $buyerQuoteSellersQuotesDetails->final_quote_price != '0.00')
                                                        <div class="clearfix"></div>
                                                        <div class="col-md-12 padding-left-none form-control-fld margin-top"><b> Seller Final Quote </b></div>                                                     
                                                       

                                                        <div class="col-md-3 form-control-fld">
                                                                <span class="data-head">Rate per KG:</span> <span class="data-value" id="final_rate_per_kg_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_rate_per_kg, 
                                                                                          $buyerQuoteSellersQuotesDetails->counter_rate_per_kg, 
                                                                                          $buyerQuoteSellersQuotesDetails->final_rate_per_kg) !!}">{!! $commonComponent->moneyFormat(
                                                                                      $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_rate_per_kg, $buyerQuoteSellersQuotesDetails->counter_rate_per_kg, $buyerQuoteSellersQuotesDetails->final_rate_per_kg
                                                                                          )) !!}/-</span>
                                                                
                                                            </div>


                                                            @if($serviceId!=COURIER)                                                            
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head" >
                                                                @if($serviceId!=COURIER)
                                                                    Conversion KG / {{ $str }}
                                                                @else
                                                                    Conversion Factor CCM/KG 
                                                                @endif
                                                                :</span><span class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_kg_per_cft, 
                                                                                            $buyerQuoteSellersQuotesDetails->counter_kg_per_cft, 
                                                                                            $buyerQuoteSellersQuotesDetails->final_kg_per_cft) !!}">
                                                                                            {!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_kg_per_cft, 
                                                                                                $buyerQuoteSellersQuotesDetails->counter_kg_per_cft, 
                                                                                                $buyerQuoteSellersQuotesDetails->final_kg_per_cft
                                                                                            ) !!} </span>                                                                
                                                            </div>
                                                            @else
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head">Conversion Factor (CCM per KG ): </span><span class="data-value" id="conversion_kg_per_cft_{!! $buyerQuoteId !!}" 
                                                                    data-rateperkg="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_conversion_factor, 
                                                                                            $buyerQuoteSellersQuotesDetails->counter_conversion_factor, 
                                                                                            $buyerQuoteSellersQuotesDetails->final_conversion_factor) !!}">
                                                                                            {!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_conversion_factor, 
                                                                                                $buyerQuoteSellersQuotesDetails->counter_conversion_factor, 
                                                                                                $buyerQuoteSellersQuotesDetails->final_conversion_factor
                                                                                            ) !!}</span>
                                                                </span>
                                                            </div>
                                                            @endif
                                                       
        
                                                        <div class="clearfix"></div>

                                                        @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 form-control-fld">
                                                                    <span class="data-head"> Pickup (Rs) :</span> 
                                                                        <span class="data-value" id="pick_up_rate_{!! $buyerQuoteId !!}" 
                                                                          data-pickuprateperkg="{!! $buyerCommonComponent->getFinalDetails(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_pick_up_rupees,
                                                                                            $buyerQuoteSellersQuotesDetails->final_pick_up_rupees) 
                                                                                        !!}">{!! $commonComponent->moneyFormat(
                                                                                $buyerCommonComponent->getFinalDetails(
                                                                                    $buyerQuoteSellersQuotesDetails->initial_pick_up_rupees,$buyerQuoteSellersQuotesDetails->final_pick_up_rupees
                                                                                    )) !!} /-</span>
                                                                    
                                                                </div>
                                                            @endif
                                                            
                                                            
                                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 form-control-fld">
                                                                <span class="data-head">Delivery (Rs) :</span> 
                                                                    <span class="data-value" id="delivery_charges_{!! $buyerQuoteId !!}" 
                                                                      data-deliverycharges="{!! $buyerCommonComponent->getFinalDetails(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_delivery_rupees,
                                                                                                $buyerQuoteSellersQuotesDetails->final_delivery_rupees) 
                                                                                                !!}">{!! $commonComponent->moneyFormat(
                                                                            $buyerCommonComponent->getFinalDetails(
                                                                                $buyerQuoteSellersQuotesDetails->initial_delivery_rupees,$buyerQuoteSellersQuotesDetails->final_delivery_rupees
                                                                                )) !!} /-</span>
                                                                
                                                                </div>
                                                            @endif
                                                           
                                                            
                                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                <div class="col-md-3 padding-right-none form-control-fld">
                                                                <span class="data-head">ODA (Rs) :</span>
                                                                    <span class="data-value" id="oda_charges_{!! $buyerQuoteId !!}" 
                                                                      data-odacharges="{!! $buyerCommonComponent->getFinalDetails(
                                                                                                $buyerQuoteSellersQuotesDetails->initial_oda_rupees,
                                                                                                $buyerQuoteSellersQuotesDetails->final_oda_rupees) 
                                                                                    !!}">{!! $commonComponent->moneyFormat(
                                                                            $buyerCommonComponent->getFinalDetails(
                                                                                $buyerQuoteSellersQuotesDetails->initial_oda_rupees,$buyerQuoteSellersQuotesDetails->final_oda_rupees
                                                                                )) !!} /-</span>
                                                                
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="col-md-3 form-control-fld">
                                                                <span class="data-head">Transit Days : </span>
                                                                <span class="data-value" id="transitdays_{!! $buyerQuoteId !!}" 
                                                                    data-freightcharges="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_freight_amount, 
                                                                                          $buyerQuoteSellersQuotesDetails->counter_freight_amount, 
                                                                                          $buyerQuoteSellersQuotesDetails->final_freight_amount)
                                                                                  !!}">{!! $buyerCommonComponent->getFinalDetails(
                                                                                $buyerQuoteSellersQuotesDetails->initial_transit_days,$buyerQuoteSellersQuotesDetails->final_transit_days) !!}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-3 padding-left-none form-control-fld">
                                                                <span class="data-head">Freight Amount (Rs) : </span>
                                                                <span class="data-value" id="freight_charges_{!! $buyerQuoteId !!}" 
                                                                    data-freightcharges="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_freight_amount, 
                                                                                          $buyerQuoteSellersQuotesDetails->counter_freight_amount, 
                                                                                          $buyerQuoteSellersQuotesDetails->final_freight_amount)
                                                                                  !!}">{!! $commonComponent->moneyFormat(
                                                                                      $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                          $buyerQuoteSellersQuotesDetails->initial_freight_amount, $buyerQuoteSellersQuotesDetails->counter_freight_amount, $buyerQuoteSellersQuotesDetails->final_freight_amount
                                                                                      ),true) !!} /-
                                                                </span>
                                                            </div>
                                                           
                                                            <div class="col-md-3 padding-left-none form-control-fld margin-bottom-none">
                                                                <span class="data-head">Total Amount (Rs) :</span> 
                                                                <span class="data-value" id="total_charges_{!! $buyerQuoteId !!}" 
                                                                      data-totalcharges="{!! $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_quote_price, 
                                                                                            $buyerQuoteSellersQuotesDetails->counter_quote_price, 
                                                                                            $buyerQuoteSellersQuotesDetails->final_quote_price)
                                                                                            !!}">{!! $commonComponent->moneyFormat(
                                                                                        $buyerCommonComponent->getFinalDetailsForCounterOffer(
                                                                                            $buyerQuoteSellersQuotesDetails->initial_quote_price, $buyerQuoteSellersQuotesDetails->counter_quote_price, $buyerQuoteSellersQuotesDetails->final_quote_price
                                                                                        ),true) !!} /-</span>
                                                            </div> 
                                                            @endif
                                    

                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                       {!! Form::close() !!}  
                                    <!-- Table Row Ends Here -->
                                </div>
                                </div>
                            </div>
                            {{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}} 
                            <div id="ltl-buyer-leads" class="tabs-group" {{$leads_style}}>
                                <!-- Table Head Starts Here -->
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-3 padding-left-none">
                                        <span class="lbl padding-8"></span>
                                        Vendor Name<i class="fa fa-caret-down"></i>
                                    </div>
                                    <div class="col-md-3 padding-left-none">Transit Time<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-2 padding-left-none">Price (<i class="fa fa-inr fa-1x"></i>)<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-2 padding-left-none">
                                        @if(!empty($comparisonType))
                                            Ranking
                                        @endif
                                    </div>
                                    <div class="col-md-2 padding-left-none"></div>
                                </div>
                                <!-- Table Head Ends Here -->
                                <div class="table-data">
                                    @if(isset($sellerDetailsLeads) && !empty($sellerDetailsLeads))
                                        @foreach ($sellerDetailsLeads as $sellerData)
                                            {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                                            <!-- Table Row Starts Here -->
                                            <div class="table-row inner-block-bg">
                                                <div class="col-md-3 padding-left-none">
                                                    <span class="lbl padding-8"></span>
                                                    {!! $sellerData->username !!}
                                                    <div class="red">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 padding-none">{!! $sellerData->transitdays !!} {!! $sellerData->units !!}</div>
                                                <div class="col-md-2 padding-none" data-price="{!! $sellerData->price !!}" id="ptl_buyer_leads_post_price_{!! $buyerQuoteForLeadId !!}">
                                                    
                                                </div>
                                                <div class="col-md-2 padding-none"></div>
                                                <div class="col-md-2 padding-none">
                                                    @if($commonComponent->CheckCartItem($id)==1)
                                                    @if($postStatus == OPEN)
                                                    {!! Form::open(array('url' => "$urlForLeads$id/$buyerQuoteForLeadId", 'id' => 'buyer_leads_book_now', 'name' => 'buyer_leads_book_now')) !!}
                                                        <input type="button" id = "ptl_buyer_leads_book_now_{{ $buyerQuoteForLeadId }}" 
                                                            class="btn red-btn pull-right submit-data buyer_leads_book_now" data-id="{{ $buyerQuoteForLeadId }}"
                                                                data-booknow_list="{!! $buyerQuoteForLeadId !!}" data-buyerpostofferid="{{ $buyerQuoteForLeadId }}"
                                                                    data-url="{{ $urlForLeads.$id.'/'.$buyerQuoteForLeadId }}" value="Book Now"/>
                                                   {!! Form::close() !!}                 
                                                    @endif
                                                    @endif
                                                </div>
                                                
                                                
                                                
                                                  <!-- --Buyer leads in seller data -->
                                                    <div class="clearfix"></div>
                                                    
				                                       <div class="pull-right text-right">
                                                                            <div class="info-links">
                                                                                    <a class="viewcount_show-data-link" data-quoteId="{{$sellerData->id}}" ><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
                                                                                    <a href="#" class="red underline_link new_message" data-transaction="{{$sellerData->transaction_no}}" data-userid='{{$sellerData->seller_id}}' data-id="{{$id}}" data-buyerleadsitemid="{{ $buyerQuoteForLeadId }}"><i class="fa fa-envelope-o"></i></a>
                                                                            </div>
                                                                        </div>                                               
                                                    
		                                               <div class="col-md-12 show-data-div padding-top">

										
                                                <div class="col-md-12 padding-none">
												<div class="col-md-8 data-div padding-top">

													<div class="col-md-4 padding-left-none data-fld">
														<span class="data-head">Base Freight</span>
														<span class="data-value" id="ltl_leads_basefright_{!! $buyerQuoteForLeadId !!}"></span>
													</div>
													
			                                          
			                                       @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
			                                            
														@if($data->is_door_pickup == 1)
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Pickup Charges</span>
															<span class="data-value">Rs {!! $sellerData->pickup_charges !!} /-</span>
														</div>
														@endif
														
														@if($data->is_door_delivery == 1)
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Delivery Charges</span>
															<span class="data-value">Rs {!! $sellerData->delivery_charges !!}  /-</span>
														</div>
														@endif		
														
														@if($sellerData->oda_charges!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">ODA Charges</span>
															<span class="data-value">Rs {!! $sellerData->oda_charges !!}  /-</span>
														</div>
														@endif													
														
											    	@endif	
											    	
											    	@if($serviceId == COURIER)
											    		@if($sellerData->kg_per_cft!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Conversion Factor(CCM/KG)</span>
															<span class="data-value">Rs {!! $sellerData->kg_per_cft !!}  /-</span>
														</div>
														@endif	
														@if($sellerData->fuel_surcharge!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Fuel Surcharge</span>
															<span class="data-value">Rs {!! $sellerData->fuel_surcharge !!} %</span>
														</div>
														@endif	
														@if($sellerData->freight_collect_charge!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Freight Collect</span>
															<span class="data-value">Rs {!! $sellerData->freight_collect_charge !!}  /-</span>
														</div>
														@endif
														@if($sellerData->arc_charge!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">ARC</span>
															<span class="data-value">Rs {!! $sellerData->arc_charge !!} %</span>
														</div>
														@endif
											    	@endif
													
														@if($sellerData->cancellation_charge_price!='')
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Cancellation Charges</span>
															<span class="data-value">Rs {!! $sellerData->cancellation_charge_price !!}  /-</span>
														</div>
														@endif	
														
														@if($sellerData->terms_conditions!='')	
														<div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Terms & Conditions</span>
															<span class="data-value">{!! $sellerData->terms_conditions !!}</span>
														</div>
														@endif													
														
														@if($sellerData->docket_charge_price!='')
														 <div class="col-md-4 padding-left-none data-fld">
															<span class="data-head">Other Charges</span>
															<span class="data-value">Rs {!! $sellerData->docket_charge_price !!} /-</span>
														</div> 
														@endif
													
														
												</div>
                                                <div class="col-md-4 padding-top">
                                                        <span class="data-head">Total Price (<i class="fa fa-inr fa-1x"></i>)</span>
                                                        <span class="data-value big-value" id="total_hidden_price_{!! $buyerQuoteForLeadId !!}"></span>
                                                    </div>
                                                    </div>

												<div class="clearfix"></div>
							<!-- -Total below start endif for check courier or not srinu added uptoop check calcution for courier-->
									
												@if($serviceId == COURIER)
                                                                                                <h2 class="sub-head margin-top"><span class="from-head">{!! $fromCity !!} - {!! $toCity !!} </span> </h2>
                                                                                                @else
                                                                                                <h2 class="sub-head margin-top"><span class="from-head">{!! $sellerData->fromcity !!} ( {!! $sellerData->frompincode !!} ) </span> - <span class="to-head"> {!! $sellerData->tocity !!} ( {!! $sellerData->topincode !!} ) </span></h2>
                                                                                                @endif
											@if($serviceId != COURIER)
												<div class="table-div table-style1">
									
													<!-- Table Head Starts Here -->
 													
													<div class="table-heading inner-block-bg">
														<div class="col-md-2 padding-left-none">Load type</div>
														<div class="col-md-1 padding-left-none">Volume</div>
														<div class="col-md-1 padding-left-none">Unit Weight</div>
														<div class="col-md-2 padding-left-none">No of Packages</div>
                                                                                                                @if($serviceId == ROAD_PTL || $serviceId == RAIL )
														<div class="col-md-1 padding-left-none">KG/CFT</div>
                                                                                                                @elseif($serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL)
                                                                                                                <div class="col-md-1 padding-left-none">KG/CCM</div>
                                                                                                                @elseif($serviceId == OCEAN)
                                                                                                                <div class="col-md-1 padding-left-none">KG/CBM</div>
                                                                                                                @else
                                                                                                                <div class="col-md-1 padding-left-none">KG/CFT</div>
                                                                                                                @endif
                                                                                                                
														<div class="col-md-2 padding-left-none">Chargable Weight</div>
														<div class="col-md-1 padding-left-none">Rate/KG</div>
														<div class="col-md-2 padding-left-none">Chargable Amount</div>
													</div>														

													<!-- Table Head Ends Here -->

													<div class="table-data chargeable_checkamnt_class" leads_id="{{$sellerData->id}}">

														<!-- Table Row Starts Here - srinu code starts here 
														calculating seller leads total price same as ltl buyer seach for sellers -->
														{{--*/  $totallcharge=0 /*--}}
														{{--*/  $totalbasechargeprice=0 /*--}}
														{{--*/  $totalChargableAmount=0 /*--}}
														
													@if(isset($arraySellerDetails) && !empty($arraySellerDetails))
                                  						@foreach($arraySellerDetails as $key=>$sellersQuotesDetails)     
                                  										
														@if($sellersQuotesDetails->weightTypeId == 1)
			                                           		{{--*/  $ptlConvertunitweight = $sellersQuotesDetails->buyerQuoteUnits /*--}}
			                                          		{{--*/  $ptlConvertDisplaytype = 'Kgs' /*--}}
				                                        @elseif($sellersQuotesDetails->weightTypeId == 2)
				                                       		{{--*/  $ptlConvertunitweight = ($sellersQuotesDetails->buyerQuoteUnits*0.001) /*--}}
				                                            {{--*/  $ptlConvertDisplaytype = 'Gms' /*--}}
				                                        @elseif($sellersQuotesDetails->weightTypeId == 3)
				                                            {{--*/ $ptlConvertunitweight = ($sellersQuotesDetails->buyerQuoteUnits*1000) /*--}}
				                                            {{--*/ $ptlConvertDisplaytype = 'MTs' /*--}}
				                                        @endif                               
				                                        
				                                       {{--*/ $displayVolumeWeight=$sellersQuotesDetails->calculated_volume_weight /*--}}
				                                 
				                                       {{--*/  $chargableWeight = ($displayVolumeWeight * $sellerData->kg_per_cft *  $sellersQuotesDetails->number_packages) /*--}}
				                                       {{--*/  $chargeunitWeight = ($ptlConvertunitweight*$sellersQuotesDetails->number_packages) /*--}}
				                                       @if($chargableWeight > $chargeunitWeight)
				                                           {{--*/  $displayChargableweighttotal = $chargableWeight /*--}}
				                                        @else
				                                           {{--*/ $displayChargableweighttotal = $chargeunitWeight /*--}}
				                                       @endif	
				                                     
			                                           {{--*/ $totalChargableAmountIndividual = ($displayChargableweighttotal*$sellerData->price) /*--}}     
			                                           {{--*/ $totalChargableAmount += ($displayChargableweighttotal*$sellerData->price) /*--}}			                                           
			                                        
			                                           {{--*/  $totallcharge += $totalChargableAmountIndividual /*--}}
			                                            
														<div class="table-row inner-block-bg">
															<div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->load_type !!}</div>
															<div class="col-md-1 padding-left-none">{!! round($sellersQuotesDetails->calculated_volume_weight,4) !!}{!! $str !!}</div>
															<div class="col-md-1 padding-left-none">{!! $sellersQuotesDetails->buyerQuoteUnits !!}  {{ $sellersQuotesDetails->weight_type }}</div>
															<div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->number_packages !!}</div>
															<div class="col-md-1 padding-left-none">{!! $sellerData->kg_per_cft !!} </div>
															<div class="col-md-2 padding-left-none">{!! $displayChargableweighttotal !!}</div>
															<div class="col-md-1 padding-left-none">{!! $sellerData->price !!} </div>
															<div class="col-md-2 padding-left-none" class="dispaly_chargable_amnt_{!! $buyerQuoteForLeadId !!}">{!! $totalChargableAmountIndividual !!}</div>
														</div>
														 @endforeach
														 @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
					                                       {{--*/   $checkOda = $commonComponent->sellerODACheck($arrayBuyerCounterOffer[0]->to_location_id,$serviceId)  /*--}}
					                                       
				                                            @if($checkOda == 1)
				                                                {{--*/ $odaPrice=$sellerData->oda_charges /*--}}
				                                            @else
				                                               {{--*/  $odaPrice = 0 /*--}}
				                                            @endif
			                                         @else
			                                        	    {{--*/  $odaPrice = 0 /*--}}
			                                         @endif
			                                         
														 @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
				                                           @if($data->is_door_pickup == 1)
				                                           {{--*/  $doorpickupcharges = $sellerData->pickup_charges /*--}}
				                                           @else
				                                           {{--*/  $doorpickupcharges = 0 /*--}}
				                                           @endif
				                                           
				                                           @if($data->is_door_delivery == 1)
				                                           {{--*/  $doordelivverycharges = $sellerData->delivery_charges /*--}}
				                                           @else
				                                           {{--*/  $doordelivverycharges = 0 /*--}}
				                                           @endif
			                                         @else
			                                         {{--*/  $doorpickupcharges = 0 /*--}}
			                                         {{--*/  $doordelivverycharges = 0 /*--}}
			                                         @endif
			                                         
			                                         {{--*/  $totallcharge += $doorpickupcharges+$doordelivverycharges /*--}}
			                                            {{--*/  $basefghtamnt= $totallcharge+$odaPrice/*--}}
			                                            <span class="total_base_oda_{!! $buyerQuoteForLeadId !!}" style="display:none">{{ $totalChargableAmount }}</span>
			                                            <span class="total_all_{!! $buyerQuoteForLeadId !!}" style="display:none">{{ $basefghtamnt }}</span>                     						
                                  						
			                                         
                                					@endif

														<!-- Table Row Ends Here -->
													</div>
												</div>
												
												@elseif($serviceId == COURIER)
												<!-- ----------Here include courier calculation partial file (srinu - 30-03-2016) -->
												<div class="courier_total_price_calc" leads_id="{{$sellerData->id}}">		
												@include('partials.courier_leads')		
												</div>
														
											  	@endif	
												<!-- -Total end endif for check courier or not -->
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
<!--                                                    <div class="col-md-12 padding-none show-data-div">-->
                                            </div>
                                        @endforeach
                                    @endif
                                    <!-- Table Row Ends Here -->
                                </div>
                            </div>
                            
                        </div>
                    <!-- Table Starts Here -->
                </div>
                <!-- Right Section Ends Here -->
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    @include('partials.footer')
@endsection
