@inject('messagesComponent', 'App\Components\MessagesComponent')
@inject('common', 'App\Components\CommonComponent')
@extends('app') @section('content')
@include('partials.page_top_navigation')
{{--*/ $countMessages = 0 /*--}}
{{--*/ $countMessages = $messagesComponent->getPerticularMessageDetailsCount(null,$orderDetails->orderid) /*--}}

{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_seller    =   $common->getGsaDocuments(3,$serviceId,0); /*--}}      
{{--*/ $docCount = count($docs_seller) /*--}}
   
<div class="main">

    {{--*/ $serviceId = Session::get('service_id') /*--}}
    @if($serviceId == ROAD_PTL)
    {{--*/ $str="Less than Truck" /*--}}
    @elseif($serviceId == RAIL)
    {{--*/ $str="Rail" /*--}}
    @elseif($serviceId == AIR_DOMESTIC)
    {{--*/ $str="Air Domestic" /*--}}
    @elseif($serviceId == AIR_INTERNATIONAL)
    {{--*/ $str="Air International" /*--}}
    @elseif($serviceId == OCEAN)
    {{--*/ $str="Ocean" /*--}}
    @elseif($serviceId == COURIER)
    {{--*/ $str="Courier" /*--}}

    @endif
    
        {{--*/ $str_perkg='' /*--}} 
        @if($serviceId==ROAD_PTL || $serviceId==RAIL)
        {{--*/ $str_perkg=' CFT' /*--}}
        @elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
        {{--*/ $str_perkg=' CCM' /*--}}
        @elseif($serviceId==OCEAN)
        {{--*/ $str_perkg=' CBM' /*--}}
        @endif
	<div class="container">
		<span class="pull-left"><h1 class="page-title">Order - {!! $orderDetails->order_no !!}</h1></span>

		@include('partials.content_top_navigation_links')

		<div class="clearfix"></div>
		
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				
				<div class="main-right">

					<div class="inner-block-bg inner-block-bg1">
						<div class="col-md-12 tab-modal-head">
							<h3>
								<i class="fa fa-map-marker"></i> {!! $orderDetails->from_city!!} to {!! $orderDetails->to_city !!}
							</h3>
						</div>
						<div class="col-md-8 data-div">
						@if($orderDetails->trans_id)
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">{!! $str !!} Post No</span>
								<span class="data-value">{!! $orderDetails->trans_id !!}</span>
							</div>
						@endif
						@if($orderDetails->username)	
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Buyer Name</span>
								<span class="data-value">{!! $orderDetails->username !!}</span>
							</div>
						@endif
						@if($orderDetails->buyer_consignment_pick_up_date)	
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Dispatch Date</span>
								<span class="data-value">{!! date("d/m/Y", strtotime($orderDetails->buyer_consignment_pick_up_date)) !!}</span>
							</div>
						@endif	
						
					
							<div class="clearfix"></div>

						@if($orderDetails->orderdelivery)	
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Delivery Date</span>
								<span class="data-value">{!! date("d/m/Y", strtotime($orderDetails->orderdelivery)) !!}</span>
							</div>
						@endif	


						@if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
							@if(isset($orderDetails->shipment_type))
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Shipment Type</span>
									<span class="data-value">{!! $orderDetails->shipment_type!!}</span>
								</div>
							@endif
							@if(isset($orderDetails->sender_identity))
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Sender Identity</span>
									<span class="data-value">{!! $orderDetails->sender_identity!!}</span>
								</div>
							@endif	
							
							@if(isset($orderDetails->ie_code))
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">IE Code</span>
									<span class="data-value">{!! $orderDetails->ie_code!!}</span>
								</div>
							@endif

							@if(isset($orderDetails->product_made))
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Product Made</span>
									<span class="data-value">{!! $orderDetails->product_made!!}</span>
								</div>
							@endif

						@endif
                        @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC || $serviceId == OCEAN)
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Fragile</span>
								<span class="data-value">
									@if($orderDetails->buyer_consignment_needs_fragile == 0)
	                                    NO
	                                @else
	                                    Yes
                                    @endif
								</span>
							</div>
                        @endif


                        @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)

							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Door Pickup</span>
								<span class="data-value">
								@if($orderDetails_buyer_pickups_veiw[0]->is_door_pickup == 0)									
									NO
                       			@else 
                       				Yes
                           		@endif
								</span>
							</div>

							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Door Delivery</span>
								<span class="data-value">
								@if($orderDetails_buyer_pickups_veiw[0]->is_door_delivery == 0)
                                                                        NO
                                                                @else 
                                                                        Yes
                                                                @endif									
								</span>
							</div>

						@endif	

							<div class="clearfix"></div>

							@if(isset($payment_mode_seller))
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Payment Terms</span>
									<span class="data-value">{!! $payment_mode_seller !!}</span>
								</div>
							@endif
                             @if(isset($tracking_order))
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Tracking</span>
								<span class="data-value">
								@if($tracking_order == 1)
									Milestone
                    			@else 
                    				Real Time
                        		@endif									
								</span>
							</div>
                            @endif	
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Documentation</span>
								<span class="data-value">{{$docCount}}</span>
							</div>
							
							@if(isset($orderDetails->quantity) && $orderDetails->quantity!='')
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Quantity</span> <span
									class="data-value">
									@if(isset($orderDetails->quantity) && $orderDetails->quantity!='')
									 {!! $orderDetails->quantity !!} @else  N/A 
									  @endif</span>
							</div>
							@endif	
                                                        
                                                        <!-- packaging details added-->
                                                        <div class="table-div">
                                                            <div class="table-heading inner-block-bg">
                                                                <div class="col-md-2 padding-left-none">S. No</div>
                                                                <div class="col-md-3 padding-left-none">Product Type</div>
                                                                <div class="col-md-3 padding-left-none">Package</div>
                                                                <div class="col-md-2 padding-left-none">Weight</div>
                                                                <div class="col-md-2 padding-left-none">Volume</div>
                                                            </div>

                                                            <div class="table-data">
                                                                @if($post_items){{--*/ $i = 1 /*--}}
                                                                    @foreach($post_items as $post_item)
                                                                        <div class="table-row inner-block-bg">
                                                                            <div class="col-md-2 padding-left-none">{{$i}}</div>
                                                                            <div class="col-md-3 padding-left-none">@if(isset($post_item->packaging)){{$post_item->packaging}}@else - @endif</div>
                                                                            <div class="col-md-3 padding-left-none">@if(isset($post_item->load)){{$post_item->load}}@else - @endif</div>
                                                                            <div class="col-md-2 padding-left-none">{{$post_item->unit}} @if(isset($post_item->weight_type)){{$post_item->weight_type}} @endif</div>
                                                                            <div class="col-md-2 padding-left-none">@if(isset($post_item->cft)){{$post_item->cft}} {{$str_perkg}} @else - @endif</div>
                                                                        </div>
                                                                        {{--*/ $i++ /*--}}
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <!-- packaging details added-->                                    
                                                        
							<div class="col-md-10 padding-none pull-left">
								<div class="info-links">
									<a href="{{ url('/getmessagedetails/0/'.$orderDetails->orderid.'/0')}}"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
									<a href="#"><i class="fa fa-hourglass-1"></i> Status <span class="badge">0</span></a>
									<a href="#" data-toggle="modal" data-target="#ftl-doc-popup"><i class="fa fa-file-text-o"></i> Documents <span class="badge">{{$docCount}}</span></a>
								</div>
							</div>

						</div>
						<div class="col-md-4 order-detail-price-block">
							@if($orderDetails->username)
								<div>
									<span class="data-value">{!! $orderDetails->username !!}</span>
								</div>
							@endif
							
								<div>
									<span class="data-head">Total Price</span>
									<span class="data-value big-value">Rs. @if($orderDetails->inv_price) 
                                                                            {!!  $common->getPriceType($orderDetails->inv_price) !!}
									@else{!! $common->getPriceType($orderDetails->price) !!} @endif</span>
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

					<div class="accordian-blocks">
						<div class="inner-block-bg inner-block-bg1 detail-head">Order No.
							{!!$orderDetails->order_no !!}</div>
						<div class="detail-data" style="display: none;">
							<div class="col-md-12 inner-block-bg padding-top margin-bottom">
								@if(!empty($vehicles))
								<h4 class="data-head">Vehicle Placement</h4>
								<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										@foreach($vehicles as $vehicle) <span class="data-head">Vehicle
											No</span> <span class="data-value">{{$vehicle->vehicle_no}}</span>
										@endforeach
									</div>

									<div class="col-md-4 padding-left-none data-fld">
										@foreach($vehicles as $vehicle) <span class="data-head">Driver
											Name</span> <span class="data-value">{{$vehicle->driver_name}}</span>
										@endforeach
									</div>
									<div class="col-md-4 padding-left-none data-fld">
										@foreach($vehicles as $vehicle) <span class="data-head">Driver
											Mobile</span> <span class="data-value">{{$vehicle->mobile}}</span>
										@endforeach
									</div>
								</div>
								@endif
								<div class="clearfix"></div>
								<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Pickup Date</span> <span
											class="data-value">@if($orderDetails->seller_pickup_lr_date!=
											'0000-00-00 00:00:00') {{date("d/m/Y",
											strtotime($orderDetails->seller_pickup_date))}} @else{!! '-'
											!!} @endif</span>
									</div>
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">
                                                                                    @if($serviceId == AIR_INTERNATIONAL || $serviceId == AIR_DOMESTIC)
                                                                                    AWB Number
                                                                                    @elseif($serviceId == OCEAN )
                                                                                    BL Number
                                                                                    @else
                                                                                    LR Number
                                                                                    @endif
                                                                                </span> <span
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
						</div>
					</div>

					@if(count($vehicles) > 0)
					<div class="accordian-blocks">
						<div class="inner-block-bg inner-block-bg1 detail-head">Vehicle Details</div>
						<div class="detail-data">
						<div class="col-md-12 margin-top margin-bottom">
							<div class="col-md-6 padding-left-none data-fld">
								@foreach($vehicles as $vehicle)
								<?php
									//echo "<pre>";
									//print_r($vehicle);
								?>
									<div class="col-md-12 padding-left-none data-fld">
										<div class="col-md-4"><strong>{{$vehicle->vehicle_no}}</strong></div>
										<div class="col-md-8 padding-none text-right">
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
						<div class="col-md-12 inner-block-bg padding-top margin-bottom">
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
                                                                <span class="data-head">Services Charges</span> <span
                                                                        class="data-value">
                                                                        @if($orderDetails->inv_service_charge)
                                                                {!! number_format($orderDetails->inv_service_charge,2)!!}
                                                                @else{!! '-' !!}
                                                                @endif</span>
                                                        </div>
                                                    
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
                                                                                        {!! $common->getPriceType($orderDetails->inv_total) !!}
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
						
						</div>
					</div>
					<div class="accordian-blocks">
						<div class="inner-block-bg inner-block-bg1 detail-head">Receipt</div>
						<div class="detail-data">
						<div class="col-md-12 inner-block-bg padding-top margin-bottom">
						<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Receipt No</span> <span
											class="data-value">@if($orderDetails->receipt)
						{!! $orderDetails->receipt !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Payment Mode</span> <span
											class="data-value">
											@if($orderDetails->payment_mode)
						{!! $orderDetails->payment_mode !!}
						@else{!! '-' !!}
						@endif</span>
									</div>

									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Freight Amount</span> <span
											class="data-value">@if($orderDetails->receipt_frieght)
						{!! number_format($orderDetails->receipt_frieght,2) !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
								</div>
								
								
									<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Insurance</span> <span
											class="data-value">@if($orderDetails->receipt_insurance)
						{!! number_format($orderDetails->receipt_insurance,2) !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
                                                                            
                                                                            
                                                        @if(SHOW_SERVICE_TAX)
									<div class="col-md-4 padding-left-none data-fld">
                                                                                                                <span class="data-head">Services Charges</span> <span
                                                                                                                        class="data-value">@if($orderDetails->receipt_service_charge)
                                                                                {!!  number_format($orderDetails->receipt_service_charge,2) !!}
                                                                                @else{!! '-' !!}
                                                                                @endif</span>
									</div>
									<div class="col-md-4 padding-left-none data-fld">
                                                                                                                <span class="data-head">Service Tax</span> <span
                                                                                                                        class="data-value">@if($orderDetails->receipt_service_tax)
                                                                                {!! number_format($orderDetails->receipt_service_tax,2) !!}
                                                                                @else{!! '-' !!}
                                                                                @endif</span>
									</div>
                                                        @endif
									</div>
									
									<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Total Amount</span> <span
											class="data-value">@if($orderDetails->receipt_total)
                                                                                        {!! $common->getPriceType($orderDetails->receipt_total) !!}
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
						
						
						
						
						</div>
					</div>

				</div>

			</div>
		</div>

		<div class="clearfix"></div>

	</div>
</div>


<!-----model pop up for FTl docuemnts dispaly ----------->

<div class="modal fade" id="ftl-doc-popup" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-body">
        <div class="col-md-12 padding-none">

                @if($docs_seller>0)                                     
                <div class="col-md-12 padding-none">
                    <h3>List of documents </h3> 
                    <ul class="popup-list">                                               

                        @foreach($docs_seller as $doc)
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
