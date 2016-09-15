	<div class="col-md-4 form-control-fld">
		<div class="input-prepend">
			<span class="add-on"><i class="fa fa-map-marker"></i></span>
			{!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
			{!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
			{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
		</div>
	</div>
	<div class="col-md-4 form-control-fld">
		<div class="input-prepend">
			<span class="add-on"><i class="fa fa-map-marker"></i></span>
			{!! Form::text('to_location', '',  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
			{!! Form::hidden('to_location_id', '' , array('id' => 'to_location_id')) !!}
		</div>
	</div>

	<div class="col-md-4 form-control-fld">
		<div class="input-prepend">
			<span class="add-on"><i class="fa fa-calendar-o"></i></span>
			{!! Form::text('valid_from', '',  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
			<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
			
		</div>
	</div>
	<div class="clearfix"></div>
	{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
	{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
	<div class="col-md-4 form-control-fld">
		<div class="input-prepend">
			<span class="add-on"><i class="fa fa-calendar-o"></i></span>
			{!! Form::text('valid_to', '' , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
			<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
		</div>
	</div>
	<div class="clearfix"></div>
