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

		{!! Form::text('from_location', request('from_location'), ['id' => 'from_location', 'class'=>'form-control clsTHFromLocation','placeholder' => 'From Location *']) !!}
		{!! Form::hidden('from_location_id', request('from_location_id'), array('id' => 'from_location_id')) !!}
                                        
	</div>
</div>
	
<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
		{!! Form::text('to_location', request('to_location'), ['id' => 'to_location', 'class'=>'form-control clsTHToLocation', 'placeholder' => 'To Location *']) !!}
		{!! Form::hidden('to_location_id', request('to_location_id'), array('id' => 'to_location_id')) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('from_date', request('from_date'), ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control clsTHReportingDate', 'placeholder' => 'Reporting Date *','readonly'=>"readonly"]) !!}
		<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="{{ request('dispatch_flexible_hidden') }}" />
	</div>
</div>	

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-archive"></i></span>
		{!!	Form::select('lkp_load_type_id',( ['11'=>'Load Type (Any)'] +$load_type ), request('lkp_load_type_id'),['class' =>'selectpicker form_control','id'=>'load_type','onChange'=>'return GetCapacity()']) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-balance-scale"></i></span>
		{!! Form::text('quantity', request('quantity'), ['id' => 'quantity', 'class'=>'form-control clsTHQuantity','placeholder' => 'Qty *']) !!}
		<span class="add-on unit1">{!!	Form::text('capacity',request('capacity'),array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}</span>
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-archive"></i></span>
        {!!	Form::select('lkp_vehicle_type_id',(['20'=>'Vehicle Type (Any)'] +$vehicle_type ), request('lkp_vehicle_type_id'),['class' =>'selectpicker form_control','id'=>'vechile_type']) !!}
    </div>
</div>

<div class="col-md-4 form-control-fld pull-right">
	<img src="images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
</div>

{!!	Form::hidden('no_of_loads',null,array('class'=>'form-control','placeholder'=>'Loads','id'=>'no_of_loads')) !!}
{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}