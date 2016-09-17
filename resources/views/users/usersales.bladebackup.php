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
		<span class="pull-left"><h1 class="page-title"></h1>
		<b>Sales</b> </span> <!-- <span class="pull-right"> <a href="{{url('/warehouseregister')}}"
			class="back-link1">Add Warehouse</a>

		</span> -->


		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			<div class="main-inner">


				<!-- Right Section Starts Here -->

				<div class="main-right">



					<!-- Table Starts Here -->

					<div class="table-div">

						<!-- Table Head Starts Here -->

						<div class="table-heading inner-block-bg">
							<div class="col-md-1 padding-left-none">Name</div>
                            <div class="col-md-3 padding-left-none">Services</div>
							<div class="col-md-3 padding-left-none">Date</div>
							<div class="col-md-4 padding-left-none">Bills</div>
							<div class="col-md-1 padding-left-none">Amount</div>

						</div>

						<!-- Table Head Ends Here -->

						<div class="table-data">

							<!-- Table Row Starts Here -->
                                                        
                            @foreach($sales as $value)                            
							<div class="table-row inner-block-bg">
								@if(!empty($value->firstname))
								<div class="col-md-1 padding-left-none">{{$value->firstname}}</div>	
								@else
								<div class="col-md-1 padding-left-none">NA</div>	
								@endif
                                <div class="col-md-3 padding-left-none">{{$value->service_name}}</div>
                                <div class="col-md-3 padding-left-none">{{date("Y/m/d",strtotime($value->created_at))}}</div>
								<div class="col-md-4 padding-left-none">{{$value->price}}</div>
							
								<div class="col-md-1 padding-left-none">{{$value->price}}</div>

								<div class="clearfix"></div>

							</div>
                            @endforeach                            


						</div>
					</div>

					

				</div>

				

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

@endsection

</div>

