@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content')
{{--*/ $serviceId = Session::get('service_id') /*--}}
{{--*/ $isDoorPickup = '' /*--}}
{{--*/ $idDoorDelivery = '' /*--}}
{{--*/ $request_blade=Session::get('request') /*--}}

@if(isset($allInput['search_ptl_buyer_doorpick_'.$sellerPostId]) && isset($allInput['search_ptl_buyer_doordelivery_'.$sellerPostId]))
        {{--*/ $isDoorPickup = ($allInput['search_ptl_buyer_doorpick_'.$sellerPostId] == 1) ? 'Yes' : 'No' /*--}}
        {{--*/ $idDoorDelivery = ($allInput['search_ptl_buyer_doordelivery_'.$sellerPostId] == 1) ? 'Yes' : 'No' /*--}}
@endif
@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $name = Auth::user()->username /*--}}
        {{--*/ $isCancelled = $data->lkp_post_status_id /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
         {{--*/ $postStatusText = $data->post_status /*--}}
        @if(isset($data->is_dispatch_flexible) && $data->is_dispatch_flexible==1)
        {{--*/ $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :date("Y-m-d", strtotime($data->from_date . ' -3 day')) /*--}}
        @else
        {{--*/ $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :$data->from_date /*--}}
        @endif
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}

        @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
        {{--*/ $product_made= $allInput['search_ptl_buyer_product_made_'.$sellerPostId] /*--}}
        {{--*/ $shipment_type = $commonComponent->getSelectedShipmentType($allInput['search_ptl_buyer_shipment_type_'.$sellerPostId]) /*--}}
        {{--*/ $sender_identity = $commonComponent->getSelectedSenderIdentity($allInput['search_ptl_buyer_sender_identity_'.$sellerPostId]) /*--}}
        {{--*/ $ie_code = $allInput['search_ptl_buyer_iecode_'.$sellerPostId] /*--}}
        @endif
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $name = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $postStatusText = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $product_made= '' /*--}}
    {{--*/ $shipment_type = '' /*--}}
    {{--*/ $sender_identity = '' /*--}}
    {{--*/ $ie_code = '' /*--}}
    {{--*/ $Dispatch_Date  = '' /*--}}
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

{{--*/ $request_blade=array() /*--}}
    {{--*/ $request_blade=Session::get('request') /*--}}    
    
<!--swathis code for flexible dates-->
        {{--*/ $validFrom='' /*--}}{{--*/ $validTo='' /*--}}
{{--*/ $fdispatch='' /*--}}{{--*/ $fdelivery='' /*--}}
    @if($request_blade['ptlDispatchDate'][0]!="") 
        <?php  $validFrom = str_replace('/','-',$request_blade['ptlDispatchDate'][0]) ?>
        {{--*/ $validFrom = date('Y-m-d', strtotime($validFrom)) /*--}}
    @endif
    @if($request_blade['ptlDeliveryhDate'][0]!="") 
        <?php $validTo = str_replace("/","-",$request_blade['ptlDeliveryhDate'][0]) ?>
        {{--*/ $validTo = date('Y-m-d', strtotime($validTo)) /*--}}
    @endif
    @if($request_blade['ptlFlexiableDispatch'][0]== 1) 
        {{--*/ $fdispatch = $buyerCommonComponent->getPreviousNextThreeDays($validFrom) /*--}}
        {{--*/ $Dispatch_Date = ($validFrom) ?date("Y-m-d", strtotime($validFrom . ' -3 day')):'' /*--}}
    @else 
        {{--*/ $fdispatch = $commonComponent->checkAndGetDate($validFrom) /*--}}
        {{--*/ $Dispatch_Date =date("Y-m-d", strtotime($validFrom)) /*--}}
    @endif
    @if($request_blade['ptlFlexiableDelivery'][0]== 1 && $request_blade['ptlDeliveryhDate'][0]!='') 
        {{--*/ $fdelivery = $buyerCommonComponent->getPreviousNextThreeDays($validTo) /*--}}
    @else 
        {{--*/ $fdelivery = $commonComponent->checkAndGetDate($validTo) /*--}}
    @endif
    {{--*/ $deliveryDate = $fdelivery /*--}}
<!--swathis code for flexible dates-->
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
		<!-- Inner Menu Ends Here -->
    <div class="main">
        <div class="container">
            @if (Session::has('cancelsuccessmessage'))
                <div class="flash alert-info">
                    <p class="text-success col-sm-12 text-center flash-txt-counterofer">
                        {{ Session::get('cancelsuccessmessage') }}</p>
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
           
            <div class="clearfix"></div>
            <span class="pull-left"><h1 class="page-title">Spot Transaction - {{ $transactionId }}</h1></span>
<!--             <span class="pull-right"> -->
<!--                 <a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a> -->
<!--                 @if($postStatus == '2') -->
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
                                {!! $fdispatch !!}
                            </span>
                        </div>
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Delivery Date</p>
                            <span class="search-result">
                                <i class="fa fa-calendar-o"></i>
                                @if(Session::get('session_delivery_buyer') == "0000-00-00" || Session::get('session_delivery_buyer') == "" )
                                    NA
                                @else
                                   {!! $fdelivery !!}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="search-head">Buyer Name</p>
                        <span class="search-result">{!! $name !!}</span>
                    </div>
                    @if($serviceId!=AIR_INTERNATIONAL && $serviceId!=OCEAN &&  $serviceId!=COURIER)
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

                    @if(Session::get('service_id') == COURIER) 
                    <div class="col-md-6">
                        <p class="search-head">Destination Type</p>
                                @if($request_blade['post_delivery_types'][0] == 1)
                                Domestic
                                @else
                                International
                                @endif                              
                    </div>
                    <div class="col-md-6">
                        <p class="search-head">Courier Type</p>
                                @if($request_blade['courier_types'][0] == 1)
                                Documents
                                @else
                                Parcel
                                @endif
                    </div>
                    @endif
                    
                    <div>
                        <p class="search-head">Status</p>
                        <span class="search-result"> {!! $postStatusText !!}</span>
                    </div>
                    <div>
                        
                    </div>
                </div>
                <div class="col-md-12 show-data-div"></div>
                <!-- Search Block Ends Here -->
            </div>
            @if(isset($seller_post) && !empty($seller_post))
                    @foreach ($seller_post as $sellerData)
                    {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                    <div class="search-block inner-block-bg margin-bottom-less-1">
                        <div>
                            <p class="search-head">Vendor Name</p>
                            <span class="search-result">
                                {!! $sellerData->username !!}
                                {{--*/ $seller_id = $sellerData->seller_id /*--}}
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
                            <span class="search-result" data-price="{!! $allInput['total_search_booknow_price_'.$sellerPostId] !!}" id="ptl_buyer_leads_post_price_{!! $buyerQuoteForLeadId !!}">
                                <i class="fa fa-rupee"></i> 
                                @if(isset($allInput['total_search_booknow_price_'.$sellerPostId]))
	                                {{--*/ $price = $allInput['total_search_booknow_price_'.$sellerPostId] /*--}}
	                                {!! $commonComponent->number_format($price,true) !!}
	                            @endif    
                               
                            </span>
                        </div>

                        <div class="empty-div"></div>
                    </div>
                    <!-- Get all hidden field-->
                    

                @endforeach
            @endif
            <!-- Search Block Ends Here -->

            <div class="col-md-12 padding-none">
                <div class="main-inner"> 
                    <!-- Right Section Starts Here -->
                    <div>
                        {!! Form::hidden('ptl_buyer_from_id_'.$sellerPostId, $allInput['search_ptl_buyer_from_id_'.$sellerPostId], array('id' => 'ptl_buyer_from_id_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_to_id_'.$sellerPostId, $allInput['search_ptl_buyer_to_id_'.$sellerPostId], array('id' => 'ptl_buyer_to_id_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_dispatch_'.$sellerPostId, $allInput['search_ptl_buyer_dispatch_'.$sellerPostId], array('id' => 'ptl_buyer_dispatch_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_delivery_'.$sellerPostId, $allInput['search_ptl_buyer_delivery_'.$sellerPostId], array('id' => 'ptl_buyer_delivery_'.$sellerPostId)) !!}
                        
                        @if($serviceId != COURIER)
                        {!! Form::hidden('ptl_buyer_load_id_'.$sellerPostId, $allInput['search_ptl_buyer_load_id_'.$sellerPostId], array('id' => 'ptl_buyer_load_id_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_pack_id_'.$sellerPostId, $allInput['search_ptl_buyer_pack_id_'.$sellerPostId], array('id' => 'ptl_buyer_pack_id_'.$sellerPostId)) !!}
                        @endif

                        {!! Form::hidden('ptl_buyer_volume_'.$sellerPostId, $allInput['search_ptl_buyer_volume_'.$sellerPostId], array('id' => 'ptl_buyer_volume_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_fdispatch_'.$sellerPostId, $allInput['search_ptl_buyer_fdispatch_'.$sellerPostId], array('id' => 'ptl_buyer_fdispatch_'.$sellerPostId)) !!}
                        
                        {!! Form::hidden('ptl_buyer_doorpick_'.$sellerPostId, $allInput['search_ptl_buyer_doorpick_'.$sellerPostId], array('id' => 'ptl_buyer_doorpick_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_fdelivery_'.$sellerPostId, $allInput['search_ptl_buyer_fdelivery_'.$sellerPostId], array('id' => 'ptl_buyer_fdelivery_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_doordelivery_'.$sellerPostId, $allInput['search_ptl_buyer_doordelivery_'.$sellerPostId], array('id' => 'ptl_buyer_doordelivery_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_weight_type_'.$sellerPostId, $allInput['search_ptl_buyer_weight_type_'.$sellerPostId], array('id' => 'ptl_buyer_weight_type_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_no_pack_'.$sellerPostId, $allInput['search_ptl_buyer_no_pack_'.$sellerPostId], array('id' => 'ptl_buyer_no_pack_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_unit_weight_'.$sellerPostId, $allInput['search_ptl_buyer_unit_weight_'.$sellerPostId], array('id' => 'ptl_buyer_unit_weight_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_vol_type_'.$sellerPostId, $allInput['search_ptl_buyer_vol_type_'.$sellerPostId], array('id' => 'ptl_buyer_vol_type_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_length_'.$sellerPostId, $allInput['search_ptl_buyer_length_'.$sellerPostId], array('id' => 'ptl_buyer_length_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_width_'.$sellerPostId, $allInput['search_ptl_buyer_width_'.$sellerPostId], array('id' => 'ptl_buyer_width_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_height_'.$sellerPostId, $allInput['search_ptl_buyer_height_'.$sellerPostId], array('id' => 'ptl_buyer_height_'.$sellerPostId)) !!}
                        @if($serviceId != COURIER && $serviceId != ROAD_PTL && $serviceId != RAIL && $serviceId!=RELOCATION_DOMESTIC && $serviceId!=AIR_DOMESTIC)
                            {!! Form::hidden('ptl_buyer_shipment_type_'.$sellerPostId, $allInput['search_ptl_buyer_shipment_type_'.$sellerPostId], array('id' => 'ptl_buyer_shipment_type_'.$sellerPostId)) !!}
                            {!! Form::hidden('ptl_buyer_iecode_'.$sellerPostId, $allInput['search_ptl_buyer_iecode_'.$sellerPostId], array('id' => 'ptl_buyer_iecode_'.$sellerPostId)) !!}
                            {!! Form::hidden('ptl_buyer_sender_identity_'.$sellerPostId, $allInput['search_ptl_buyer_sender_identity_'.$sellerPostId], array('id' => 'ptl_buyer_sender_identity_'.$sellerPostId)) !!}
                            {!! Form::hidden('ptl_buyer_product_made_'.$sellerPostId, $allInput['search_ptl_buyer_product_made_'.$sellerPostId], array('id' => 'ptl_buyer_product_made_'.$sellerPostId)) !!}
                        @endif
                        {!! Form::hidden('ptl_buyer_dispatchs_'.$sellerPostId, $allInput['search_ptl_buyer_dispatchs_'.$sellerPostId], array('id' => 'ptl_buyer_dispatchs_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_deliverys_'.$sellerPostId, $allInput['search_ptl_buyer_deliverys_'.$sellerPostId], array('id' => 'ptl_buyer_deliverys_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_post_buyer_id_'.$sellerPostId, $allInput['search_ptl_buyer_post_buyer_id_'.$sellerPostId], array('id' => 'ptl_buyer_post_buyer_id_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_post_seller_id_'.$sellerPostId, $allInput['search_ptl_buyer_post_seller_id_'.$sellerPostId], array('id' => 'ptl_buyer_post_seller_id_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_search_seller_to_date_'.$sellerPostId, $exactDeliveryDate, array('id' => 'ptl_buyer_search_seller_to_date_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_buyer_search_booknow_seller_price_'.$sellerPostId, $allInput['total_search_booknow_price_'.$sellerPostId], array('id' => 'ptl_buyer_search_booknow_seller_price_'.$sellerPostId)) !!}
                        {!! Form::hidden('ptl_cancel_buyer_counter_offer_enquiry', $id, array('id' => 'ptl_cancel_buyer_counter_offer_enquiry','data-id'=>$id)) !!}
                        
                        {!! Form::hidden('fdispatch-date_'.$sellerPostId, $Dispatch_Date, array('id' => 'fdispatch-date_'.$sellerPostId)) !!}
                    </div>
                    <div class="main-right">
                        {{--*/ $buyerQuoteId = $sellerPostId /*--}}
                        {!! Form::open(array('url' => '#', 'id' => 'ltl-buyer-search-booknow', 'name' => 'ltl-buyer-search-booknow')) !!}
                            @include('partials.buyer_booknow')
                        {!! Form::close() !!}
                        <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                        <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--*/ $request_blade=Session::get('request') /*--}} 
    <input type="hidden" name="commerical_type" id="commerical_type" value="1">
    
    @include('partials.gsa_booknow')
   
	@include('partials.footer')
@endsection