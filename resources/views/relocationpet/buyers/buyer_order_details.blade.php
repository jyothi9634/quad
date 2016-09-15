@inject('messagesComponent', 'App\Components\MessagesComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $countMessages = 0 /*--}}
{{--*/ $countMessages = $messagesComponent->getPerticularMessageDetailsCount(null,$orderDetails->orderid) /*--}}
    
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
                                         @endif to 
                                        @if($orderDetails->to_city)
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
                                        </span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date</span>
                                        <span class="data-value">
                                            @if($orderDetails->delivery_date!='' && $orderDetails->delivery_date!='0000-00-00')
                                            {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}
                                            @else NA
                                            @endif
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
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Pet Type</span>
                                        <span class="data-value">
                                            @if($orderDetails->pet_type)
                                            {!! $orderDetails->pet_type !!}
                                            @else NA
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Breed</span>
                                        <span class="data-value">
                                            @if($orderDetails->breed_type)
                                            {!! $orderDetails->breed_type !!}
                                            @else NA
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Cage Type</span>
                                        <span class="data-result">
                                            @if($orderDetails->cage_type)
                                            {!! $orderDetails->cage_type !!}
                                            @else NA
                                            @endif
                                        </span>
                                    </div>                                    
                                    
                                    <div class="clearfix"></div>
                                    
                                    
                                    <div class="col-md-4 padding-left-none data-fld">                                        
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
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Payment Type</span>
                                        <span class="data-value">                                            
                                            {{--*/ $paymentMode = $orderDetails->payment_mode /*--}}    
                                            @if ($paymentMode == 'Advance')                       
                                           <i class="fa fa-credit-card"></i> Online Payment
                                            @else
                                            <i class="fa fa-rupee"></i> {!! $orderDetails->payment_mode !!}
                                            @endif
                                        </span>
                                    </div>
                                   
                                    
                                    <div class="col-md-10 padding-none pull-left">
                                        <div class="info-links">
                                            <a href="{{ url('/getmessagedetails/0/'.$orderDetails->orderid.'/0')}}" class="tabs-showdiv" ><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
                                            <a href="#"><i class="fa fa-file-text-o"></i> Status<span class="badge">0</span></a>
                                            <a href="#"><i class="fa fa-file-text-o"></i> Documents<span class="badge">0</span></a>
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
                                                                    <span class="data-head">AWB Number</span> <span
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
                                                                    <span class="data-head">Recipient Name</span> <span
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

                            @if(count($vehicles) > 0)
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">Vehicle Placement</div>
                                <div class="detail-data">
                                <div class="col-md-12 margin-top margin-bottom">
                                    <div class="col-md-6 padding-left-none data-fld">
                                        @foreach($vehicles as $vehicle)
                                        
                                            <div class="col-md-12 padding-left-none data-fld">
                                                <div class="col-md-3"><span class="data-head">Vehicle No</span> 
                                                    <span class="data-value">{{$vehicle->vehicle_no}}</span>
                                                </div>
                                                <div class="col-md-3"><span class="data-head">Driver Name</span> 
                                                    <span class="data-value">{{$vehicle->driver_name}}</span>
                                                </div>
                                                <div class="col-md-3"><span class="data-head">Driver Mobile</span> 
                                                    <span class="data-value">{{$vehicle->mobile}}</span>
                                                </div>
                                                <div class="col-md-3 padding-none text-right">
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
                                @endif</span>
                                                        </div></div>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                            <!--blocks added from seller-->
                            

                            <div class="accordian-blocks">
	                            <div class="inner-block-bg inner-block-bg1 detail-head">Documents</div>
	                            <div class="detail-data table-slide-document">
	                                <div class="col-md-12 margin-top margin-bottom">
	                                    <p class="underline-new"><b>Non Commercial Consignment</b></p>
	                                    Declaration on Nature of Goods and purpose
	                                </div>
	                                <div class="col-md-12 col-sm-12 col-xs-12 padding-top text-left margin-bottom">
	                                    <p class="underline-new"><b>Commercial Consignment</b></p>
	                                    Billing Document <br>
	                                    Stock Transfer Document <br>
	                                    VAT Forms <br>
	                                    Other Statutory Forms
	                                </div>
	                                <div class="clearfix"></div>
	                            </div>
                            </div>
							

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
@include('partials.footer')
@endsection
