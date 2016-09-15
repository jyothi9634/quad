<div class="col-md-12 form-control-fld margin-none">
    <div class="radio-block">
        <div class="radio_inline">
        	<input type="radio" name="is_commercial" id="is_commercial" value="1" {{ (request('is_commercial')==1 || !request()->exists('is_commercial'))? 'checked="checked"':'' }} /> 
        	<label for="is_commercial"><span></span>Commercial</label>
       	</div>
        <div class="radio_inline">
        	<input type="radio" name="is_commercial" id="non_commercial" value="0" {{ (request('is_commercial')===0)? 'checked="checked"':'' }}/> <label for="non_commercial"><span></span>Non Commercial</label>
        </div>
    </div>
</div>
                                       
<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>

		{!! Form::text('from_location', request('from_location'), ['id' => 'from_location', 'class'=>'form-control clsLocation','placeholder' => 'Location *']) !!}
		{!! Form::hidden('from_location_id', request('from_location_id'), array('id' => 'from_location_id')) !!}
                                    
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('from_date', request('from_date'), ['id' => 'dispatch_date1','class' => 'clsFromDate form-control from-date-control', 'placeholder' => 'From Date *','readonly'=>"readonly"]) !!}
		<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="{{ request('dispatch_flexible_hidden') }}">
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('to_date', request('to_date'), ['id' => 'delivery_date1','class' => 'clsToDate form-control to-date-control', 'placeholder' => 'To Date *','readonly'=>"readonly"]) !!}
		<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="{{ request('delivery_flexible_hidden') }}">
	</div>
</div>
<div class="clearfix"></div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-archive"></i></span>
		{!!	Form::select('lkp_vehicle_type_id',(['' => 'Select Vehicle Type *'] +$vehicle_type + ['20'=>'Vehicle Type (Any)']), request('lkp_vehicle_type_id'),['class' =>'selectpicker form_control','id'=>'vechile_type']) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-archive"></i></span>
		{!!	Form::select('lkp_trucklease_lease_term_id',(['' => 'Lease Term *'] +$getAllleaseTypes ), request('lkp_trucklease_lease_term_id'),['class' =>'selectpicker form_control','id'=>'lkp_trucklease_lease_term_id']) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-wheelchair"></i></span>
		{!!	Form::select('driver_availability',$driver_availability, request('driver_availability'),['class' =>'selectpicker form_control','id'=>'driver_availability']) !!}
	</div>
</div>

{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}

<div class="col-md-6 form-control-fld">
	<img src="images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
</div>