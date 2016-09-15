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
    {{--*/ $serviceId = Session::get('service_id') /*--}}
    {!! Form::hidden('serviceId', $serviceId, array('id' => 'serviceId')) !!}
	
	
    <div class="main">

        <div class="container">

            <span class="pull-left"><h1 class="page-title">Seller Consignment Pickup - {{$order->order_no}}</h1></span>
            <span class="pull-right"><a onclick="return checkSession(1,'/createseller');" href="#"><button class="btn post-btn pull-right">+ Post</button></a></span>

            <div class="filter-expand-block">

                <div class="search-block inner-block-bg margin-bottom-less-1">

                    <div class="from-to-area">

                    <span class="search-result">
                        <i class="fa fa-map-marker"></i>
                        <span class="location-text">{{$post->from}} to {{$post->to}}</span>
                    </span>
                    </div>
                    <div class="date-area">
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Dispatch Date</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            <?php $order->dispatch_date=$order->buyer_consignment_pick_up_date;?>
                            {{date("d/m/Y", strtotime($order->buyer_consignment_pick_up_date))}}
                        </span>
                        </div>
                        <div class="col-md-6 padding-none">
                            <p class="search-head">Delivery Date</p>
                        <span class="search-result">
                            <i class="fa fa-calendar-o"></i>
                            {{date("d/m/Y", strtotime($order->delivery_date))}}
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
                    <div>
                    <span class="search-result">
                        <p class="search-head">No of Loads</p>
                        <span class="location-text">
                            @if(isset($post->number_loads) && $post->number_loads!='' && $post->number_loads!='undefined')
                                {!! $post->number_loads !!} @else  N/A 
                            @endif
                            
                        </span>
                    </span>
                    </div>
                    
                    <!--div>
                        <p class="search-head">Status</p>
                    <span class="search-result status-block">
                        <div class="status-bar">
                            <div class="status-bar-left"></div>
                            <span class="status-text">{{$order->order_status}}</span>
                        </div>
                    </span>
                    </div-->
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

                <!--toggle div starts-->
                <div class="show-trans-details-div-expand trans-details-expand">
                    <div class="expand-block">
                        <div class="col-md-12">
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Full Truck Post Number</span>
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
                        @include('partials.is_gsa_consignment_accepted')

                        @if(empty($vehicleExist) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Placement</h2>
                                </div>

                                <div class="detail-data">
                                    @if(isset($order->number_loads) && $order->number_loads!='undefined' && $order->number_loads!='' )
                                        @if($order->number_loads>count($vehicles))
                                            <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-sellerpickup']) !!}
                                            <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                            <div class="col-md-12 padding-none">

                                                <div class="col-md-3 form-control-fld">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="fa fa-truck"></i></span>
                                                        <!-- input type="text" placeholder="Vehicle Number" id="" class="form-control" -->
                                                        {!! Form::text('vehicle', '', ['id' => 'vehicle','class'=>"form-control clsVehicleno", 'placeholder' => 'Vehicle Number*']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-control-fld">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                        <!-- input type="text" placeholder="Driver Name" id="" class="form-control" -->
                                                        {!! Form::text('driver', '', ['id' => 'driver','class'=>"form-control clsDrivername", 'placeholder' => 'Driver Name*']) !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-3 form-control-fld">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="fa fa-phone"></i></span>
                                                        <!-- input type="text" placeholder="Driver Mobile Number" id="" class="form-control" -->

                                                        {!! Form::text('mobile', '', ['id' => 'mobile','class'=>"form-control clsMobileno", 'placeholder' => 'Driver Mobile Number*']) !!}

                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-control-fld text-right">
                                                    <!-- button class="btn add-btn">Add Vehicle</button -->

						<input type="hidden" name="truckhaul_valid_from" id="truckhaul_valid_from" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'from_date',$order->id) }}">
						<input type="hidden" name="truckhaul_valid_to" id="truckhaul_valid_to" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'to_date',$order->id) }}">
						<input type="hidden" name="truckhaul_from_location_id" id="truckhaul_from_location_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'to_location_id',$order->id) }}">
						<input type="hidden" name="truckhaul_to_location_id" id="truckhaul_to_location_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'from_location_id',$order->id) }}">
						<input type="hidden" name="truckhaul_district_id" id="truckhaul_district_id" value="{{ $common->getDistrict($common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'to_location_id',$order->id),$serviceId,$type = 2) }}">						
						<input type="hidden" name="truckhaul_load_type_id" id="truckhaul_load_type_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'lkp_load_type_id',$order->id) }}">
						<input type="hidden" name="truckhaul_vehicle_type_id" id="truckhaul_vehicle_type_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'lkp_vehicle_type_id',$order->id) }}">
						<input type="hidden" name="truckhaul_price" id="truckhaul_price" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'price',$order->id) }}">
						<input type="hidden" name="truckhaul_transit_days" id="truckhaul_transit_days" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'transitdays') }}">	
						<input type="hidden" name="truckhaul_vehicle_no" id="truckhaul_vehicle_no" value="">
						<input type="hidden" name="truckhaul_order_no" id="truckhaul_order_no" value="{{ $order->id }}">
					{{--*/ $sellerTruckHaulService = ''; /*--}} 
					{{--*/ $sellerTruckHaulService = $common->checkSellerForTruckHaul(Auth::user()->id) /*--}}
					@if($sellerTruckHaulService == '1')
                                            <input type="hidden" name="truckhaul_confirm" id="truckhaul_confirm" value="1">
					@elseif($sellerTruckHaulService == '0')
					    <input type="hidden" name="truckhaul_confirm" id="truckhaul_confirm" value="0">					     
					@endif
 <input type="hidden" name="add_truck_flag" id="add_truck_flag" value="">
<input type="submit" class="btn add-btn" id="add_vehicle" value="Add Vehicle">
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                            </div>
                                        @endif
                                    @else
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-sellerpickup']) !!}
                                        <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                        <div class="col-md-12 padding-none">

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                                    <!-- input type="text" placeholder="Vehicle Number" id="" class="form-control" -->
                                                    {!! Form::text('vehicle', '', ['id' => 'vehicle','class'=>"form-control clsVehicleno", 'placeholder' => 'Vehicle Number*']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                    <!-- input type="text" placeholder="Driver Name" id="" class="form-control" -->
                                                    {!! Form::text('driver', '', ['id' => 'driver','class'=>"form-control clsDrivername", 'placeholder' => 'Driver Name*']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-phone"></i></span>
                                                    <!-- input type="text" placeholder="Driver Mobile Number" id="" class="form-control" -->

                                                    {!! Form::text('mobile', '', ['id' => 'mobile','class'=>"form-control clsMobileno", 'placeholder' => 'Driver Mobile Number*']) !!}

                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld text-right">
                                                <!-- button class="btn add-btn">Add Vehicle</button -->

 <input type="hidden" name="truckhaul_valid_from" id="truckhaul_valid_from" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'from_date') }}">
						<input type="hidden" name="truckhaul_valid_to" id="truckhaul_valid_to" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'to_date') }}">
						<input type="hidden" name="truckhaul_from_location_id" id="truckhaul_from_location_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'from_location_id') }}">
						<input type="hidden" name="truckhaul_to_location_id" id="truckhaul_to_location_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'to_location_id') }}">
						<input type="hidden" name="truckhaul_district_id" id="truckhaul_district_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'lkp_district_id') }}">						
						<input type="hidden" name="truckhaul_load_type_id" id="truckhaul_load_type_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'lkp_load_type_id') }}">
						<input type="hidden" name="truckhaul_vehicle_type_id" id="truckhaul_vehicle_type_id" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'lkp_vehicle_type_id') }}">
						<input type="hidden" name="truckhaul_price" id="truckhaul_price" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'price') }}">
						<input type="hidden" name="truckhaul_transit_days" id="truckhaul_transit_days" value="{{ $common->getFTlSellerPostItForTruckHaul($order->seller_post_item_id,'transitdays') }}">	
						<input type="hidden" name="truckhaul_vehicle_no" id="truckhaul_vehicle_no" value="">
						<input type="hidden" name="truckhaul_order_no" id="truckhaul_order_no" value="{{ $order->id }}">
					{{--*/ $sellerTruckHaulService = ''; /*--}} 
					{{--*/ $sellerTruckHaulService = $common->checkSellerForTruckHaul(Auth::user()->id) /*--}}
					@if($sellerTruckHaulService == '1')
                                            <input type="hidden" name="truckhaul_confirm" id="truckhaul_confirm" value="1">
					@elseif($sellerTruckHaulService == '0')
					    <input type="hidden" name="truckhaul_confirm" id="truckhaul_confirm" value="0">					     
					@endif
 <input type="hidden" name="add_truck_flag" id="add_truck_flag" value="">
<input type="submit" class="btn add-btn" id="add_vehicle" value="Add Vehicle">
                                               
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    @endif
                                    <!-- Table Starts Here -->

                                    <div class="table-div table-style1">

                                        <!-- Table Head Starts Here -->

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-2 padding-left-none">Vehicle Number</div>
                                            <div class="col-md-2 padding-left-none">Driver Name</div>
                                            <div class="col-md-2 padding-left-none">Driver Mobile Number</div>
                                            <div class="col-md-6 padding-left-none"></div>											
                                        </div>

                                        <!-- Table Head Ends Here -->

                                        <div class="table-data">
                                            <!-- Table Row Ends Here -->
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
                                            <!-- button class="btn add-btn flat-btn">Confirm</button -->
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-vehicle']) !!}
                                            <input type="hidden" name="vehicle_confirm" value="1">
                                            <input type="submit" class="btn add-btn flat-btn truckhaul"  value="Confirm">
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                    <!-- Table Ends Here -->

                                </div>
                            </div>
                        @endif


                        @if(!empty($vehicleExist) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Vehicle Placement</h2>
                                </div>

                                <div class="detail-data">
                                    <div>&nbsp;</div>
                                    <!-- Table Starts Here -->

                                    <div class="table-div table-style1">

                                        <!-- Table Head Starts Here -->

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-2 padding-left-none">Vehicle Number</div>
                                            <div class="col-md-2 padding-left-none">Driver Name</div>
                                            <div class="col-md-2 padding-left-none">Driver Mobile Number</div>
											<div class="col-md-6 padding-left-none"></div>
                                        </div>

                                        <!-- Table Head Ends Here -->

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

                        @if(empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Consignment Pickup</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-pickup']) !!}
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    <!-- input type="text" placeholder="Pickup Date" id="" class="form-control"-->
                                                    {!! Form::text('pick_date', '', ['id' => 'pick_date','class'=>"form-control calendar from-date-control", 'placeholder' => 'Pickup Date*','min-date'=>date('Y-m-d', strtotime('-1 day', strtotime($order->dispatch_date))),'max-date'=>$order->delivery_date,'readonly' => true]) !!}
                                                    <input type='hidden' id='cpick' value="{{date('d/m/Y', strtotime('-1 day', strtotime($order->dispatch_date)))}}">
                                                    <input type='hidden' id='cdelivery' value="{{date('d/m/Y', strtotime($order->delivery_date))}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-gg"></i></span>
                                                    <!-- input type="text" placeholder="LR Number" id="" class="form-control" -->
                                                    {!! Form::text('lr_no', '', ['id' => 'lr_no','class'=>"form-control clsLRnumber", 'placeholder' => 'LR Number*']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    <!-- input type="text" placeholder="LR Date" id="" class="form-control"-->
                                                    {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"form-control calendar to-date-control", 'placeholder' => 'LR Date*','readonly' => true]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-gg"></i></span>
                                                    <!-- input type="text" placeholder="Transporter Bill Number" id="" class="form-control" -->
                                                    {!! Form::text('bill_no', '', ['id' => 'bill_no','class'=>"form-control clsTransporterBill", 'placeholder' => 'Transporter bill no.*']) !!}
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                                    <!--input type="text" placeholder="Customer Document 1 (Optional)" id="" class="form-control"-->
                                                    {!! Form::text('info1', '', ['id' => 'info1','class'=>"form-control clsCustDocs", 'placeholder' => 'Customer Document 1 (Optional)']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                                    <!--input type="text" placeholder="Customer Document 2 (Optional)" id="" class="form-control"-->
                                                    {!! Form::text('info2', '', ['id' => 'info2','class'=>"form-control clsCustDocs", 'placeholder' => 'Customer Document 2 (Optional)']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-control-fld text-right">
                                                <!--button class="btn add-btn">Confirm</button-->
                                                <input type="submit" class="btn add-btn" value="Confirm">
                                            </div>
                                            {!! Form::close() !!}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($pickExist)&& ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Consignment Pickup</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            <div class="col-md-12">
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Pickup Date</span>
                                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_date))}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">LR Number:</span>
                                                    <span class="data-value">{{$order->seller_pickup_lr_number}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">LR Date:</span>
                                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_lr_date))}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Transporter bill no:</span>
                                                    <span class="data-value">{{$order->seller_pickup_transport_bill_no}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Customer Document 1 (Optional):</span>
                                                    <span class="data-value">{{$order->seller_pickup_customer_doc_one}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Customer Document 2 (Optional)</span>
                                                    <span class="data-value">{{$order->seller_pickup_customer_doc_two}}</span>
                                                </div>


                                            </div>
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
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-tracklocation']) !!}
                                            <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                    <!-- input type="text" placeholder="Location" id="" class="form-control"-->
                                                    {!! Form::text('location', '', ['id' => 'location','class'=>"form-control alphanumericspace_strVal",'required'=>'required', 'placeholder' => 'Location*']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    <!--input type="text" placeholder="Date" id="" class="form-control"-->
                                                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                                    {!! Form::text('date', '', ['id' => 'date','class'=>"form-control calendar from-date-control", 'placeholder' => 'Date*','readonly' => true]) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-6 form-control-fld text-right">
                                                <!-- button class="btn add-btn">Add</button-->
                                                <input type="submit" class="btn add-btn" value="Add">
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>



                                    <!-- Table Starts Here -->

                                    <div class="table-div table-style1">

                                        <!-- Table Head Starts Here -->

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-4 padding-left-none">Location</div>
                                            <div class="col-md-8 padding-left-none">Date</div>
                                        </div>

                                        <!-- Table Head Ends Here -->

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

                                        <div class="col-md-12 text-right btn-block">
                                            {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-track']) !!}
                                                    <!--button class="btn add-btn flat-btn">Confirm</button-->
                                            <input type="hidden" name="tracking_confirm" value="1">
                                            <input type="submit" class="btn add-btn flat-btn" id="track_confirm" value="Confirm">
                                            {!! Form::close() !!}
                                        </div>

                                    </div>

                                    <!-- Table Ends Here -->




                                </div>
                            </div>
                        @endif



                        @if($trackingExist==1 && !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Transit Details</h2>
                                </div>

                                <div class="detail-data">
                                    <div>&nbsp;</div>
                                    <!-- Table Starts Here -->

                                    <div class="table-div table-style1">

                                        <!-- Table Head Starts Here -->

                                        <div class="table-heading inner-block-bg">
                                            <div class="col-md-4 padding-left-none">Location</div>
                                            <div class="col-md-8 padding-left-none">Date</div>
                                        </div>

                                        <!-- Table Head Ends Here -->

                                        <div class="table-data">

                                            <!-- Table Row Starts Here -->
                                            <div id="track_locations">
                                                @foreach($locations as $loc)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-4 padding-left-none">{{$loc->tracking_location}}</div>
                                                        <div class="col-md-4 padding-left-none loc_date">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

@endif

                        @if(empty($deliveryExist)&& ($trackingExist==1 ||  (isset($tracking) && $tracking==2)) && !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Consignment Delivery Details</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-delivery']) !!}
                                        <div class="col-md-12 padding-none">

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    <!-- input type="text" placeholder="Date" id="" class="form-control"-->
                                                    {!! Form::text('delivery_date', '', ['id' => 'cdelivery_date','class'=>"form-control calendar from-date-control", 'placeholder' => 'Delivery Date*','max-date'=>$order->dispatch_date,'readonly' => true]) !!}
                                                    <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                                    <!-- input type="text" class="form-control" id="" placeholder="Driver Name" -->
                                                    {!! Form::text('delivery_driver', '', ['id' => 'delivery_driver','class'=>"form-control clsRecipientName", 'placeholder' => 'Recipient Name*']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-phone"></i></span>
                                                    <!-- input type="text" class="form-control" id="" placeholder="Recipient Mobile Number" -->
                                                    {!! Form::text('delivery_mobile', '', ['id' => 'delivery_mobile','class'=>"form-control clsMobileno", 'placeholder' => 'Recipient Mobile Number*']) !!}
                                                </div>
                                            </div>

                                            <div class="clearfix"></div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                                    <!-- input type="text" class="form-control" id="" placeholder="Freight Amount (Optional)" -->
                                                    {!! Form::text('freight_amt', '', ['id' => 'freight_amt','class'=>"form-control clsFTLFreightAmount", 'placeholder' => 'Freight Amount (Optional)']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                                    <!--input type="text" class="form-control" id="" placeholder="Additional Information (Optional)"-->
                                                    {!! Form::text('delivery_info', '', ['id' => 'delivery_info','class'=>"form-control clsConsignAddInfo", 'placeholder' => 'Additional Information (Optional)']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-6 form-control-fld text-right">
                                                <!--button class="btn add-btn">Confirm</button-->
                                                <input type="submit" class="btn add-btn" value="Confirm">
                                            </div>

                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @endif


                        @if(!empty($deliveryExist)&& ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Consignment Delivery Details</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-12 padding-none">
                                            <div class="col-md-12">
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Delivery Date</span>
                                                    <span class="data-value">{{date("d/m/Y", strtotime($order->seller_delivery_date))}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Recipient Name</span>
                                                    <span class="data-value">{{$order->seller_delivery_driver_name}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Recipient Mobile</span>
                                                    <span class="data-value">{{$order->seller_delivery_recipient_mobile}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Freight Amount</span>
                                                    <span class="data-value">{{$order->seller_delivery_frieght_amt}}</span>
                                                </div>

                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Additional Information</span>
                                                    <span class="data-value">{{$order->seller_delivery_additional_details}}</span>
                                                </div>


                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        @endif


                        @if(!empty($invoiceExist) && !empty($deliveryExist) && ($trackingExist==1 ||  (isset($tracking) && $tracking==2)) && !empty($pickExist) && ($vehicleExist==1) && $order->gsa_accepted==1)

                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">
                                    <h2 class="filter-head1">Invoice Generation</h2>
                                </div>
                                <div class="detail-data">
                                    <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                        <div class="col-md-6 data-div order-detail-price-block">
                                            <div><span class="vendor-name">Invoice</span></div>
                                            <div class="col-md-6 padding-left-none data-fld">
                                                <span class="data-head">Invoice No</span>
                                                <span class="data-value">{{$invoice->invoice_no}}</span>
                                            </div>
                                            <div class="col-md-6 padding-left-none data-fld">
                                                <span class="data-head">Services Charges</span>
                                                <span class="data-value">{{number_format($invoice->service_charge_amount,2)}}</span>
                                            </div>
                                            @if(SHOW_SERVICE_TAX)
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Service Tax</span>
                                                    <span class="data-value">{{number_format($invoice->service_tax_amount,2)}}</span>
                                                </div>
                                            @endif 
                                            

                                            <div class="col-md-6 padding-left-none data-fld">
                                                <span class="data-head">Total Price</span>
                                                <span class="data-value big-value">{{number_format($invoice->total_amount,2)}}
                                                
                                                <br>
                                                @if(!SHOW_SERVICE_TAX)
                                                <span class="small serviceTax">(* Service Tax not included )</span>
                                                @endif
                        
                                                </span>
                                            </div>
                                            
                            
                                            @if(empty($receiptExist) && $invoice->total_amount!=0)
                                            <div class="col-md-8 padding-none data-fld text-right">
                                                <br>
                                                {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-receipt']) !!}
                                                        <!-- button class="btn add-btn flat-btn">make Payment</button-->
                                                <input type="button" name="receipt" id="receipt" class="btn add-btn flat-btn" data-toggle="modal" data-target="#payment" value="Make Payment">
                                                <input type="hidden" name="receipts" value="receipts">

                                                {!! Form::close() !!}
                                            </div>
											@endif	

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
                                                <div class="col-md-8 padding-left-none data-fld">
                                                    <span class="data-head">Payment Mode</span>
                                                    <span class="data-value">{{$payment_mode}} xxxx xxxx xxxx 7377 on {{date("d/m/Y", strtotime($receipt->created_at))}}</span>
                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Total Price</span>
                                                    <span class="data-value big-value">{{number_format($receipt->total_amount,2)}}/-</span>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-right btn-block">
                                                <button class="btn add-btn flat-btn">Reverse</button>
                                                <button class="btn red-btn flat-btn">Confirm</button>
                                            </div>

                                            <!-- div class="col-md-12 order-detail-price-block">
                                                <div><span class="vendor-name">Reciept</span></div>
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Receipt No</span>
                                                    <span class="data-value">{{$receipt->receipt_no}}</span>
                                                </div>
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Freight Amount</span>
                                                    <span class="data-value">{{number_format($receipt->frieght_amount,2)}}/-</span>
                                                </div>
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Insurance</span>
                                                    <span class="data-value">{{number_format($receipt->insurance,2)}}/-</span>
                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Payment Mode</span>
                                                    <span class="data-value">{{$payment_mode}} xxxx xxxx xxxx 7377 on {{date("d/m/Y", strtotime($receipt->created_at))}}</span>
                                                </div>
                                                <div class="col-md-2 padding-left-none data-fld">
                                                    <span class="data-head">Total Price</span>
                                                    <span class="data-value big-value">{{number_format($receipt->total_amount,2)}}/-</span>
                                                </div>
                                            </div-->
                                        @endif

                                        <div class="col-md-12 text-right btn-block">
                                            <!--button class="btn red-btn flat-btn">Confirm</button-->
                                            <!-- input type="button" class="btn red-btn flat-btn" value="Confirm" -->
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
