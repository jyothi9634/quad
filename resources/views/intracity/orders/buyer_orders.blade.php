@extends('app')
@section('content')
@include('partials.page_top_navigation')<div class="clearfix"></div>
<div class="main">
	<div class="container">
	<span class="pull-left">
	<h1 class="page-title">Orders (Intracity)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
				
<a onclick="return checkSession(3,'/intracity/buyer_post');" href="#"> <button class="btn post-btn pull-right">Post & get Quote</button></a>				
					<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

							<div class="gray-bg">
								<div class="col-md-12 padding-none filter">
									  {!! Form::open(array('url' => 'buyerordersearch', 'id' =>'seller-order-search',
                        'class'=>'form-inline')) !!}
									
									<div class="col-md-3 form-control-fld">
											<div class="normal-select">
										{!! Form::select('status_id',array('' => 'Status (All)') + $status,$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}
											
											</div>
										</div>
										
										<div class="col-md-3 form-control-fld pull-right">
										{!! Form::submit(' GO ', array('class'=>'btn add-btn pull-right')) !!}
										</div>
										
										
                            {!! Form :: close() !!}		
										
									</div>

							</div>


							<div class="gray-bg">
								<div class="col-md-12 padding-none filter">
									 {!! $filter->open !!}
									<div class="col-md-3 form-control-fld">
											<div class="normal-select">
											 {!! $filter->field('orders.lkp_ict_vehicle_id') !!}
											</div></div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
											
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
															@if(isset($_GET['start_dispatch_date']))
                            {!! Form::text('start_dispatch_date', $_GET['start_dispatch_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From']) !!}
                            @else
                            {!! Form::text('start_dispatch_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From']) !!}
                            @endif
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												@if(isset($_GET['end_dispatch_date']))
                               {!! Form::text('end_dispatch_date', $_GET['end_dispatch_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To']) !!}
                               @else
                               {!! Form::text('end_dispatch_date','',['id' => 'end_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To']) !!}
                               @endif
                               
											</div>
										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
											
												
												{!! $filter->field('orders.order_no') !!}

												<span class="input-group-addon cursor-hover searchSubmit"> <i class="fa fa-search"></i></span>
											</div>
										</div>

										<div class="col-md-12 form-control-fld displayNone">
											<button class="btn add-btn pull-right">Filter</button>
										</div>
									</div>

							</div>


							<!-- Table Starts Here -->

							<div class="table-div">
								 {!! $grid !!}
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								<!-- Table Head Starts Here -->

								

								<!-- Table Head Ends Here -->

								
							</div>	

							<!-- Table Starts Here -->

						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
			 
			 @include('partials.footer')
			 
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

	</div>	  
</div>

@endsection
