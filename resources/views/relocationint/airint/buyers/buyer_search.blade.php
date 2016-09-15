<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
			{!! Form::text('from_location', (Session::has('session_from_location_buyer'))?Session::get('session_from_location_buyer'):''  , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location (Only Major Cities) *']) !!}
            {!! Form::hidden('from_location_id', (Session::has('session_from_city_id_buyer'))?Session::get('session_from_city_id_buyer'):'' , array('id' => 'from_location_id')) !!}
            <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
			{!! Form::text('to_location',(Session::has('session_to_location_buyer'))?Session::get('session_to_location_buyer'):'',  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location (Only Major Cities) *']) !!}
           	{!! Form::hidden('to_location_id',  (Session::has('session_to_city_id_buyer'))?Session::get('session_to_city_id_buyer'):'' , array('id' => 'to_location_id')) !!}
	</div>
</div>
	
<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('from_date', (Session::has('session_dispatch_date_buyer'))?Session::get('session_dispatch_date_buyer'):'',  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
		<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
	</div>
</div>
<div class="clearfix"></div>	
<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('to_date', (Session::has('session_delivery_date_buyer'))?Session::get('session_delivery_date_buyer'):'' , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
		<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
	</div>
</div>
<div class="clearfix"></div>							
<div class="table-div table-style1">
		
	<!-- Table Head Starts Here -->
	<div class="table-heading inner-block-bg">
		<div class="col-md-8 padding-left-none">Carton Type</div>
		<div class="col-md-4 padding-left-none">Nos</div>
	</div>

	<!-- Table Head Ends Here -->
	<div class="table-data">
		{{--*/ $cartons = $commonComponent->getCartons() /*--}}
		{{--*/ $i=1/*--}}
		@foreach($cartons as $carton)
	
        <div class="table-row inner-block-bg">
            <div class="col-md-8 padding-left-none">{{ $carton->carton_type }} ({{ $carton->carton_description }}  )</div>
            <div class="col-md-4 padding-left-none">
            	<input type="text" class="cartons form-control form-control1 input-short pull-left clsRIASNoOfCartons" name="cartons_{{ $carton->id}}" value="{{(Session::has('session_cartons_'.$i))?Session::get('session_cartons_'.$i):''}}"/>
            </div>
        </div>
        {{--*/ $i= $i+1 /*--}}
        @endforeach

	</div>
</div>	
															
<div class="col-md-4 col-md-offset-4">
	<button class="btn theme-btn btn-block" type="submit">Search</button>
</div>