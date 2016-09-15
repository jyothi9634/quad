@inject('messagesComponent', 'App\Components\MessagesComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $countMessages = 0 /*--}}
{{--*/ $countMessages = $messagesComponent->getPerticularMessageDetailsCount(null,$orderDetails->orderid) /*--}}

{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(3,$serviceId,0); /*--}}      
{{--*/ $docCount = count($docs_seller) /*--}}
   
<link rel="stylesheet" href="{{ asset('/css/volty/style.css') }}" type='text/css' />
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBnRy371oDNcVsh4uSMYfBpA-8BJ5anB5s&libraries=geometry&libraries=places"></script>
<script src="{{ asset('/js/volty/jquery-1.11.0.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/volty/infobox.js') }}"></script>
<script src="{{ asset('/js/volty/veh_history.js') }}"></script>
<script src="{{ asset('/js/volty/onload.js') }}" type="text/javascript"></script>

<div class="main">

	<div class="container">
		<span class="pull-left"><h1 class="page-title">Order - {!!
				$orderDetails->order_no !!}</h1></span>


		<a onclick="return checkSession(1,'/createseller');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			<div class="main-inner">


				<!-- Right Section Starts Here -->

				<div class="main-right">

					<div class="inner-block-bg inner-block-bg1">
						<div class="col-md-12 tab-modal-head">
							<h3>
								<i class="fa fa-map-marker"></i>
								@if(isset($orderDetails->from_city)) {!!
								$orderDetails->from_city !!} @else &nbsp; @endif to
								@if(isset($orderDetails->to_city)) {!! $orderDetails->to_city
								!!} @else &nbsp; @endif
							</h3>
						</div>
						<div class="col-md-8 data-div">
							
							<div class="col-md-4 padding-left-none data-fld">						
							
                                                            <span class="data-head">Load Type</span> <span
									class="data-value">@if(isset($orderDetails->load_type)) {!!
									$orderDetails->load_type !!} @else &nbsp; @endif</span>
                                                           
							</div>
							<div class="col-md-4 padding-left-none data-fld">
							
								<span class="data-head">Vehicle Type</span> <span
                                                                    class="data-value">@if(isset($orderDetails->vehicle_type)) {!!
                                                                    $orderDetails->vehicle_type !!} @else &nbsp; @endif</span>
								
							</div>
                                                    
                                                    <div class="col-md-4 padding-left-none data-fld">
							
								<span class="data-head">Price Type</span> <span
                                                                    class="data-value">
                                                                    @if(isset($orderDetails->lkp_quote_price_type_id))
                                                                        @if($orderDetails->lkp_quote_price_type_id == 1)
                                                                        Competitive Price 
                                                                        @else
                                                                        Firm Price
                                                                        @endif
                                                                    @endif</span>
								
							</div>

							<div class="clearfix"></div>

							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Reporting Date</span> <span
									class="data-value">@if(isset($orderDetails->dispatch_date)
									&& $orderDetails->dispatch_date != 01/01/1970)
									{{date("d/m/Y",	strtotime($orderDetails->dispatch_date))}}
									@else &nbsp; @endif</span>
							</div>
                                                        
							<div class="col-md-10 padding-none pull-left">
								<div class="info-links">
									<a href="{{ url('/getmessagedetails/0/'.$orderDetails->orderid.'/0')}}"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a> <a
										href="#"><i class="fa fa-hourglass-1"></i> Status<span class="badge">0</span></a> <a
										href="#" data-toggle="modal" data-target="#ftl-doc-popup"><i class="fa fa-file-text-o"></i> Documents<span class="badge">{{$docCount}}</span></a>
								</div>
							</div>

						</div>
						<div class="col-md-4 order-detail-price-block">
							<div>
                                                <span class="vendor-name">{{$orderDetails->username}}</span>
							</div>

							<div>
								<span class="data-head">Total Price</span> <span
									class="data-value big-value">Rs. @if($orderDetails->inv_price) {!!
									$commonComponent->getPriceType($orderDetails->inv_price) !!}
									@else{!! $commonComponent->getPriceType($orderDetails->price) !!} @endif </span>
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
                                    
                                    {{--*/ $splitBuyepick = explode(" ",$orderDetails->buyer_consignment_pick_up_date) /*--}}  
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
							
                                    <div class="col-md-6 padding-none">
                                        <span class="data-head">Status</span>
                                    	<div class="status-bar">
                                            <div class="status-bar">												
                                            {!! $str !!}{!! $strdelivery !!}      		
                                            <span class="status-text">
                                                @if($orderDetails->lkp_order_status_id == 2)
                                                {{--*/ $status		=	'Placement Due' /*--}} 
                                                @elseif($orderDetails->lkp_order_status_id == 3)
                                                {{--*/ $status		=	'Placed' /*--}} 
                                                @elseif($orderDetails->lkp_order_status_id == 6)
                                                {{--*/ $status		=	'Reported' /*--}} 
                                                @else
                                                {{--*/ $status		=	'Pending' /*--}} 
                                                @endif
                                                {{ $status }}                                                         
                                            </span>												
                                            </div>    
                                        </div>                                        
                                    </div>		
						
							
						
						</div>
					</div>
				
					<div class="clearfix"></div>

			
				
				
                            
					<div class="col-md-12 inner-block-bg inner-block-bg1">
						<div class="col-md-4 padding-none">
							<div class="center-block pull-left">
								<i class="fa fa-print"></i> <span>Print Order</span>
							</div>
						</div>

						<div class="col-md-4 padding-none">
							<div class="center-block">
								<i class="fa fa-file-text-o"></i> <span>Email Invoice</span>
							</div>
						</div>

						<div class="col-md-4 padding-none">
							<div class="center-block pull-right">
								<i class="fa fa-phone"></i> <span>Contact Us</span>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>

					<div class="accordian-blocks">
						<div class="inner-block-bg inner-block-bg1 detail-head">Order No.
							{!!$orderDetails->order_no !!}</div>
						<div class="detail-data" style="display: none;">
							<div class="col-md-12 margin-top margin-bottom">
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
										<span class="data-head">Reporting Date</span> <span
											class="data-value">
                                                                                    {{date("d/m/Y",strtotime($orderDetails->dispatch_date))}}</span>
									</div>
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Reporting Time</span> 
                                                                                <span class="data-value">
                                                                                    @if($orderDetails->buyer_consignment_pick_up_time_from)                                            
                                                                                    {{ date('G:i A ', strtotime($orderDetails->buyer_consignment_pick_up_time_from)) }} - {{ date('G:i A ', strtotime($orderDetails->buyer_consignment_pick_up_time_to)) }}
                                                                                    @else NA
                                                                                    @endif
                                                                                </span>
									</div>		
                                                                    
                                                                        <div class="col-md-4 padding-left-none data-fld">
                                                                                    <span class="data-head">Report To</span> <span
                                                                                            class="data-value">
                                                                                        {{ $orderDetails->buyer_consignor_name }}</span>
                                                                         </div>
								</div>

							</div>
							<div class="clearfix"></div>
						</div>
					</div>
					@if(isset($vehicles))
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
										<span class="data-head">Services Charges</span> <span
											class="data-value">
											@if($orderDetails->inv_service_charge)
						{!! $commonComponent->getPriceType($orderDetails->inv_service_charge)!!}
						@else{!! '-' !!}
						@endif</span>
									</div>
                                                
                                                                                    <div class="col-md-4 padding-left-none data-fld">
                                                                                            <span class="data-head">Service Tax</span> <span
                                                                                                    class="data-value">@if($orderDetails->inv_service_tax)
                                                            {!! $commonComponent->getPriceType($orderDetails->inv_service_tax) !!}
                                                            @else{!! '-' !!}
                                                            @endif</span>
                                                                                    </div>
                                                @endif
								</div>
								
								
									<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Total Amount</span> <span
											class="data-value">@if($orderDetails->inv_total)
						{!! $commonComponent->getPriceType($orderDetails->inv_total) !!}
						@else{!! '-' !!}
						@endif</span>
									</div></div>
						
						</div>
						<div class="clearfix"></div>
					</div>
                                        </div>
					<div class="accordian-blocks">
						<div class="inner-block-bg inner-block-bg1 detail-head">Receipt</div>
						<div class="detail-data">
						<div class="col-md-12 margin-top margin-bottom">
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
						{!! $commonComponent->getPriceType($orderDetails->receipt_frieght) !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
								</div>
								
								
									<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Insurance</span> <span
											class="data-value">@if($orderDetails->receipt_insurance)
						{!! $commonComponent->getPriceType($orderDetails->receipt_insurance) !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
                                                                            
                                                @if(SHOW_SERVICE_TAX)
                                                                            
                                                                                        <div class="col-md-4 padding-left-none data-fld">
                                                                                                <span class="data-head">Services Charges</span> <span
                                                                                                        class="data-value">@if($orderDetails->receipt_service_charge)
                                                                {!!  $commonComponent->getPriceType($orderDetails->receipt_service_charge) !!}
                                                                @else{!! '-' !!}
                                                                @endif</span>
                                                                                        </div>
                                                                                        <div class="col-md-4 padding-left-none data-fld">
                                                                                                <span class="data-head">Service Tax</span> <span
                                                                                                        class="data-value">@if($orderDetails->receipt_service_tax)
                                                                {!! $commonComponent->getPriceType($orderDetails->receipt_service_tax) !!}
                                                                @else{!! '-' !!}
                                                                @endif</span>
                                                                                        </div>
                                                @endif
									</div>
									
									<div class="col-md-12 padding-left-none data-fld">
									<div class="col-md-4 padding-left-none data-fld">
										<span class="data-head">Total Amount</span> <span
											class="data-value">@if($orderDetails->receipt_total)
						{!! $commonComponent->getPriceType($orderDetails->receipt_total) !!}
						@else{!! '-' !!}
						@endif</span>
									</div>
									</div>
						
						</div>
						<div class="clearfix"></div>
						
						
						
						</div>
					</div>

				</div>

				<!-- Right Section Ends Here -->

			</div>
		</div>

		<div class="clearfix"></div>

	</div>
</div>



<footer>
	<div class="container">
		Logistiks.com &copy; 2016. <a href="#">Privacy Policy</a>
	</div>
</footer>




<!-- Modal -->
<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
				<div class="col-md-12 modal-form">
					<div class="col-md-4 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-user"></i></span> <select
									class="selectpicker">
									<option value="0">Select Service</option>
									<option value="1">Full Truck (FTL)</option>
									<option value="2">Full Truck (LTL)</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-8 padding-none">
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="From Location">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="To Location">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="Dispatch Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="Delivery Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span> <select
									class="selectpicker">
									<option value="0">Select Load Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="col-md-6 form-control-fld padding-left-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text" placeholder="Qty">
								</div>
							</div>
							<div class="col-md-6 form-control-fld padding-right-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text"
										placeholder="Capacity">
								</div>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span> <select
									class="selectpicker">
									<option value="0">Select Vehicle Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<img src="../images/truck.png" class="truck-type" /> <span
								class="truck-type-text">Vehicle Dimensions *</span>
						</div>

						<div class="col-md-6 form-control-fld">
							<button class="btn theme-btn btn-block">Search</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

@include('partials.footer')


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


@endsection
