{{--*/ $exclude_transit=array(RELOCATION_PET_MOVE,RELOCATION_INTERNATIONAL,RELOCATION_GLOBAL_MOBILITY,RELOCATION_OFFICE_MOVE) /*--}}
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('relOceanSellerCComponent', 'App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent')
@if(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==1)
{{--*/ $buyer_post_inventory_details=$commonComponent->getCartonDetails($post->id) /*--}}
@endif
{{--*/ $serviceId = Session::get('service_id') /*--}}
@if(in_array($serviceId,$exclude_transit))
    {{--*/ $trackingExist = 1 /*--}}
@endif

@extends('app')
@section('content')
    @include('partials.page_top_navigation')

    {{--*/ $SellerPickupDate 	= 		$order->seller_pickup_date; /*--}}
    {{--*/ $buyerPickupDate 	=  		$order->buyer_consignment_pick_up_date; /*--}}
    {{--*/ $SellerDeliveryDate 	=  		$order->seller_delivery_date; /*--}}
    {{--*/ $DeliveryDate 	=  		$order->delivery_date; /*--}}
    {{--*/ $DispatchDate 	=  		$order->dispatch_date; /*--}}
    {{--*/ $current_date_seller	=  		date("Y-m-d");  /*--}}
    {{--*/ $str			=		'' /*--}}
    {{--*/ $strdelivery		=		'' /*--}}

    <div class="main">

        <div class="container">

            <span class="pull-left"><h1 class="page-title">Seller Consignment Pickup - {{$order->order_no}}</h1></span>
            <input type='hidden' id='current_service_id' value="{{$serviceId}}">
            <span class="pull-right"><a onclick="return checkSession({{$serviceId}},'/createseller');" href="#"><button class="btn post-btn pull-right">+ Post</button></a></span>

            <div class="filter-expand-block">

                <div class="search-block inner-block-bg margin-bottom-less-1">

                    <div class="from-to-area">

                    <span class="search-result">
                        <i class="fa fa-map-marker"></i>
                        <span class="location-text">
                        @if($serviceId==RELOCATION_GLOBAL_MOBILITY) 
                            {{$post->from}}
                        @else
                            {{$post->from}}@if($serviceId!=RELOCATION_OFFICE_MOVE) to {{$post->to}} @endif</span>
                        @endif
                    </span>
                    </div>
                    <div class="date-area">
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Dispatch Date</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            @if($order->dispatch_date)
                                {{date("d/m/Y", strtotime($order->dispatch_date))}}
                            @else
                                <?php $order->dispatch_date=$order->buyer_consignment_pick_up_date;?>
                                {{date("d/m/Y", strtotime($order->buyer_consignment_pick_up_date))}}
                            @endif
                        </span>
                        </div>
                        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY) 
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Delivery Date</p>
                            <span class="search-result">
                                @if($order->delivery_date =="" || $order->delivery_date=="0000-00-00")
                                    N/A
                                @else
                                    <i class="fa fa-calendar-o"></i>
                                    {{date("d/m/Y", strtotime($order->delivery_date))}}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    @if($serviceId==RELOCATION_DOMESTIC)
                        <div>
                        <span class="search-result">
                            <p class="search-head">Post Type</p>
                            <span class="location-text">
                                @if(isset($post->lkp_post_ratecard_type_id))
                                    @if($post->lkp_post_ratecard_type_id==1)
                                        HHG
                                    @else
                                        Vehicle
                                    @endif
                                @endif
                            </span>
                        </span>
                        </div>
                        <div>
                            @if($post->lkp_post_ratecard_type_id==1)
                                    <span class="data-head">Load Type</span> <span
                                            class="data-value">
                                            @if(isset($post->lkp_post_ratecard_type_id))
                                            @if(isset($post->lkp_load_category_id) && $post->lkp_load_category_id==1)
                                            Full Load
                                            @else
                                            Part Load
                                            @endif
                                            @endif
                                            </span>
                            @else
                                    <span class="data-head">Vehicle Catagoery</span> <span
                                            class="data-value">
                                            @if(isset($post->lkp_vehicle_category_id))
                                            @if($post->lkp_vehicle_category_id==1)
                                            Car
                                            @else
                                            Bike / Scooter / Scooty
                                            @endif
                                            @endif

                                            </span>
                            @endif
                        </div>
                    @elseif($serviceId==RELOCATION_PET_MOVE)
                        <div>
                            <span class="search-result">
                                <p class="search-head">Pet Type</p>
                                <span class="location-text">
                                    @if(isset($post->lkp_pet_type_id))
                                    {!! $commonComponent->getPetType($post->lkp_pet_type_id) !!}
                                    @else &nbsp;
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div>
                            <span class="search-result">
                                <p class="search-head">Cage Type</p>
                                <span class="location-text">
                                    @if(isset($post->lkp_cage_type_id))
                                    {!! $commonComponent->getCageType($post->lkp_cage_type_id) !!}
                                    @else &nbsp;
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div>
                            <span class="search-result">
                                <p class="search-head">Cage Weight</p>
                                <span class="location-text">
                                    @if(isset($post->lkp_cage_type_id))
                                    {!! $commonComponent->getCageWeight($post->lkp_cage_type_id) !!} KGs
                                    @else &nbsp;
                                    @endif
                                </span>
                            </span>
                        </div>
                    @elseif($serviceId==RELOCATION_INTERNATIONAL)
                        <div>
                            <span class="search-result">
                                <p class="search-head">Type</p>
                                <span class="location-text">
                                    @if(isset($order->lkp_international_type_id) && $order->lkp_international_type_id==1)
                                    Air
                                    @else
                                    Ocean
                                    @endif
                                </span>
                            </span>
                        </div>
                        @if(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==1)
                            <div>
                                <span class="search-result">
                                    <p class="search-head">No of Cartons</p>
                                    <span class="location-text">
                                        {{$commonComponent->getCartonsTotal($post->id)}}
                                    </span>
                                </span>
                            </div>
                            <div>
                            <span class="search-result">
                                <p class="search-head">Weight</p>
                                <span class="location-text">
                                    @if(isset($post->total_cartons_weight))
                                    {!! $post->total_cartons_weight !!} KGs
                                    @else &nbsp;
                                    @endif
                                </span>
                            </span>
                            </div>
                        @elseif(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==2)
                            <div>
                            <span class="search-result">
                                <p class="search-head">Volume</p>
                                <span class="location-text">
                                    {{--*/ $totalCFT=$relOceanSellerCComponent->getVolumeCft($post->id) /*--}}
                                    {{--*/ $volume=round($totalCFT/35.5, 2) /*--}}
                                    {{ $volume }} CBM
                                </span>
                            </span>
                            </div>
                        @endif

                    @endif
                    <!-- -status bar check variables and conditions -->

                    @if($SellerPickupDate == '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00')
                        @if($current_date_seller < $DispatchDate)
                        {{--*/ $str				=		'' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($current_date_seller > $DispatchDate)
                        {{--*/ $str				=		'<div class="status-bar-left"></div>' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($current_date_seller == $DispatchDate)
                        {{--*/ $str				=		'' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @endif
                    @elseif($SellerPickupDate != '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00')
                        @if($SellerPickupDate <= $DispatchDate." 00:00:00")
                        {{--*/ $sellerpickupcolor=		'green' /*--}}
                        {{--*/ $str				=		'<div class="status-bar-left-green"></div>'  /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($SellerPickupDate > $DispatchDate." 00:00:00")
                        {{--*/ $sellerpickupcolor=		'red' /*--}}
                        {{--*/ $str				=		'<div class="status-bar-left"></div>'    /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @endif

                        @if($current_date_seller < $DeliveryDate)
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($current_date_seller > $DeliveryDate)
                        {{--*/ $strdelivery		=		'<div class="status-bar-right-red"></div>'  /*--}}
                        @elseif($current_date_seller == $DeliveryDate)
                        {{--*/ $strdelivery		=		'' /*--}}
                        @endif
                    @else
                                 @if($DispatchDate == $current_date_seller)
                                                                {{--*/ $sellerpickupcolor=		'green' /*--}}
                        {{--*/ $str				=		'<div class="status-bar-left-green"></div>' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($SellerPickupDate <= $DispatchDate." 00:00:00")
                        {{--*/ $sellerpickupcolor=		'green' /*--}}
                        {{--*/ $str				=		'<div class="status-bar-right-full"></div>' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @elseif($SellerPickupDate > $DispatchDate." 00:00:00")
                        {{--*/ $sellerpickupcolor=		'red' /*--}}
                        {{--*/ $str				=		'<div class="status-bar-left"></div>' /*--}}
                        {{--*/ $strdelivery		=		'' /*--}}
                        @endif

                        @if($SellerDeliveryDate <= $DeliveryDate." 00:00:00")
                                @if($sellerpickupcolor!="")
                                {{--*/ $strdelivery		=		'<div class="status-bar-right"></div>' /*--}}
                                @endif
                        @elseif($SellerDeliveryDate > $DeliveryDate." 00:00:00")
                                @if($sellerpickupcolor!="")
                                {{--*/ $strdelivery='<div class="status-bar-right-red"></div>'  /*--}}
                                @endif
                        @endif
                    @endif

                    <div>
                        <p class="search-head">Status</p>
                        <span class="search-result status-block">
                            <div class="status-bar">
                                <div class="status-bar">
                                {!! $str !!}{!! $strdelivery !!}
                                        <span class="status-text">
                                            @if($serviceId==RELOCATION_GLOBAL_MOBILITY)
                                                @if($order->order_status=="Pickup due")
                                                Commencement Due
                                                @elseif($order->order_status=="Consignment pickup")
                                                Commencement Started
                                                @else
                                                Commencement Completed
                                                @endif
                                            @else
                                            {!! $order->order_status !!}
                                            @endif
                                        </span>
                                </div>
                            </div>
                        </span>
                    </div>

                    <div class="text-right filter-details">
                        <div class="info-links">
                            <a class="transaction-details-expand"><span class="show-icon">+</span>
                                <span class="hide-icon">-</span> Details
                            </a>
                        </div>
                    </div>

                </div>

                <!--toggle div starts-->
                <div class="show-trans-details-div-expand trans-details-expand">
                    <div class="expand-block">
                        <div class="col-md-12">
                            @if($serviceId==RELOCATION_GLOBAL_MOBILITY) 
                                @include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $post->id])
                            @endif
                            @if(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==2)
                                <div class="col-md-2 padding-left-none data-fld">
                                    <span class="data-head">Property Type</span>
                                    <span class="data-value">
                                        {{$commonComponent->getPropertyType($post->lkp_property_type_id)}}
                                    </span>
                                </div>
                            @endif
                            @if($serviceId!=RELOCATION_GLOBAL_MOBILITY) 
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Relocation Post Number</span>
                                <span class="data-value">{{$post->transid}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Buyer Name</span>
                                <span class="data-value">{{$post->name}}</span>
                            </div>
                            @if($order->buyer_consignee_name)
                                <div class="col-md-2 padding-left-none data-fld">
                                    <span class="data-head">Consignee</span>
                                    <span class="data-value">{{$order->buyer_consignee_name}}</span>
                                </div>
                            @endif
                            @endif
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            @if(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==2)
                                @include('relocationint.ocean.buyers.buyerpost_inventory_details',array('buyerpost_id'=>$post->id))
                            @elseif(isset($post->lkp_international_type_id) && $post->lkp_international_type_id==1)
                                <div class="table-div table-style1 padding-none">
                                    <div class="table-heading inner-block-bg">
                                            <div class="col-md-8 padding-left-none">Carton Type</div>
                                            <div class="col-md-4 padding-left-none">Nos</div>
                                    </div>
                                    <div class="table-data">
                                        @foreach($buyer_post_inventory_details as $buyer_cartons)
                                         <div class="table-row inner-block-bg">
                                                <div class="col-md-8 padding-left-none">{{$buyer_cartons->carton_type}} ({{$buyer_cartons->carton_description}})</div>
                                                <div class="col-md-4 padding-left-none">
                                                    {{$buyer_cartons->number_of_cartons}}
                                                </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="clearfix"></div>

                    </div>
                </div>
                <!--toggle div ends-->

            </div>

            <div class="col-md-12 padding-none">
                <div class="main-inner">
                    <div class="main-right">
                    @include('partials.is_gsa_consignment_accepted')
                    @include('partials.pickup_details')
                    @if(!in_array($serviceId,$exclude_transit))
                        @include('partials.tracking_details')
                    @endif
                    @include('partials.delivery_details')
                    @include('partials.invoice_details')

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    @include('partials.gsa_consignment')
    @include('partials.footer')
@endsection