@extends('app')
@section('content') 


	<div class="main-container">	
		<div class="container container-inner">			
		<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
		<!-- Left Nav Ends Here -->
		{!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form']) !!}			
			<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				
				<div class="block">
					<div class="tab-nav">
						<!-- Navigation links Starts Here -->
						@include('partials.search_navigation_links')
						<!-- Navigation links Starts Here -->
					</div>
					
					
                    @if (Session::has('cancelsuccessmessage'))
                        <div class="flash alert-info">test
                            <p class="text-success col-sm-12 text-center flash-txt-counterofer">{{
                        Session::get('cancelsuccessmessage') }}</p>
                        </div>
                    @endif
					
					
					
					<div class="clearfix"></div>
					<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">							
							{!! Form::text('from_location', '', ['id' => 'from_location', 'class'=>'form-control','placeholder' => 'From Location']) !!}
							{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
							{!! Form::text('to_location', '', ['id' => 'to_location', 'class'=>'form-control', 'placeholder' => 'To Location']) !!}
							{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="clearfix"></div>
					
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">							
							{!! Form::text('from_date', '', ['id' => 'dispatch_date1','class' => 'calendar form-control', 'placeholder' => 'Dispatch Date']) !!}
							<!--<input type="checkbox" name="is_flexiable" value="1">-->
							<input type="hidden" name="is_flexiable_hidden" id="is_flexiable_hidden" value="0">
						</div>
						
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
							{!! Form::text('to_date', '', ['id' => 'delivery_date1','class' => 'calendar form-control', 'placeholder' => 'Delivery Date']) !!}
							<input type="hidden" name="is_flexiable_hidden1" id="is_flexiable_hidden" value="0">
						</div>
						<div class="clearfix"></div>
						<div class="form-group col-md-3 col-sm-3 col-xs-12 padding-none">
							{!!	Form::select('lkp_load_type_id',(['' => 'Select Load Type'] +$load_type), null,['class' =>'selectpicker form_control','id'=>'load_type','onChange'=>'return GetCapacity()']) !!}
						</div>
						<div class="col-md-2 col-sm-2 col-xs-6 padding-right-none mobile-padding-none"><input type="text" placeholder="Qty" class="form-control" name="quantity" id="quantity"></div>
						<div class="col-md-2 col-sm-2 col-xs-6 padding-right-none">
							{!!	Form::text('capacity',null,array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}
						</div>
						<div class="clearfix"></div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
							{!!	Form::select('lkp_vehicle_type_id',(['' => 'Select Vehicle Type'] +$vehicle_type), null,['class' =>'selectpicker form_control','id'=>'vehicle_type', 'onChange'=>'return CheckLoads(this.value)']) !!}
						</div>
						
						{!!	Form::hidden('no_of_loads',null,array('class'=>'form-control','placeholder'=>'Loads','id'=>'no_of_loads')) !!}
						
						<div class="col-md-8 col-sm-8 col-xs-12 padding-right-none mobile-padding-none">
							
								 <div class="col-md-12 col-sm-12 col-xs-12 padding-none">
								 	<img src="images/truck.png" />
								 	&nbsp;&nbsp;Vehicle Dimensions* <span id ='dimension'></span></div>
								 <div class="clearfix"></div>							
						</div>
					{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn black-btn pull-right','id' => 'Search']) !!}				
				</div>
			</div>
			{!! Form::close() !!}
		<!-- Right Starts Here -->
		@include('partials.right')
		<!-- Right Ends Here -->
		</div>
	</div>
		
@endsection