@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">

		<div class="container">
				{!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form' , 'autocomplete'=>'off','method'=>'get']) !!}	
				<div class="home-search gray-bg margin-top-none">
					<div class="col-md-12 padding-none">
					<div class="col-md-12 form-control-fld margin-none">
	                            <div class="radio-block">
	                                <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" checked /> <label for="is_commercial"><span></span>Commercial</label></div>
	                                <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0" /> <label for="non_commercial"><span></span>Non Commercial</label></div>
	                            </div>
	                        </div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								
								{!! Form::text('from_location', '', ['id' => 'from_location', 'class'=>'form-control clsFTLFromLocation','placeholder' => 'From Location *']) !!}
								{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', '', ['id' => 'to_location', 'class'=>'form-control clsFTLtoLocation', 'placeholder' => 'To Location *']) !!}
								{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
							</div>
						</div>
						
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', '', ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *','readonly'=>"readonly"]) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', '', ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date','readonly'=>"readonly"]) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!!	Form::select('lkp_load_type_id',(['' => 'Select Load Type *'] +$load_type ), null,['class' =>'selectpicker form_control','id'=>'load_type','onChange'=>'return GetCapacity()']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input type="text" placeholder="Qty *" class="form-control clsFTLQuantity" name="quantity" id="quantity" search="1">
									<span class="add-on unit1">{!!	Form::text('capacity',null,array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}</span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!!	Form::select('lkp_vehicle_type_id',(['20'=>'Vehicle Type (Any)'] +$vehicle_type ), null,['class' =>'selectpicker form_control','id'=>'vechile_type']) !!}
								</div>
							</div>
							{!!	Form::hidden('no_of_loads',null,array('class'=>'form-control','placeholder'=>'Loads','id'=>'no_of_loads')) !!}
							{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}

							
							
							<div class="col-md-6 form-control-fld">
								<img src="images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'></span></span>
							</div>
                                                        
                            
						
					</div>
				</div>
			</div>
			<div class="container">
				<div class="col-md-4 col-md-offset-4">
					{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}	
				</div>
			</div>
			{!! Form::close() !!}

			
			<!-- Include static content block on the search page and footer -->
			@include('partials.searchcontentblock')
			@include('partials.footer')
</div>

@endsection