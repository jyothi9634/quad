@extends('app')

@section('content')

@include('partials.page_top_navigation')
<div class="main">

			<div class="container">
				@if (Session::has('message') && Session::get('message')!='')
	                        <div class="flash">
	                            <p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('message') }}</p>
	                        </div>
                    	 @endif  
                    	 <span class="pull-left"><h1 class="page-title">Vehicle Registration List</h1> </span>
				<span class="pull-right">
					<a  href="{{url('/vehicleregister')}}" class="back-link1">Add Vehicle</a>
					 
				</span>
 

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

						 

							<!-- Table Starts Here -->

							<div class="table-div">
								
								<!-- Table Head Starts Here -->

								<div class="table-heading inner-block-bg">
									<div class="col-md-2 padding-left-none">States</div>
                                                                        <div class="col-md-2 padding-left-none">Location</div>
									<div class="col-md-2 padding-left-none">Vehicle Number</div>
									<div class="col-md-2 padding-left-none">Truck Type</div>
                                                                        <div class="col-md-2 padding-left-none">Capacity</div>
									<div class="col-md-1 padding-left-none">Status </div> 
									<div class="col-md-1 padding-left-none">Actions </div>  
								</div>

								<!-- Table Head Ends Here -->

								<div class="table-data">
									
									<!-- Table Row Starts Here -->
                                                                        {{--*/ $i=0 /*--}}
                                                                        @foreach ($vehicle as $v)    {{--*/ $i++ /*--}}                                  
									<div class="table-row inner-block-bg">
										<div class="col-md-2 padding-left-none"></div>
                                                                                <div class="col-md-2 padding-left-none"></div>
										<div class="col-md-2 padding-left-none">{{$v->vehicle_number}}</div>
										<div class="col-md-2 padding-left-none">{{$v->vehicle_type}}</div>
										<div class="col-md-2 padding-left-none">{{$v-> vehicle_capacity}}</div>
                                                                                 <div class="col-md-1 padding-left-none">{{$v->status}}</div>
										<div class="col-md-1 padding-left-none">
										<a href="{{url('vehicle_edit/'.$v->id)}}" class=""><i class="fa fa-edit red" title="Edit"></i></a>
										 <a href="{{url('vehicle_destroy/'.$v->id)}}" class="" onclick="return confirm('Are you sure, you want to delete?')"><i class="fa fa-trash-o red" title="Delete"></i></a>
										 </div>
										  
										<div class="clearfix"></div>
 

									</div>

	@endforeach					
								</div>
							</div>	

							<!-- Table Starts Here -->

						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
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
@endsection