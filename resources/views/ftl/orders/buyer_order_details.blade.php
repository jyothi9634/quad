@inject('messagesComponent', 'App\Components\MessagesComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $countMessages = 0 /*--}}
{{--*/ $countMessages = $messagesComponent->getPerticularMessageDetailsCount(null,$orderDetails->orderid) /*--}}

{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(3,$serviceId,0); /*--}}      
{{--*/ $docCount = count($docs_buyer) /*--}}
    
<div class="main">
    <div class="container">
        @if (Session::has('cancelsuccessmessage'))
        <div class="flash">
            <p class="text-success col-sm-12 text-center flash-txt alert-success">
                {{ Session::get('cancelsuccessmessage') }}</p>
        </div>
        @endif
      
        <span class="pull-left"><h1 class="page-title">Order - {!! $orderDetails->order_no !!}</h1></span>
<?php //echo "<pre>"; print_r($orderDetails); exit;?>
        @include('partials.content_top_navigation_links')

        
            <div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            <div class="inner-block-bg inner-block-bg1">
                                <div class="col-md-12 tab-modal-head">
                                    <h3>
                                        <i class="fa fa-map-marker"></i> 
                                        @if($orderDetails->from_city)
                                        {!! $orderDetails->from_city!!}
                                        @else &nbsp;
                                         @endif 
                                        @if(isset($orderDetails->to_city) && $orderDetails->to_city)
                                         to 
                                        {!! $orderDetails->to_city !!}
                                        @else &nbsp;
                                        @endif
                                    </h3>
                                </div>
                                <div class="col-md-8 data-div">
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Dispatch Date</span>
                                        <span class="data-value">
                                            @if(isset($orderDetails->dispatch_date))
                                            {{date("d/m/Y", strtotime($orderDetails->dispatch_date))}}
                                            @else 
                                            &nbsp;
                                            @endif
                                            <!--@if(isset($orderDetails->buyer_consignment_pick_up_date) && isset($orderDetails->orderdeliverydate))
                                            {{date("d/m/Y", strtotime($orderDetails->buyer_consignment_pick_up_date))}} - {{date("d/m/Y", strtotime($orderDetails->orderdeliverydate))}}
                                        @else &nbsp;
                                                @endif-->
                                        </span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Consignee</span>
                                        <span class="data-value">
                                            @if($orderDetails->buyer_consignee_name)
                                            {!! $orderDetails->buyer_consignee_name !!}
                                            @else &nbsp;
                                            @endif
                                        </span>
                                    </div>
                                    @if(Session::get('service_id') != RELOCATION_DOMESTIC && Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Type</span>
                                        <span class="data-value">
                                            @if($orderDetails->vehicle_type)
                                            {!! $orderDetails->vehicle_type !!}
                                            @else &nbsp;
                                                @endif
                                        </span>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Load Type</span>
                                        <span class="data-value">
                                            @if($orderDetails->load_type)
                                            {!! $orderDetails->load_type !!}
                                            @else &nbsp;
                                                @endif
                                        </span>
                                    </div>
                                    @if(isset($tracking))
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Tracking</span>
                                        <span class="data-value">@if( $tracking == 1)
									Milestone
                        			@elseif( $tracking == 2) 
                        			Real Time
                                                @else &nbsp;
                            		@endif</span>
                                    </div>
                                    @endif
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">FTL Post No</span>
                                        <span class="data-value">
                                            @if($orderDetails->trans_id)
                                            {!! $orderDetails->trans_id !!}
                                        @else &nbsp;
                                                @endif
                                        </span>
                                    </div>
                                    @else

                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Relocation Post No</span>
                                        <span class="data-value">
                                            @if($orderDetails->postid)
                                            {!! $orderDetails->postid !!}
                                        	@else &nbsp;
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @endif
                                    <div class="col-md-4 padding-left-none data-fld">
                                        @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                            <span class="data-head">Volume</span> 
                                            <span class="data-value">
                                            @if($orderDetails->buyer_quote_id!='')
                                            {{--*/ $volume_total = $commonComponent->getVolumeCft($orderDetails->buyer_quote_id)+$commonComponent->getCratingVolumeCft($orderDetails->buyer_quote_id) /*--}}
                                            @else
                                            {{--*/ $volume_total = $commonComponent->getTermVolumeCft($orderDetails->term_contract_id) /*--}}
                                            @endif
                                            @if($volume_total!=0)
                                                {{$volume_total}}
                                            @else
                                                N/A 
                                            @endif
                                             </span>
                                        @elseif(Session::get('service_id')==RELOCATION_OFFICE_MOVE)
                                         <span class="data-head">Volume</span> 
                                            <span class="data-value">
                                            @if($orderDetails->buyer_quote_id!='')
                                                 {{--*/  $volume = $commonComponent->getOfficeBuyerVolume($orderDetails->buyer_quote_id) /*--}}
                                                 @if(!empty($volume))
                                                    {{$volume}}
                                                 @else
                                                    N/A 
                                                 @endif
                                            @else
                                                N/A 
                                            @endif
                                             </span>
                                        @else
                                            <span class="data-head">Quantity</span> 
                                            <span  class="data-value">
                                                @if(isset($orderDetails->quantity) && $orderDetails->quantity!='')
                                                    {!! $orderDetails->quantity !!} @else  N/A 
                                                @endif

                                                @if(isset($orderDetails->units) && $orderDetails->units!='')
                                                    {!! $orderDetails->units !!} 
                                                @endif
                                             </span>
                                        @endif		 
                                    </div>
                                    @if(Session::get('service_id')==ROAD_FTL)
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">No of Loads</span> 
                                        <span class="data-value">
                                            @if(isset($orderDetails->number_loads) && $orderDetails->number_loads!='' && $orderDetails->number_loads!='undefined')
                                                {!! $orderDetails->number_loads !!} @else  N/A 
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date</span>
                                    <span class="data-result">
                                        <i class="fa fa-calendar-o"></i>
                                        {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}
                                    </span>
                                    </div>
                                    
                                    @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                    <div class="col-md-4 padding-left-none data-fld">
                                            <span class="data-head">Post Type</span> <span
                                                    class="data-value">
                                                    @if(isset($orderDetails->lkp_post_ratecard_type_id))
                                                    @if($orderDetails->lkp_post_ratecard_type_id==1) 
                                                    HHG
                                                    @else 
                                                    Vehicle
                                                    @endif
                                                    @endif
                                            </span>
                                    </div>
                                    @endif
                                    <div class="clearfix"></div>
                                    
                                    @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                    <div class="col-md-4 padding-left-none data-fld">
                                        @if($orderDetails->lkp_post_ratecard_type_id==1) 
                                                <span class="data-head">Load Type</span> <span
                                                        class="data-value">
                                                        @if(isset($orderDetails->lkp_post_ratecard_type_id))
                                                        @if($orderDetails->lkp_load_category_id==1) 
                                                        Full Load
                                                        @else 
                                                        Part Load
                                                        @endif
                                                        @endif
                                                        </span>
                                        @else
                                                <span class="data-head">Vehicle Catagoery</span> <span
                                                        class="data-value">
                                                        @if(isset($orderDetails->lkp_vehicle_category_id))
                                                        @if($orderDetails->lkp_vehicle_category_id==1) 
                                                        Car
                                                        @else 
                                                        Bike / Scooter / Scooty
                                                        @endif
                                                        @endif

                                                        </span>	
                                        @endif		
                                    </div>
                                    @endif	
                                    
                                    
                                    <div class="col-md-10 padding-none pull-left">
                                        <div class="info-links">
                                            <a href="{{ url('/getmessagedetails/0/'.$orderDetails->orderid.'/0')}}" class="tabs-showdiv" ><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
                                            <a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
                                            <a href="#" data-toggle="modal" data-target="#ftl-doc-popup"><i class="fa fa-file-text-o"></i> Documents<span class="badge">{{ $docCount }}</span></a>                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-4 order-detail-price-block">
                                    <div>

                                        <span class="vendor-name">{{$orderDetails->username}}</span>

                                    </div>
									
                                    <div>
                                        <span class="data-head">Total Price</span>
                                        <span class="data-value big-value">Rs. 
                                            @if($orderDetails->inv_total) {!!
                                                number_format($orderDetails->inv_total,2) !!}
                                            @else
                                                {!! number_format($orderDetails->orderprice,2) !!} 
                                            @endif
                                            </span>
                                    </div>
                                    
                                    
                                    <!-- -status bar check variables and conditions -->
                                    {{--*/ $SellerPickupDate 	= 		$orderDetails->seller_pickup_date; /*--}}
                                    {{--*/ $buyerPickupDate 	=  		$orderDetails->buyer_consignment_pick_up_date; /*--}}
                                    {{--*/ $SellerDeliveryDate 	=  		$orderDetails->seller_delivery_date; /*--}}
                                    {{--*/ $DeliveryDate 		=  		$orderDetails->delivery_date; /*--}}
                                    {{--*/ $DispatchDate 		=  		$orderDetails->dispatch_date; /*--}}
                                    {{--*/ $current_date_seller	=  		date("Y-m-d");  /*--}} 
                                    {{--*/ $str				=		'' /*--}}       
                                    {{--*/ $strdelivery				=		'' /*--}}    
                                    
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

                                    <div class="col-md-4 padding-none">
                                    	<span class="data-head">Status</span>
                                    	<div class="status-bar">
	                                    	<div class="status-bar">												
	            									{!! $str !!}{!! $strdelivery !!}      		
												<span class="status-text">{!! $orderDetails->order_status !!}</span>												
											</div>    
                                   		 </div>                                        
                                    </div>
                                    
                                </div>
                            </div>
                            <?php /*@if (strtotime($cancel_book_date)>(strtotime(date ( 'Y-m-d H:i:s' ))+(24*3600) ) && ($orderDetails->order_status!='Cancelled' && $orderDetails->order_status!='Delivered' && $orderDetails->order_status!='In transit' && $orderDetails->order_status!='Reached destination' ))*/ ?>
                            @if (strtotime($cancel_book_date)>(strtotime(date ( 'Y-m-d H:i:s' ))+(24*3600) ) && ($orderDetails->order_status!='Cancelled' && $orderDetails->order_status!='Delivered' && $orderDetails->order_status!='Intransit' && $orderDetails->order_status!='Reached destination' ))
                            <div class="col-md-12 col-sm-12 col-xs-12 text-right padding-right-none margin-bottom">
                                <a class="btn post-btn pull-right" data-target="#cancelordermodal" data-toggle="modal" onclick="setorderid({{$orderDetails->orderid}})"><span >Cancel Booking</span></a>
                                                </div>
                            @endif
                            <div class="clearfix"></div>
                            <div class="col-md-12 inner-block-bg inner-block-bg1">
                                <div class="col-md-4 padding-none">
                                    <div class="center-block pull-left">
                                        <i class="fa fa-print"></i>
                                        <span>Print Order</span>
                                    </div>
                                </div>

                                <div class="col-md-4 padding-none">
                                    <div class="center-block">
                                        <i class="fa fa-file-text-o"></i>
                                        <span>Email Invoice</span>
                                    </div>
                                </div>

                                <div class="col-md-4 padding-none">
                                    <div class="center-block pull-right">
                                        <i class="fa fa-phone"></i>
                                        <span>Contact Us</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!--blocks added from seller-->
                            <div class="accordian-blocks">
                                    <div class="inner-block-bg inner-block-bg1 detail-head">Order No.
                                            {!!$orderDetails->order_no !!}</div>
                                    <div class="detail-data" style="display: none;">
                                            <div class="col-md-12 margin-top margin-bottom">
                                                    
                                                    <div class="col-md-12 padding-left-none data-fld">
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Pickup Date</span> <span
                                                                            class="data-value">@if($orderDetails->seller_pickup_lr_date!=
                                                                            '0000-00-00 00:00:00') {{date("d/m/Y",
                                                                            strtotime($orderDetails->seller_pickup_date))}} @else{!! '-'
                                                                            !!} @endif</span>
                                                            </div>
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">LR Number</span> <span
                                                                            class="data-value">@if($orderDetails->seller_pickup_lr_number)
                                                                            {!! $orderDetails->seller_pickup_lr_number !!} @else{!! '-'
                                                                            !!} @endif</span>
                                                            </div>

                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Transporter bill no.</span> <span
                                                                            class="data-value">@if($orderDetails->seller_pickup_transport_bill_no)
                                                                            {!! $orderDetails->seller_pickup_transport_bill_no !!}
                                                                            @else{!! '-' !!} @endif</span>
                                                            </div>
                                                    </div>




                                                    <div class="col-md-12 padding-left-none data-fld">
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Delivery on</span> <span
                                                                            class="data-value">@if($orderDetails->seller_delivery_date !=
                                                                            '0000-00-00 00:00:00') {{date("d/m/Y",
                                                                            strtotime($orderDetails->seller_delivery_date))}} @else{!!
                                                                            '-' !!} @endif</span>
                                                            </div>
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Acknowledge by</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignee_name)
                                                                            {!! $orderDetails->buyer_consignee_name !!} @else{!! '-' !!}
                                                                            @endif</span>
                                                            </div>

                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Mobile</span> <span class="data-value">@if($orderDetails->buyer_consignee_mobile)
                                                                            {!! $orderDetails->buyer_consignee_mobile !!} @else{!! '-'
                                                                            !!} @endif</span>
                                                            </div>
                                                    </div>


                                                    <div class="col-md-12 padding-left-none data-fld">
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Consignment Value</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignment_value)
                                    {!! number_format($orderDetails->buyer_consignment_value,2) !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Source address</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignor_address)
                                    {!! $orderDetails->buyer_consignor_address !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>

                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Pin code</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignor_pincode)
                                    {!! $orderDetails->buyer_consignor_pincode !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>
                                                    </div>






                                                    <div class="col-md-12 padding-left-none data-fld">
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Consignee / Destination address</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignee_address)
                                    {!! $orderDetails->buyer_consignee_address !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Pincode</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignee_pincode)
                                    {!! $orderDetails->buyer_consignee_pincode !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>

                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                    <span class="data-head">Consignee Mobile</span> <span
                                                                            class="data-value">@if($orderDetails->buyer_consignee_mobile)
                                    {!! $orderDetails->buyer_consignee_mobile !!}
                                    @else{!! '-' !!}
                                    @endif</span>
                                                            </div>
                                                    </div>







                                            </div>
                                            <div class="clearfix"></div>
                                    </div>
                            </div>

                            @if(isset($vehicles) && count($vehicles) > 0)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">Vehicle Placement</div>
                                <div class="detail-data">
                                <div class="col-md-12 margin-top margin-bottom">
                                    <div class="col-md-12 padding-left-none data-fld">
                                        @foreach($vehicles as $vehicle)
                                        
                                            <div class="col-md-12 padding-left-none data-fld">
                                                <div class="col-md-2"><span class="data-head">Vehicle No</span> 
                                                    <span class="data-value">{{$vehicle->vehicle_no}}</span>
                                                </div>
                                                <div class="col-md-2"><span class="data-head">Driver Name</span> 
                                                    <span class="data-value">{{$vehicle->driver_name}}</span>
                                                </div>
                                                <div class="col-md-2"><span class="data-head">Driver Mobile</span> 
                                                    <span class="data-value">{{$vehicle->mobile}}</span>
                                                </div>
                                                <div class="col-md-4 padding-none text-right">
                                                    @include('partials._tracking')                        
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!--div class="col-md-12 padding-left-none data-fld">
                                    <div id="gmap" class="gmap" style="width: 800px; height: 800px; position: relative;">
                                    </div>
                                </div-->
                                <div class="clearfix"></div>
                                </div>
                            </div>
                            @endif
                            
                            
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">Invoice
                                        Trails</div>
                                <div class="detail-data">
                                <div class="col-md-12 margin-top margin-bottom">
                                <div class="col-md-12 padding-left-none data-fld">
                                                        <div class="col-md-4 padding-left-none data-fld">
                                                                <span class="data-head">Invoice No.</span> <span
                                                                        class="data-value">@if($orderDetails->invoice)
                                {!! $orderDetails->invoice !!}
                                @else{!! '-' !!}
                                @endif</span>
                                                        </div>
                                                        
                                @if(SHOW_SERVICE_TAX)
                                                                    <div class="col-md-4 padding-left-none data-fld">
                                                                            <span class="data-head">Service Tax</span> <span
                                                                                    class="data-value">@if($orderDetails->inv_service_tax)
                                            {!! number_format($orderDetails->inv_service_tax,2) !!}
                                            @else{!! '-' !!}
                                            @endif</span>
                                                                    </div>
                                @endif
                                                </div>


                                                        <div class="col-md-12 padding-left-none data-fld">
                                                        <div class="col-md-4 padding-left-none data-fld">
                                                                <span class="data-head">Total Amount</span> <span
                                                                        class="data-value">@if($orderDetails->inv_total)
                                {!! number_format($orderDetails->inv_total,2) !!}
                                @else{!! '-' !!}
                                @endif
                                                                
                                    <br>
                                    @if(!SHOW_SERVICE_TAX)
                                    <span class="small serviceTax">(* Service Tax not included )</span>
                                    @endif
                                                                
                                                                </span>
                                                        </div>
                                                        
                                                            
                                                        </div>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                            <!--blocks added from seller-->
                            

                            <div class="accordian-blocks">
	                            <div class="inner-block-bg inner-block-bg1 detail-head">Documents</div>
	                            <div class="detail-data table-slide-document">
	                                <div class="col-md-12 margin-top margin-bottom">
	                                    @include('partials.documents_partials')
	                                </div>
	                                
	                                <div class="clearfix"></div>
	                            </div>
                            </div>
							
							@if(Session::get('service_id') != RELOCATION_DOMESTIC && (Session::get('service_id') != RELOCATION_OFFICE_MOVE))
                            <div class="accordian-blocks">
                            	<div class="inner-block-bg inner-block-bg1 detail-head">Price Trails</div>
                            	<div class="detail-data">
                            		<div class="col-md-12 margin-top margin-bottom">
                                    <div class="table-heading inner-block-bg">
                                        <div class="col-md-12 padding-none">
                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Quote</div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Price</div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Date</div>
                                        </div>
                                    </div>
                                    <div class="table-data" id="pick_vehicles">
	                                    <div class="table-row inner-block-bg">
	                                        @if($priceDetails->initial_quote_price && $priceDetails->initial_quote_price!=0)
	                                        <div class="col-md-12 padding-none">
	                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Initial Quote</div>
	                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->initial_quote_price}}</div>
	                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{date("d/m/Y", strtotime($priceDetails->initial_quote_created_at))}}</div>
	                                        </div>
	                                        @endif
	                                       </div>
	                                       
	                                        @if($orderDetails->lkp_order_type_id==1)
		                                        @if($priceDetails->counter_quote_price && $priceDetails->counter_quote_price!=0)
		                                        <div class="table-row inner-block-bg">
		                                        <div class="col-md-12 padding-none">
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Counter Quote</div>
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->counter_quote_price}}</div>
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{date("d/m/Y", strtotime($priceDetails->counter_quote_created_at))}}</div>
		                                        </div>
		                                        </div>
		                                        @endif
		                                        @if($priceDetails->final_quote_price && $priceDetails->final_quote_price!=0)
		                                        <div class="table-row inner-block-bg">
		                                        <div class="col-md-12 padding-none">
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Final Quote</div>
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->final_quote_price}}</div>
		                                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{date("d/m/Y", strtotime($priceDetails->final_quote_created_at))}}</div>
		                                        </div>
		                                        </div>
		                                        @endif
	                                        @endif
	                                        
	                                    </div>
	                                    
                                    </div>
                                    <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                             	</div>
                              @endif

                            <div class="accordian-blocks">
                            	<div class="inner-block-bg inner-block-bg1 detail-head">Approval</div>
                            	<div class="detail-data">
                            		<div class="col-md-12 margin-top margin-bottom">
                            		Data Not Found 
                            		</div>
                            		<div class="clearfix"></div>
                            	</div>
                            </div>


                           

                        </div>
                        <!-- Right Section Ends Here -->

                    </div>
                </div>

                <div class="clearfix"></div> 
          
    </div> <!-- Container -->
</div> <!-- Main -->



<!-----model pop up for FTl docuemnts dispaly ----------->

<div class="modal fade" id="ftl-doc-popup" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-body">
        <div class="col-md-12 padding-none">

                @if($docCount>0)                                     
                <div class="col-md-12 padding-none">
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
  </div>
</div>
</div>



@include('partials.footer')
@endsection
