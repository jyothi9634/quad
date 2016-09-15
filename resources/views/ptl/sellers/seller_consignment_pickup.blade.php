@extends('app')
@section('content')
@include('partials.page_top_navigation')
@inject('common', 'App\Components\CommonComponent')
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

        {{--*/ $serviceId = Session::get('service_id') /*--}}
        {{--*/ $str_perkg='' /*--}} {{--*/ $str_service='' /*--}}
        @if($serviceId==ROAD_PTL || $serviceId==RAIL)
        {{--*/ $str_perkg=' CFT' /*--}}
        @elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
        {{--*/ $str_perkg=' CCM' /*--}}
        @elseif($serviceId==OCEAN)
        {{--*/ $str_perkg=' CBM' /*--}}
        @endif
        @if($serviceId == ROAD_PTL)
        {{--*/ $str_service="Lessthan Truck" /*--}}
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
        {!! Form::hidden('serviceId', $serviceId, array('id' => 'serviceId')) !!}
        <span class="pull-left"><h1 class="page-title">Seller Consignment Pickup - {{$order->order_no}}</h1></span>
        <span class="pull-right"><button class="btn post-btn pull-right" onclick="return checkSession({{$serviceId}},'/ptl/createsellerpost');">+ Post</button></span>

        <div class="filter-expand-block">
            <div class="search-block inner-block-bg margin-bottom-less-1">
                @if($post->from && $post->to)
                    <div class="from-to-area">
                        <span class="search-result">
                            <i class="fa fa-map-marker"></i>
                            <span class="location-text">{{$post->from}} to 
                            @if(isset($post->courier_delivery_type) && $post->courier_delivery_type!='')
                           		@if($post->courier_delivery_type=='International')
                           		{{ $common->getCountry($post->to_location_id) }}
                            	@else
                            	{{ $common->getPinName($post->to_location_id) }}
                            	@endif
                            @else
                                {{ $common->getPinName($post->to_location_id) }}
                            @endif
                            </span>
                        </span>
                    </div>
                @endif    
                
                <div class="date-area">
                    <?php
                        $dispatch_date=$order->buyer_consignment_pick_up_date;
                        $order->dispatch_date=$order->buyer_consignment_pick_up_date;
                    ?>
                    <div class="col-md-6 padding-none">
                        <p class="search-head">Dispatch Date</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            {{date("d/m/Y", strtotime($dispatch_date))}}
                        </span>
                    </div>
                
                    <?php $delivery_date=$order->delivery_date; ?>
            
                    <div class="col-md-6 padding-none">
                        <p class="search-head">Delivery Date</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            {{date("d/m/Y", strtotime($delivery_date))}}
                        </span>
                    </div>

                </div>
                @if($serviceId != AIR_DOMESTIC && $serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                <div>
                    <span class="search-result">
                        <p class="search-head">Door Pickup</p>
                        @if(isset($post->door_pickup)&& $post->door_pickup==1)
                            {{--*/ $door_pickup_status="Yes" /*--}}
                        @else
                            {{--*/ $door_pickup_status="No" /*--}}
                        @endif    
                        <span class="location-text">{{$door_pickup_status}}</span>
                    </span>
                </div>
                <div>
                    <span class="search-result">
                        <p class="search-head">Door Delivery</p>
                        @if(isset($post->door_delivery)&& $post->door_delivery==1)
                            {{--*/ $door_delivery_status="Yes" /*--}}
                        @else
                            {{--*/ $door_delivery_status="No" /*--}}                        
                        @endif        
                        <span class="location-text">{{$door_delivery_status}}</span>
                    </span>
                </div>
                @endif

                @if($serviceId == COURIER)
                <div>
                    <span class="search-result">
                        <p class="search-head">Destination Type</p>                          
                        <span class="location-text">{{$post->courier_delivery_type}}</span>
                    </span>
                </div>
                <div>
                    <span class="search-result">
                        <p class="search-head">Courier Type</p>                             
                        <span class="location-text">{{$post->courier_type}}</span>
                    </span>
                </div>
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
                                        <span class="status-text">{!! $order->order_status !!}</span>												
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

            <div class="show-trans-details-div-expand trans-details-expand">
                <div class="expand-block">
                    
                    @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)

                    <div class="col-md-12">
                        @if($post->shipment_type)
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Shipment Type</span>
                                <span class="data-value">{!! $post->shipment_type!!}</span>
                            </div>
                        @endif

                        @if($post->sender_identity)
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Sender Identity</span>
                                <span class="data-value">{!! $post->sender_identity!!}</span>
                            </div>
                        @endif

                        @if($post->ie_code)
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">IE Code</span>
                                <span class="data-value">{!! $post->ie_code!!}</span>
                            </div>
                        @endif
                        
                        @if($post->product_made)    
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Product Made</span>
                                <span class="data-value">{!! $post->product_made!!}</span>
                            </div>
                        @endif    
                    </div>
                    <div class="clearfix"></div>
                    @endif

                    <div class="table-div">
                        <div class="table-heading inner-block-bg">
                            <div class="col-md-2 padding-left-none">S. No</div>
                            @if($serviceId != COURIER)
                            <div class="col-md-3 padding-left-none">Product Type</div>
                            <div class="col-md-3 padding-left-none">Package</div>
                            @endif
                            @if($serviceId == COURIER)
                            <div class="col-md-3 padding-left-none">Courier Purpose</div>
                            <div class="col-md-3 padding-left-none">Number Packages</div>
                            @endif
                            <div class="col-md-2 padding-left-none">Weight</div>
                            <div class="col-md-2 padding-left-none">Volume</div>
                        </div>
                
                        <div class="table-data">
                            @if($post_items){{--*/ $i = 1 /*--}}
                                @foreach($post_items as $post_item)
                                    <div class="table-row inner-block-bg">
                                        <div class="col-md-2 padding-left-none">{{$i}}</div>
                                        @if($serviceId != COURIER)
                                        <div class="col-md-3 padding-left-none">@if(isset($post_item->packaging)){{$post_item->packaging}}@else - @endif</div>
                                        <div class="col-md-3 padding-left-none">@if(isset($post_item->load)){{$post_item->load}}@else - @endif</div>
                                        @endif
                                         @if($serviceId == COURIER)
                                        <div class="col-md-3 padding-left-none">@if(isset($post_item->courier_purpose)){{$post_item->courier_purpose}}@else - @endif</div>
                                        <div class="col-md-3 padding-left-none">@if(isset($post_item->number_packages)){{$post_item->number_packages}}@else - @endif</div>
                                        @endif
                                         @if($serviceId == COURIER)
                                        <div class="col-md-2 padding-left-none">N/A</div>
                                        @else
                                        <div class="col-md-2 padding-left-none">{{$post_item->unit}} @if(isset($post_item->weight_type)){{$post_item->weight_type}} @endif</div>
                                        @endif
                                        <div class="col-md-2 padding-left-none">@if(isset($post_item->cft)){{$post_item->cft}} {{$str_perkg}} @else - @endif</div>
                                    </div>
                                    {{--*/ $i++ /*--}}
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        
        <div class="col-md-12 padding-none">
            <div class="main-inner"> 
                
            <div class="main-right">
            @include('partials.is_gsa_consignment_accepted')
            
            @if($serviceId != AIR_DOMESTIC && $serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN)    

                @if(empty($vehicleExist) && $order->gsa_accepted==1)
                    @if($serviceId == ROAD_PTL || $serviceId == RAIL)
                        @if(isset($post->door_pickup) && $post->door_pickup==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Placement</h2>
                                </div>
                                <div class="detail-data">
                                    {!! Form::open(['url' => '','id'=>'posts-form-sellerpickup']) !!}
                                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                                    {!! Form::text('vehicle', '', ['id' => 'vehicle', 'placeholder' => 'Vehicle Number', 'class' => 'form-control clsVehicleno']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                    {!! Form::text('driver', '', ['id' => 'driver', 'placeholder' => 'Driver Name', 'class' => 'form-control clsDrivername']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-phone"></i></span>
                                                    {!! Form::text('mobile', '', ['id' => 'mobile', 'placeholder' => 'Driver Mobile Number', 'class' => 'form-control clsMobileno']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld text-right">
                                                <!--button class="btn add-btn">Add Vehicle</button-->
                                                <input type="submit" class="btn pull-right margin-bottom btn add-btn" id="add_vehicle" value="Add Vehicle">
                                            </div>

                                        </div>
                                    </div>
                                    {!! Form::close() !!}

                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-4 padding-left-none">Vehicle Number<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-4 padding-left-none">Driver Name<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-4 padding-left-none">Driver Mobile Number<i class="fa fa-caret-down"></i></div>
                                        </div>


                                        <div class="table-data">
                                            <div id="pick_vehicles">
                                                @foreach($vehicles as $vehicle)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-4 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                        <div class="col-md-4 padding-left-none">{{$vehicle->driver_name}}</div>
                                                        <div class="col-md-4 padding-left-none">{{$vehicle->mobile}}</div>
                                                    </div>
                                                @endforeach
                                            </div>    
                                        </div>

                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-vehicle']) !!}
                                        <input type="hidden" name="vehicle_confirm" value="1">
                                        <div class="col-md-12 text-right btn-block">
                                            <!--button class="btn add-btn flat-btn">Confirm</button-->
                                            <input type="submit" class="btn pull-right margin-bottom truckhaul add-btn flat-btn" value="Confirm">
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @else
                            {{--*/ $vehicleExist=1 /*--}}
                        @endif
                    @else
                    <div class="accordian-blocks">
                        <div class="inner-block-bg inner-block-bg1 detail-head">
                            <h2 class="filter-head1">Vehicle Placement</h2>
                        </div>
                        <div class="detail-data">
                            {!! Form::open(['url' => '','id'=>'posts-form-sellerpickup']) !!}
                            <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                            <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                <div class="col-md-12 padding-none">
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-truck"></i></span>
                                            {!! Form::text('vehicle', '', ['id' => 'vehicle', 'placeholder' => 'Vehicle Number', 'class' => 'form-control clsVehicleno']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                            {!! Form::text('driver', '', ['id' => 'driver', 'placeholder' => 'Driver Name', 'class' => 'form-control clsDrivername']) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-phone"></i></span>
                                            {!! Form::text('mobile', '', ['id' => 'mobile', 'placeholder' => 'Driver Mobile Number', 'class' => 'form-control clsMobileno']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-control-fld text-right">
                                        <!--button class="btn add-btn">Add Vehicle</button-->
                                        <input type="submit" class="btn pull-right margin-bottom btn add-btn" id="add_vehicle" value="Add Vehicle">
                                    </div>
            
                                </div>
                            </div>
                            {!! Form::close() !!}

                            <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-4 padding-left-none">Vehicle Number<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-4 padding-left-none">Driver Name<i class="fa fa-caret-down"></i></div>
                                    <div class="col-md-4 padding-left-none">Driver Mobile Number<i class="fa fa-caret-down"></i></div>
                                </div>


                                <div class="table-data">
                                    <div id="pick_vehicles">
                                        @foreach($vehicles as $vehicle)
                                            <div class="table-row inner-block-bg">
                                                <div class="col-md-4 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                <div class="col-md-4 padding-left-none">{{$vehicle->driver_name}}</div>
                                                <div class="col-md-4 padding-left-none">{{$vehicle->mobile}}</div>
                                            </div>
                                        @endforeach
                                    </div>    
                                </div>

                                {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-vehicle']) !!}
                                <input type="hidden" name="vehicle_confirm" value="1">
                                <div class="col-md-12 text-right btn-block">
                                    <!--button class="btn add-btn flat-btn">Confirm</button-->
                                    <input type="submit" class="btn pull-right margin-bottom truckhaul add-btn flat-btn" value="Confirm">
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    @endif
                @endif


                @if(!empty($vehicleExist) && $order->gsa_accepted==1)
                    @if($serviceId == ROAD_PTL || $serviceId == RAIL)
                        @if(isset($post->door_pickup) && $post->door_pickup==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Placement</h2>
                                </div>
                                <div class="detail-data padding-top">
                                    <div class="table-div table-style1">
                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-3 padding-left-none">Vehicle Number<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none">Driver Name<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none">Driver Mobile Number<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none"></div>
                                        </div>

                                        <div class="table-data">
                                            <div id="pick_vehicles">
                                                @foreach($vehicles as $vehicle)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->driver_name}}</div>
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->mobile}}</div>
                                                        <div class="col-md-3 padding-none text-right">
                                                            @include('partials._tracking')                                                         
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Placement</h2>
                                </div>
                                <div class="detail-data padding-top">
                                    <div class="table-div table-style1">
                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-3 padding-left-none">Vehicle Number<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none">Driver Name<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none">Driver Mobile Number<i class="fa fa-caret-down"></i></div>
                                            <div class="col-md-3 padding-left-none"></div>
                                        </div>

                                        <div class="table-data">
                                            <div id="pick_vehicles">
                                                @foreach($vehicles as $vehicle)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->driver_name}}</div>
                                                        <div class="col-md-3 padding-left-none">{{$vehicle->mobile}}</div>
                                                        <div class="col-md-3 padding-none text-right">
                                                            @include('partials._tracking') 
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endif
                    
                        
                @endif
            @else
                {{--*/ $vehicleExist=1 /*--}}
            @endif

            @if(empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)

                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Consignment Pickup</h2>
                    </div>
                    <div class="detail-data">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-pickup']) !!}
                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('pick_date', '', ['id' => 'pick_date','class'=>"calendar form-control from-date-control", 'placeholder' => 'Pickup Date','readonly' => true]) !!}
                                        <input type='hidden' id='cpick' value="{{date('d/m/Y', strtotime('-1 day', strtotime($order->dispatch_date)))}}">
                                        <input type='hidden' id='cdelivery' value="{{date('d/m/Y', strtotime($order->delivery_date))}}">
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-gg"></i></span>
                                        @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC)
                                            {!! Form::text('lr_no', '', ['id' => 'lr_no', 'placeholder' => 'AWB Number', 'class' => 'form-control clsRIASAwbnumber']) !!}
                                        @elseif($serviceId == OCEAN )
                                            {!! Form::text('lr_no', '', ['id' => 'lr_no', 'placeholder' => 'BL Number', 'class' => 'form-control clsRIASAwbnumber']) !!}
                                        @else
                                            {!! Form::text('lr_no', '', ['id' => 'lr_no', 'placeholder' => 'LR Number', 'class' => 'form-control clsLRnumber']) !!}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC)
                                        {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"calendar form-control", 'placeholder' => 'AWB Date','readonly' => true]) !!}
                                        @elseif($serviceId == OCEAN )
                                        {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"calendar form-control", 'placeholder' => 'BL Date','readonly' => true]) !!}
                                        @else
                                        {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"calendar form-control", 'placeholder' => 'LR Date','readonly' => true]) !!}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-gg"></i></span>
                                        {!! Form::text('bill_no', '', ['id' => 'bill_no', 'placeholder' => 'Transporter Bill Number', 'class' => 'form-control clsTransporterBill']) !!}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                        {!! Form::text('info1', '', ['id' => 'info1', 'placeholder' => 'Customer Document 1 (Optional)', 'class' => 'form-control clsCustDocs']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                        {!! Form::text('info2', '', ['id' => 'info2', 'placeholder' => 'Customer Document 2 (Optional)', 'class' => 'form-control clsCustDocs']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 form-control-fld text-right">
                                    <input type="submit" class="btn add-btn" value="Confirm">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            @endif    

            @if(!empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)

                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Consignment Pickup</h2>
                    </div>
                    <div class="detail-data">

                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">

                            <div class="col-md-12">

                                <div class="col-md-3 padding-left-none data-fld">
                                    <span class="data-head">Pickup Date</span>
                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_date))}}</span>
                                </div>

                                <div class="col-md-3 padding-left-none data-fld">
                                    <span class="data-head">
                                     @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC)
					AWB Number
                                     @elseif($serviceId == OCEAN )
					BL Number
                                     @else
                                    	LR Number
                                     @endif
                                    </span>
                                    <span class="data-value">{{$order->seller_pickup_lr_number}}</span>
                                </div>

                                <div class="col-md-3 padding-left-none data-fld">
                                    <span class="data-head">
                                    @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC)
					AWB Date
                                    @elseif($serviceId == OCEAN )
					BL Date    
                                    @else
                                        LR Date
                                    @endif</span>
                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_lr_date))}}</span>
                                </div>

                                <div class="col-md-3 padding-left-none data-fld">
                                    <span class="data-head">Transporter bill no</span>
                                    <span class="data-value">{{$order->seller_pickup_transport_bill_no}}</span>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 padding-left-none data-fld">
                                    <span class="data-head">Additional Fields Optional</span>
                                </div>

                                @if($order->seller_pickup_customer_doc_one)
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Customer Document 1</span>
                                        <span class="data-value">{{$order->seller_pickup_customer_doc_one}}</span>
                                    </div>
                                @endif

                                @if($order->seller_pickup_customer_doc_two)
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Customer Document 2</span>
                                        <span class="data-value">{{$order->seller_pickup_customer_doc_two}}</span>
                                    </div>
                                @endif


                            </div>

                        </div>
                    </div>
                </div>
            @endif

@if(isset($tracking) && $tracking==1)

            @if($trackingExist==0 && !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Transit Details</h2>
                    </div>
                    <div class="detail-data padding-top">
                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-tracklocation']) !!}
                            <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('location', '', ['id' => 'location', 'placeholder' => 'Location', 'class' => 'form-control clsAlphaSpace']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('date', '', ['id' => 'date','class'=>"calendar", 'placeholder' => 'Date','readonly' => true, 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 form-control-fld text-right">
                                    <input type="submit" class="btn add-btn" value="Add">
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <div class="table-div table-style1">
                            <div class="table-heading inner-block-bg">
                                <div class="col-md-4 padding-left-none">Location<i class="fa fa-caret-down"></i></div>
                                <div class="col-md-8 padding-left-none">Date<i class="fa fa-caret-down"></i></div>
                            </div>

                            <div class="table-data">
                                <div id="track_locations">
                                    @foreach($locations as $loc)
                                        <div class="table-row inner-block-bg">
                                            <div class="col-md-4 padding-left-none">{{$loc->tracking_location}}</div>
                                            <div class="col-md-4 padding-left-none loc_date">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                                        </div>
                                    @endforeach    
                                </div>    
                            </div>

                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-track']) !!}
                            <input type="hidden" name="tracking_confirm" value="1">

                            <div class="col-md-12 text-right btn-block">
                                <input type="submit" class="btn add-btn flat-btn" id="track_confirm" value="Confirm">
                            </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            @endif    

            @if($trackingExist==1 && !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Transit Details</h2>
                    </div>
                    <div class="detail-data padding-top">
                        <div class="table-div table-style1">
                            <div class="table-heading inner-block-bg">
                                <div class="col-md-6 padding-left-none">Location<i class="fa fa-caret-down"></i></div>
                                <div class="col-md-6 padding-left-none">Date<i class="fa fa-caret-down"></i></div>
                            </div>

                            <div class="table-data">
                                <div id="track_locations">
                                    @foreach($locations as $loc)
                                        <div class="table-row inner-block-bg">
                                            <div class="col-md-6 padding-left-none">{{$loc->tracking_location}}</div>
                                            <div class="col-md-6 padding-left-none loc_date">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                                        </div>
                                    @endforeach    
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            @endif

@endif

            @if(empty($deliveryExist) && $order->gsa_accepted==1 && ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) && ($vehicleExist==1))

                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Consignment Delivery Details</h2>
                    </div>
                    <div class="detail-data">
                        
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-delivery']) !!}
                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('delivery_date', '', ['id' => 'cdelivery_date','class'=>"calendar", 'placeholder' => 'Delivery Date','readonly' => true, 'class' => 'form-control']) !!}
                                        <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                        {!! Form::text('delivery_driver', '', ['id' => 'delivery_driver', 'placeholder' => 'Recipient Name', 'class' => 'form-control clsDrivername']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-phone"></i></span>
                                        {!! Form::text('delivery_mobile', '', ['id' => 'delivery_mobile', 'placeholder' => 'Recipient Mobile Number', 'class' => 'form-control clsMobileno']) !!}
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                        {!! Form::text('freight_amt', '', ['id' => 'freight_amt', 'placeholder' => 'Freight Amount (Optional)', 'class' => 'form-control fivedigitstwodecimals_deciVal']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                        {!! Form::text('delivery_info', '', ['id' => 'delivery_info', 'maxlength'=>'500', 'placeholder' => 'Additional Information (Optional)', 'class' => 'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-6 form-control-fld text-right">
                                    <input type="submit" class="btn add-btn" value="Confirm">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            @endif
            
            @if(!empty($deliveryExist) && $order->gsa_accepted==1 && ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) && ($vehicleExist==1))

                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Consignment Delivery Details</h2>
                    </div>
                    <div class="detail-data">
                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">


                            <div class="col-md-12">
                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Delivery Date</span>
                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_delivery_date))}}</span>
                                </div>

                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Recipient Name</span>
                                    <span class="data-value">{{$order->seller_delivery_driver_name}}</span>
                                </div>

                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Recipient Mobile</span>
                                    <span class="data-value">{{$order->seller_delivery_recipient_mobile}}</span>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-2 padding-left-none data-fld margin-bottom">
                                    <span class="side-head">Additional Fields Optional</span>
                                </div>

                                <div class="clearfix"></div>

                                @if($order->seller_delivery_frieght_amt)
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Freight Amount</span>
                                        <span class="data-value">{{$order->seller_delivery_frieght_amt}}</span>
                                    </div>
                                @endif

                                @if($order->seller_delivery_additional_details)
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Additional Informantion</span>
                                        <span class="data-value">{{$order->seller_delivery_additional_details}}</span>
                                    </div>
                                @endif
                            </div>
                                            
                        
        
                        </div>
                    </div>
                </div>
            @endif


            @if(!empty($invoiceExist) && $order->gsa_accepted==1 && !empty($deliveryExist)&& ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) && ($vehicleExist==1))
                <div class="accordian-blocks">
                    <div class="inner-block-bg inner-block-bg1 detail-head">
                        <h2 class="filter-head1">Invoice Generation</h2>
                    </div>
                    <div class="detail-data">
                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                            <div class="col-md-6 data-div order-detail-price-block">
                                <div><span class="vendor-name">Invoice</span></div>
                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Invoice No</span>
                                    <span class="data-value">{{$invoice->invoice_no}}</span>
                                </div>
                                @if(SHOW_SERVICE_TAX)
                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Services Charges</span>
                                    <span class="data-value">{{number_format($invoice->service_charge_amount,2)}}</span>
                                </div>                                
                                <div class="col-md-4 padding-left-none data-fld">
                                    <span class="data-head">Service Tax</span>
                                    <span class="data-value">{{number_format($invoice->service_tax_amount,2)}}</span>
                                </div>
                                @endif
                                <div class="clearfix"></div>
                                <div class="col-md-4 padding-left-none data-fld margin-top">
                                    <span class="data-head">Total Price</span>
                                    <span class="data-value big-value">{{number_format($invoice->total_amount,2)}}
                                        <br>
                                        @if(!SHOW_SERVICE_TAX)
                                        <span class="small serviceTax">(* Service Tax not included )</span>
                                        @endif
                                        </span>
                                </div>
                                 
                                
                                @if(empty($receiptExist) && $invoice->total_amount!=0)
                                    
                                    <div class="col-md-8 padding-none data-fld pull-right text-right">
                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-receipt']) !!}
                                            <br>
                                            <!--button class="btn add-btn flat-btn">make Payment</button-->
                                            <input type="button" name="receipt" id="receipt" class="btn add-btn flat-btn" data-toggle="modal" data-target="#payment" value="Make Payment">
                                            <input type="hidden" name="receipts" value="receipts">

                                        {!! Form::close() !!}
                                    </div>
                                @endif    
                            </div>

                            <div class="col-md-6 padding-top">
                                       
                            </div>

                            @if(!empty($receiptExist))
                                <div class="col-md-6 order-detail-price-block">
                                    <div><span class="vendor-name">Reciept</span></div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Receipt No</span>
                                        <span class="data-value">{{$receipt->receipt_no}}</span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Freight Amount</span>
                                        <span class="data-value">{{number_format($receipt->frieght_amount,2)}}/-</span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Insurance</span>
                                        <span class="data-value">{{number_format($receipt->insurance,2)}}/-</span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-8 padding-left-none data-fld">
                                        <span class="data-head">Payment Mode</span>
                                        <span class="data-value">{{$payment_mode}} xxxx xxxx xxxx 7377 on {{date("d/m/Y", strtotime($receipt->created_at))}}</span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Total Price</span>
                                        <span class="data-value big-value">{{number_format($receipt->total_amount,2)}}/-</span>
                                    </div>
                                </div>
                            @endif    

                            <div class="col-md-12 text-right btn-block">
                                <button class="btn add-btn flat-btn">Reverse</button>
                                
                                <input type="button" class="btn red-btn flat-btn" value="Confirm">
                            </div>

                                                        

                            <div class="col-md-12 text-right btn-block">
                                <!--button class="btn red-btn flat-btn">Confirm</button-->
                                <!--input type="button" class="btn red-btn flat-btn" value="Confirm" -->
                            </div>

                        </div>
                    </div>
                </div>
            @endif    


            </div>
            <!-- Right Section Ends Here -->

            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
@include('partials.gsa_consignment')
@include('partials.footer')
@endsection