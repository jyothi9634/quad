<div class="showhide_spot" id="showhide_spot">

	{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers']) !!}
	<div class="home-search-form">
		<div class="clearfix"></div>
		
		<div class="col-md-12 padding-none">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('from_location', request('from_location'), ['id' => 'from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
					{!! Form::hidden('from_city_id', request('from_city_id'), array('id' => 'from_location_id')) !!}
					{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
				</div>
			</div>
			
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('to_location', request('to_location'), ['id' => 'to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
					{!! Form::hidden('to_city_id', request('to_city_id'), array('id' => 'to_location_id')) !!}
				</div>
			</div>
			
			
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('dispatch_date', request('dispatch_date'), ['id' => 'datepicker_search','class' => 'form-control calendar from-date-control','readonly'=>true, 'placeholder' => 'Reporting Date*']) !!}
					<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
				</div>
			</div>
			<div class="clearfix"></div>									
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), request('lkp_load_type_id'), ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
				</div>
			</div>

			@if(request('qty')!='')
				{!! Form::hidden('qty', request('qty'), ['id' => 'qty']) !!}
			@else
				{!! Form::hidden('qty', '1', ['id' => 'qty']) !!}
			@endif
			{!!	Form::hidden('capacity',request('capacity'),array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}
															
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
                    {!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), request('lkp_vehicle_type_id'), ['class' => 'selectpicker','id' => 'vechile_types']) !!}
				</div>
			</div>

			<div class="col-md-4 form-control-fld pull-right">
				<img src="{{asset('/images/truck.png')}}" class="truck-type" />
				<span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
			</div>

		</div>
	</div>
	<div class="submit_container">
		<div class="col-md-4 col-md-offset-4">
			<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
		</div>
	</div>
	{!! Form::close() !!}

</div>

					