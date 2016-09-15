@extends('app') @section('content')
<div class="container container-inner">
	<!-- Left Nav Starts Here -->
	@include('partials.seller_leftnav')
	<!-- Left Nav Ends Here -->
	<!-- Page Center Content Starts Here -->

	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

		<div class="block">
			<div class="tab-nav underline">
				@include('partials.page_top_navigation')
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12 padding-top">

				<h5>
					<div class="col-md-3 col-sm-3 col-xs-6 padding-none">
						<b>Full Truck Order No</b>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6 padding-none">
						<b>{!! $orderDetails->order_no !!}</b>
					</div>
					<div class="clearfix"></div>

				</h5>
				<div class="col-md-3 col-sm-3 col-xs-6 padding-none">

					<p>Full Truck Post No</p>
					<p>Buyer</p>
					<p>Vehicle Type</p>
					<p>Product Type</p>
					<p>From Location</p>
					<p>To Location</p>
					<p>Date</p>
					<p>Consignee</p>
					<p>Status</p>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6 padding-none">
					<p>{!! $orderDetails->trans_id !!}</p>
					<p>{!! $orderDetails->username !!}</p>
					<p>{!! $orderDetails->vehicle_type !!}</p>
					<p>{!! $orderDetails->load_type !!}</p>
					<p>{!! $orderDetails->from_city!!}</p>
					<p>{!! $orderDetails->to_city !!}</p>
					<p>{{date("d/m/Y", strtotime($orderDetails->dispatch_date))}} - {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}</p>
					<p>{!! $orderDetails->buyer_consignee_name !!}</p>
					<p>{!! $orderDetails->order_status !!}</p>
				</div>

				<div class="clearfix"></div>

			</div>
			<div class="block">
				<div
					class="col-md-8 col-sm-8 col-xs-12 padding-left-none padding-right-none">
					<div
						class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
						<a href="#">
							<div class="margin-center">
								<i class="fa fa-envelope"></i> <span
									class="red superscript-table">9</span>
							</div> Messages
						</a>
					</div>
					<div
						class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
						<a href="#">
							<div class="margin-center">
								<i class="fa fa-file-text-o"></i> <span
									class="red superscript-table">9</span>
							</div> Status
						</a>
					</div>
					<div
						class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
						<a href="#">
							<div class="margin-center">
								<i class="fa fa-file-text-o"></i> <span
									class="red superscript-table">0</span>
							</div> Documents
						</a>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>

			<div width="100%" class="table table-data  border-bottom border-top">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left">
					<span class="detailsslide-1">Order No. {!!$orderDetails->order_no !!}</span>
				</div>
				<div class="clearfix"></div>
				<div
					class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide table-slide-1">
					<div class="col-md-4 col-sm-4 col-xs-6 padding-left-none">

						<p>
							Vehicle No.<span class="pull-right">:</span>
						</p>
						<p>
							Driver Name<span class="pull-right">:</span>
						</p>
						<p>
							Mobile<span class="pull-right">:</span>
						</p>
						<p>
							Pickup Date<span class="pull-right">:</span>
						</p>
						<p>
							LR Number<span class="pull-right">:</span>
						</p>
						<p>
							Transporter bill no.<span class="pull-right">:</span>
						</p>
						<p>
							Delivery on<span class="pull-right">:</span>
						</p>
						<p>
							Acknowledge by<span class="pull-right">:</span>
						</p>
						<p>
							Mobile<span class="pull-right">:</span>
						</p>

					</div>
					<div class="colmd-4 col-sm-4 col-xs-6 padding-none">

						<p>@if($orderDetails->vehicle_no)
						{!! $orderDetails->vehicle_no !!}
						@else{!!'-' !!}
						@endif</p>
						<p>@if($orderDetails->driver_name)
						{!! $orderDetails->driver_name !!}
						@else{!! '-' !!}
						@endif</p>
						
						<p>@if($orderDetails->mobile)
						{!! $orderDetails->mobile !!}
						@else{!!'-' !!}
						@endif</p>
						
						<p>@if($orderDetails->seller_pickup_lr_date)
						{{date("d/m/Y", strtotime($orderDetails->seller_pickup_lr_date))}} - {{date("h.i a", strtotime($orderDetails->seller_pickup_lr_date))}}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->seller_pickup_lr_number)
						{!! $orderDetails->seller_pickup_lr_number !!}
						@else{!! '-' !!}
						@endif</p>
						
						<p>@if($orderDetails->seller_pickup_transport_bill_no)
						{!! $orderDetails->seller_pickup_transport_bill_no !!}
						@else{!! '-' !!}
						@endif</p>
						
						<p>@if($orderDetails->seller_delivery_date)
						{{$orderDetails->seller_delivery_date}}
						@else{!! '-' !!}
						@endif</p>
						
						<p>@if($orderDetails->buyer_consignee_name)
						{!! $orderDetails->buyer_consignee_name !!}
						@else{!! '-' !!}
						@endif</p>
						
						
						<p>@if($orderDetails->buyer_consignee_mobile)
						{!! $orderDetails->buyer_consignee_mobile !!}
						@else{!! '-' !!}
						@endif</p>
						
						
					</div>
					<div class="clearfix"></div>
					<div
						class="colmd-12 col-sm-12 col-xs-12 padding-none border-bottom"></div>
					<div class="col-md-4 col-sm-4 col-xs-6 padding-left-none">

						<p>
							Consignment Value<span class="pull-right">:</span>
						</p>
						<p>
							Source address<span class="pull-right">:</span>
						</p>
						<p>
							Pin code<span class="pull-right">:</span>
						</p>
						<p>
							Destination address<span class="pull-right">:</span>
						</p>
						<p>
							Pin code<span class="pull-right">:</span>
						</p>
						<p>
							Mobile<span class="pull-right">:</span>
						</p>


					</div>
					<div class="colmd-8 col-sm-8 col-xs-6 padding-none">
					<p>@if($orderDetails->buyer_consignment_value)
						{!! number_format($orderDetails->buyer_consignment_value,2) !!}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->buyer_consignor_address)
						{!! $orderDetails->buyer_consignor_address !!}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->buyer_consignor_pincode)
						{!! $orderDetails->buyer_consignor_pincode !!}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->buyer_consignee_address)
						{!! $orderDetails->buyer_consignee_address !!}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->buyer_consignee_pincode)
						{!! $orderDetails->buyer_consignee_pincode !!}
						@else{!! '-' !!}
						@endif</p>
						<p>@if($orderDetails->buyer_consignee_mobile)
						{!! $orderDetails->buyer_consignee_mobile !!}
						@else{!! '-' !!}
						@endif</p>
						

					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div width="100%" class="table table-data  border-bottom">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left">
					<span class="detailsslide-2">Invoice</span>
				</div>
				<div class="clearfix"></div>
				<div
					class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide table-slide-2">
					<div class="col-md-4 col-sm-4 col-xs-6 padding-left-none">

						<p>
							Invoice No.<span class="pull-right">:</span>
						</p>
                                                @if(SHOW_SERVICE_TAX)
						<p>
							Services Charges<span class="pull-right">:</span>
						</p>
                                                
                                                    <p>
                                                            Service Tax<span class="pull-right">:</span>
                                                    </p>
                                                @endif
						<p>
							Total Amount<span class="pull-right">:</span>
						</p>

					</div>
					<div class="colmd-4 col-sm-4 col-xs-6 padding-none">

						<p>@if($orderDetails->invoice)
						{!! $orderDetails->invoice !!}
						@else{!! '-' !!}
						@endif</p>
                                                @if(SHOW_SERVICE_TAX)
						<p>@if($orderDetails->inv_service_charge)
						{!! number_format($orderDetails->inv_service_charge,2)!!}
						@else{!! '-' !!}
						@endif</p>
                                                
                                                
                                                    <p>@if($orderDetails->inv_service_tax)
                                                    {!! number_format($orderDetails->inv_service_tax,2) !!}
                                                    @else{!! '-' !!}
                                                    @endif</p>
                                                @endif
                                                
						<p>@if($orderDetails->inv_total)
						{!! number_format($orderDetails->inv_total,2) !!}
						@else{!! '-' !!}
						@endif
                                                
                                                 <br>
                                                @if(!SHOW_SERVICE_TAX)
                                                <span class="small serviceTax">(* Service Tax not included )</span>
                                                @endif
                                                
                                                </p>
                                                
                                                
						
						
					</div>
					<div class="clearfix"></div>

				</div>
				<div class="clearfix"></div>
			</div>
			<div width="100%" class="table table-data  border-bottom">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left">
					<span class="detailsslide-3">Receipt</span>
				</div>
				<div class="clearfix"></div>
				<div
					class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide table-slide-3">
					<div class="col-md-4 col-sm-4 col-xs-6 padding-left-none">

						<p>
							Receipt No<span class="pull-right">:</span>
						</p>
						<p>
							Payment Mode<span class="pull-right">:</span>
						</p>
						<p>
							Freight Amount<span class="pull-right">:</span>
						</p>
						<p>
							Insurance<span class="pull-right">:</span>
						</p>
                                                <p>
							Services Charges<span class="pull-right">:</span>
						</p>
                                                
                                                @if(SHOW_SERVICE_TAX)
                                                <p>
							Service Tax<span class="pull-right">:</span>
						</p>
                                                @endif
						<p>
							Total Amount<span class="pull-right">:</span>
						</p>

					</div>
					<div class="colmd-4 col-sm-4 col-xs-6 padding-none">
					
					<p>@if($orderDetails->receipt)
						{!! $orderDetails->receipt !!}
						@else{!! '-' !!}
						@endif</p>
						
						
						<p>@if($orderDetails->payment_mode)
						{!! $orderDetails->payment_mode !!}
						@else{!! '-' !!}
						@endif</p>
						
						
						<p>@if($orderDetails->receipt_frieght)
						{!! number_format($orderDetails->receipt_frieght,2) !!}
						@else{!! '-' !!}
						@endif</p>
						
						
						<p>@if($orderDetails->receipt_insurance)
						{!! number_format($orderDetails->receipt_insurance,2) !!}
						@else{!! '-' !!}
						@endif</p>
						
						
						<p>@if($orderDetails->receipt_service_charge)
						{!!  number_format($orderDetails->receipt_service_charge,2) !!}
						@else{!! '-' !!}
						@endif</p>
                                                
                                                @if(SHOW_SERVICE_TAX)
                                                    <p>@if($orderDetails->receipt_service_tax)
                                                    {!! number_format($orderDetails->receipt_service_tax,2) !!}
                                                    @else{!! '-' !!}
                                                    @endif</p>
                                                @endif
						<p>@if($orderDetails->receipt_total)
						{!! number_format($orderDetails->receipt_total,2) !!}
						@else{!! '-' !!}
						@endif</p>

					</div>
					<div class="clearfix"></div>

				</div>
				<div class="clearfix"></div>
			</div>
			<?php /*<div width="100%" class="table table-data  border-bottom">
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left">
					<span class="detailsslide detailsslide-4">Receipt Cancellation no.
						if applicable</span>
				</div>
				<div class="clearfix"></div>
				<div
					class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide table-slide-4">
					<div class="col-md-4 col-sm-4 col-xs-12 padding-left-none">

						<p>
							Cancellation date<span class="pull-right">:</span>
						</p>
						<p>
							Total Amount<span class="pull-right">:</span>
						</p>
						<p>
							Cancellation Amount<span class="pull-right">:</span>
						</p>
						<p>
							Balance Refund<span class="pull-right">:</span>
						</p>
						<p>
							Cancellation Payment Mode<span class="pull-right">:</span>
						</p>
						<p>
							Bank Transaction ID<span class="pull-right">:</span>
						</p>

					</div>
					<div class="colmd-8 col-sm-8 col-xs-12 padding-none">

						<p>dd/mm/yyyy</p>
							<p>{!! $orderDetails->cancellation_amt !!}</p>
						<p>{!! $orderDetails->total_amt !!}</p>
						<p>{!! $orderDetails->balance_refund !!}</p>
						<p>{!! $orderDetails->cancellation_payment_mode !!}</p>
						<p>{!! $orderDetails->bank_trans_id !!}</p>
					</div>
					<div class="clearfix"></div>

				</div>
				<div class="clearfix"></div>
			</div>*/ ?>



		</div>
	</div>
	<!-- Page Center Content Ends Here -->
	<!-- Right Starts Here -->
	@include('partials.seller_rightnav')
	<!-- Right Ends Here -->

</div>
@endsection
