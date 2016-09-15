@extends('app')
@section('content')
@include('partials.page_top_navigation')
@inject('sellercomp', 'App\Components\SellerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
    {{--*/ $sellerdata                =   $sellercomp->truckHaulSellerPostDetails($order->id) /*--}}
    {{--*/ $SellerPickupDate 	= 		$order->seller_pickup_date; /*--}}
    {{--*/ $buyerPickupDate 	=  		$order->buyer_consignment_pick_up_date; /*--}}
    {{--*/ $SellerDeliveryDate 	=  		$order->seller_delivery_date; /*--}}
    {{--*/ $DeliveryDate 	=  		$order->delivery_date; /*--}}
    {{--*/ $DispatchDate 	=  		$order->dispatch_date; /*--}}
    {{--*/ $current_date_seller	=  		date("Y-m-d");  /*--}} 
    {{--*/ $str			=		'' /*--}}       
    {{--*/ $strdelivery		=		'' /*--}}   
    {{--*/ $serviceId = Session::get('service_id') /*--}}
    {!! Form::hidden('serviceId', $serviceId, array('id' => 'serviceId')) !!}
    <div class="main">

        <div class="container">

            <span class="pull-left"><h1 class="page-title">Seller Consignment Pickup - {{$order->order_no}}</h1></span>
            <span class="pull-right"><a onclick="return checkSession(4,'/createseller');" href="#"><button class="btn post-btn pull-right">+ Post</button></a></span>

            <div class="filter-expand-block">

                <div class="search-block inner-block-bg margin-bottom-less-1">

                    <div class="from-to-area">

                    <span class="search-result">
                        <i class="fa fa-map-marker"></i>
                        <span class="location-text">{{$post->from}} to {{$post->to}}</span>
                    </span>
                    </div>
                    <div class="date-area">
                        <div class="col-md-12 padding-none">
                            <p class="search-head">Reporting Date & Time</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            <?php $order->dispatch_date=$order->buyer_consignment_pick_up_date;?>
                            {{date("j F Y", strtotime($order->buyer_consignment_pick_up_date))}} 
                            {{date("H:i A", strtotime($order->buyer_consignment_pick_up_time_from))}} - {{date("H:i A", strtotime($order->buyer_consignment_pick_up_time_to))}}
                        </span>
                        </div>
                        
                    </div>
                    <div>
                    <span class="search-result">
                        <p class="search-head">Vehicle Type</p>
                        <span class="location-text">{{$post->vehicle}}</span>
                    </span>
                    </div>
                    <div>
                    <span class="search-result">
                        <p class="search-head">Load Type</p>
                        <span class="location-text">{{$post->load}}</span>
                    </span>
                    </div>
                   

                    
                                    <!-- -status bar check variables and conditions -->
                                    {{--*/ $SellerPickupDate 	= 		$order->seller_pickup_date; /*--}}
                                    {{--*/ $buyerPickupDate 	=  		$order->buyer_consignment_pick_up_date; /*--}}
                                    {{--*/ $SellerDeliveryDate 	=  		$order->seller_delivery_date; /*--}}
                                    {{--*/ $DeliveryDate 	=  		$order->delivery_date; /*--}}
                                    {{--*/ $DispatchDate 	=  		$order->dispatch_date; /*--}}
                                    {{--*/ $current_date_seller	=  		date("Y-m-d");  /*--}} 
                                    {{--*/ $str			=		'' /*--}}       
                                    {{--*/ $strdelivery		=		'' /*--}}    
                                    
                                    
                                    {{--*/ $splitBuyepick = explode(" ",$order->buyer_consignment_pick_up_date) /*--}}  
                                    {{--*/ $splitpick = $splitBuyepick[0] /*--}}  
                                    
                                    @if($SellerDeliveryDate == '0000-00-00 00:00:00')
                                    	@if($current_date_seller < $splitpick)
                                    	{{--*/ $str				=		'' /*--}}       
                                    	{{--*/ $strdelivery		=		'' /*--}} 
                                    	@elseif($current_date_seller > $splitpick)
                                    	{{--*/ $str				=		'<div class="status-bar-left"></div>' /*--}}       
                                    	{{--*/ $strdelivery		=		'' /*--}} 
                                    	@elseif($current_date_seller == $splitpick)     
                                    	{{--*/ $str				=		'' /*--}}       
                                    	{{--*/ $strdelivery		=		'' /*--}}   
                                    	@endif
                                    @else                                   
                                        {{--*/ $splitTimeStamp = explode(" ",$SellerDeliveryDate) /*--}}  
                                        {{--*/ $splitdateDelivery = $splitTimeStamp[0] /*--}}  
                                        @if($splitdateDelivery == $current_date_seller)
                                        {{--*/ $sellerpickupcolor=		'green' /*--}}
                                    	{{--*/ $str				=		'<div class="status-bar-right-full"></div>' /*--}}
                                    	{{--*/ $strdelivery		=		'' /*--}}
                                    	@elseif($splitdateDelivery." 00:00:00" <= $buyerPickupDate." 00:00:00")
                                    	{{--*/ $sellerpickupcolor=		'green' /*--}}
                                    	{{--*/ $str				=		'<div class="status-bar-right-full"></div>' /*--}}
                                    	{{--*/ $strdelivery		=		'' /*--}}  
                                    	@elseif($splitdateDelivery." 00:00:00" > $buyerPickupDate." 00:00:00")
                                    	{{--*/ $sellerpickupcolor=		'red' /*--}}
                                    	{{--*/ $str				=		'<div class="status-bar-left-full"></div>' /*--}}
                                    	{{--*/ $strdelivery		=		'' /*--}}  
                                    	@endif
                                    @endif   
                   
                    
                    
                    <div>
                        <p class="search-head">Status</p>
                        <span class="search-result status-block">
                            <div class="status-bar">
                                <div class="status-bar">											
                                {!! $str !!}{!! $strdelivery !!}      		
                                <span class="status-text">
                                    @if($order->lkp_order_status_id == 2)
                                    {{--*/ $status		=	'Placement Due' /*--}} 
                                    @elseif($order->lkp_order_status_id == 3)
                                    {{--*/ $status		=	'Placed' /*--}} 
                                    @elseif($order->lkp_order_status_id == 6)
                                    {{--*/ $status		=	'Reported' /*--}} 
                                    @else
                                    {{--*/ $status		=	'Pending' /*--}} 
                                    @endif
                                    {{ $status }}
                                    
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
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Truck Haul Post Number</span>
                                <span class="data-value">{{$post->transid}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Buyer Name</span>
                                <span class="data-value">{{$post->name}}</span>
                            </div>
                            @if($order->buyer_consignor_name)
                                <div class="col-md-2 padding-left-none data-fld">
                                    <span class="data-head">Reporting To</span>
                                    <span class="data-value">{{$order->buyer_consignor_name}}</span>
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
                        @if(empty($vehicleExist) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Allotment</h2>
                                </div>
                                <div class="detail-data">
                                    @if(empty($vehicles))
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-sellerpickup']) !!}
                                        <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                        <div class="col-md-12 padding-none">

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                                    @if(isset($sellerdata->vehicle_number) && $sellerdata->vehicle_number!="")
                                                    {!! Form::text('vehicle', $sellerdata->vehicle_number, ['id' => 'vehicle','class'=>"form-control clsVehicleno", 'placeholder' => 'Vehicle Number*']) !!}
                                                    @else
                                                    {!! Form::text('vehicle','', ['id' => 'vehicle','class'=>"form-control clsVehicleno", 'placeholder' => 'Vehicle Number*']) !!}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                    {!! Form::text('driver', '', ['id' => 'driver','class'=>"form-control clsDrivername", 'placeholder' => 'Driver Name*']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-phone"></i></span>
                                                    {!! Form::text('mobile', '', ['id' => 'mobile','class'=>"form-control clsMobile", 'placeholder' => 'Driver Mobile Number*']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld text-right">
                                               <input type="submit" class="btn add-btn" id="add_vehicle" value="Add Vehicle">
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    @endif
                                    <!-- Table Starts Here -->
                                    
                                    <div class="table-div table-style1">

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-2 padding-left-none">Vehicle Number</div>
                                            <div class="col-md-2 padding-left-none">Driver Name</div>
                                            <div class="col-md-2 padding-left-none">Driver Mobile Number</div>
                                            <div class="col-md-6 padding-left-none"></div>											
                                        </div>

                                        <div class="table-data">

                                            <div id="pick_vehicles">
                                                @foreach($vehicles as $vehicle)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-2 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                        <div class="col-md-2 padding-left-none">{{$vehicle->driver_name}}</div>
                                                        <div class="col-md-2 padding-left-none">{{$vehicle->mobile}}</div>
                                                        <div class="col-md-6 padding-none text-right">
                                                        </div>
														
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>

                                        <div class="col-md-12 text-right btn-block">
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-vehicle']) !!}
                                            <input type="hidden" name="vehicle_confirm" value="1">
                                            <input type="submit" class="btn add-btn flat-btn truckhaul"  value="Confirm">
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif


                        @if(!empty($vehicleExist) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Allotment</h2>
                                </div>

                                <div class="detail-data">
                                    <div>&nbsp;</div>
                                    <div class="table-div table-style1">

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-2 padding-left-none">Vehicle Number</div>
                                            <div class="col-md-2 padding-left-none">Driver Name</div>
                                            <div class="col-md-2 padding-left-none">Driver Mobile Number</div>
					<div class="col-md-6 padding-left-none"></div>
                                        </div>

                                        <div class="table-data">

                                            @foreach($vehicles as $vehicle)
                                                <div class="table-row inner-block-bg">
                                                    <div class="col-md-2 padding-left-none">{{$vehicle->vehicle_no}}</div>
                                                    <div class="col-md-2 padding-left-none">{{$vehicle->driver_name}}</div>
                                                    <div class="col-md-2 padding-left-none">{{$vehicle->mobile}}</div>
                                                    <div class="col-md-6 padding-none text-right">
                                                        @include('partials._tracking') 
                                                    </div>
													
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <!-- Table Ends Here -->

                                </div>
                            </div>
                        @endif
						

                        @if(empty($deliveryExist)&& ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Reporting</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'reporting-form-delivery']) !!}
                                        <div class="col-md-12 padding-none">

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!! Form::text('delivery_date', \App\Components\CommonComponent::convertDateDisplay($order->buyer_consignment_pick_up_date), ['id' => 'cdelivery_date','class'=>"form-control calendar", 'placeholder' => 'Reporting Date *','max-date'=>$order->dispatch_date,'readonly' => true]) !!}
                                                    <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->dispatch_date))}}">
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend date" id="reporting_time_icon">
                                                    <span class="add-on"><i class="fa fa-clock-o"></i></span>
                                                    {!! Form::text('delivery_time', date("H:i", strtotime($order->buyer_consignment_pick_up_time_from)), ['id' => 'delivery_time','class'=>"form-control", 'placeholder' => 'Reporting Time *','readonly' => true]) !!}
                                                </div>
                                                <label class="error" id="err_reporting_time" for="err_reporting_time"></label>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                    {!! Form::text('delivery_driver', '', ['id' => 'delivery_driver','class'=>"form-control clsDrivername", 'placeholder' => 'Reporting to *']) !!}
                                                </div>
                                            </div>
                                            

                                            <div class="clearfix"></div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    
                                                    {!! Form::text('delivery_address', '', ['id' => 'delivery_address','class'=>"form-control form-control1", 'placeholder' => 'Address *']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                                    {!! Form::text('delivery_info', '', ['id' => 'delivery_info','class'=>"form-control clsAdditionalInfo", 'placeholder' => 'Additional Information (Optional)']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-6 form-control-fld text-right">
                                                <input type="submit" class="btn add-btn" value="Confirm">
                                            </div>

                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @endif


                        @if(!empty($deliveryExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Reporting</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            <div class="col-md-12">
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Reporting Date</span>
                                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_delivery_date))}}</span>
                                                </div>
                                                
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Reporting Time</span>
                                                    <span class="data-value">{{date("H:i A", strtotime($order->seller_delivery_date))}}</span>
                                                </div>
                                                
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Reporting To</span>
                                                    <span class="data-value">{{$order->seller_delivery_driver_name}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Address</span>
                                                    <span class="data-value">{{$order->seller_delivery_address}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Additional Informantion</span>
                                                    <span class="data-value">{{$order->seller_delivery_additional_details}}</span>
                                                </div>

                                            </div>
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