@extends('app') @section('content')

@include('partials.page_top_navigation')
<div class="clearfix"></div>

		<div class="main">
			<div class="container">
			
				<span class="pull-left"><h1 class="page-title">Intracity Order - {!! $orderDetails->order_no !!}</h1></span>
<a onclick="return checkSession(3,'/intracity/buyer_post');" href="#"> <button class="btn post-btn pull-right">Post & get Quote</button></a>				

				<div class="clearfix"></div>
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

                                                <div class="inner-block-bg inner-block-bg1">


                                                        <div class="col-md-12 tab-modal-head">
                                                                <h3>
                                                                        <i class="fa fa-map-marker"></i> {!! $orderDetails->from_city !!} to {!! $orderDetails->to_city !!}
                                                                </h3>
                                                        </div>
                                                        <div class="col-md-8 data-div">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">City</span>
                                                                        <span class="data-value">{!! $orderDetails->city !!}</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Load Type</span>
                                                                        <span class="data-value">{!! $orderDetails->load_type !!}</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Vehicle Type</span>
                                                                        <span class="data-value">{!! $orderDetails->vehicle_type !!}</span>
                                                                </div>

                                                                <div class="clearfix"></div>


<!--                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Payment</span>
                                                                        <span class="data-value"> {!! $orderDetails->vehicle_type !!}</span>
                                                                </div>-->

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Pick-up Date & Time</span>
                                                                        <span class="data-value">
                                                                        {{date("d/m/Y",strtotime($orderDetails->buyer_consignment_pick_up_date))}} - {{date("H:i A",
        strtotime($orderDetails->buyer_consignment_pick_up_date))}}</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Vehicle No.</span>
                                                                        <span class="data-value">{!! $orderDetails->vehicle !!}</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Driver Mobile No.</span>
                                                                        <span class="data-value">{!! $orderDetails->driver_number !!}</span>
                                                                </div>    
                                                                <div class="clearfix"></div>
                                                                
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Intracity post No.</span>
                                                                        <span class="data-value">{!! $orderDetails->trans_id !!}</span>
                                                                </div>



                                                        </div>
                                                        <div class="col-md-4 order-detail-price-block">

                                                                <div>
                                                                        <span class="data-head">Total Price</span>
                                                                        <span class="data-value big-value">{!! $orderDetails->orderprice !!} /-</span>
                                                                </div>

                                                                <div class="col-md-5 padding-none">


                                                                        <span class="data-head">Status</span>
                                                                        <div class="status-bar">
                                                        <div class="status-bar"></div>
                                                        <span class="status-text">{!! $orderDetails->order_status !!}</span>
                                                </div>
                                                                </div>
                                                        </div>
							</div>
                                                        <?php /*@if (strtotime($cancel_book_date)>(strtotime(date ( 'Y-m-d H:i:s' ))+7200 ) && ($orderDetails->order_status!='Cancelled' && $orderDetails->order_status!='Delivered' && $orderDetails->order_status!='In transit' && $orderDetails->order_status!='Reached destination' ))*/ ?>
                                                        @if ($orderDetails->order_status!='Cancelled' && $orderDetails->order_status!='Delivered' && $orderDetails->order_status!='In transit' && $orderDetails->order_status!='Reached destination')
                                                        <div class="col-md-12 col-sm-12 col-xs-12 text-right padding-right-none margin-bottom">
                                                                <a class="btn post-btn pull-right" data-target="#cancelordermodal" data-toggle="modal" onclick="setorderid({{$orderDetails->orderid}})">
                                                                    <span>Cancel Booking</span></a>
                                                        </div>
                                                        @endif
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
								<div class="inner-block-bg inner-block-bg1 detail-head">Documents</div>
								<div class="detail-data">
									<div class="col-md-12 margin-top margin-bottom">Data Not Found</div>
									<div class="clearfix"></div>
								</div>
							</div>

							<div class="accordian-blocks">
								<div class="inner-block-bg inner-block-bg1 detail-head">Price Trails</div>
								<div class="detail-data">
									<div class="col-md-12 margin-top margin-bottom">Data Not Found</div>
									<div class="clearfix"></div>
								</div>
							</div>
	
							<div class="accordian-blocks">
								<div class="inner-block-bg inner-block-bg1 detail-head">Approval</div>
								<div class="detail-data">
									<div class="col-md-12 margin-top margin-bottom">Data Not Found</div>
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
								<span class="add-on"><i class="fa fa-user"></i></span>
								<select class="selectpicker">
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
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<input class="form-control" id="" type="text" placeholder="From Location">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<input class="form-control" id="" type="text" placeholder="To Location">
							</div>
						</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									<input class="form-control" id="" type="text" placeholder="Dispatch Date">
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									<input class="form-control" id="" type="text" placeholder="Delivery Date">
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									<select class="selectpicker">
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
										<input class="form-control" id="" type="text" placeholder="Capacity">
									</div>
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									<select class="selectpicker">
										<option value="0">Select Vehicle Type</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<img src="../images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions *</span>
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
@endsection