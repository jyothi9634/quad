<div class="col-md-3 text-center">
	<div class="col-md-12 form-control-fld">
		<div class="radio-block">
			<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /><label for ="spot_lead_type"><span></span>Spot</label></div>
			<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" /><label for ="term_lead_type"><span></span>Term</label></div>
		</div>
	</div>
</div>

<div class="showhide_spot" id="showhide_spot">

	{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers']) !!}
	<div class="home-search-form">

		<div class="clearfix"></div>
		<div class="col-md-12 padding-none">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('from_location', request('from_location'), ['id' => 'from_location','class' => 'top-text-fld form-control clsFTLFromLocation', 'placeholder' => 'From Location*']) !!}
					{!! Form::hidden('from_city_id', request('from_city_id'), array('id' => 'from_location_id')) !!}
					{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('to_location', request('to_location'), ['id' => 'to_location','class' => 'top-text-fld form-control clsFTLtoLocation', 'placeholder' => 'To Location*']) !!}
					{!! Form::hidden('to_city_id', request('to_city_id'), array('id' => 'to_location_id')) !!}
				</div>
			</div>
			<div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-calendar-o"></i></span>
						{!! Form::text('dispatch_date', request('dispatch_date'), ['id' => 'datepicker_search','class' => 'form-control calendar from-date-control','readonly'=>true, 'placeholder' => 'Dispatch Date*']) !!}
						<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-calendar-o"></i></span>
						{!! Form::text('delivery_date', session('searchMod.delivery_date'), ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly'=>true, 'placeholder' => 'Delivery Date']) !!}
						<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
					</div>
				</div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-archive"></i></span>
						{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), request('lkp_load_type_id'), ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
					</div>
				</div>
				<div class="col-md-4 form-control-fld">

					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-balance-scale"></i></span>
						{!! Form::text('qty', request('qty'), ['id' => 'qty', 'search'=>'1','class' => 'form-control clsFTLQuantity', 'placeholder' => 'Qty']) !!}

						<span class="add-on unit1">
							{!!	Form::text('capacity',request('capacity'),array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}
					</span>
					</div>

				</div>
				<div class="clearfix"></div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-archive"></i></span>
                       	{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), request('lkp_vehicle_type_id'), ['class' => 'selectpicker','id' => 'vechile_type']) !!}
					</div>
				</div>
				<div class="col-md-4 form-control-fld">
					<img src="{{asset('/images/truck.png')}}" class="truck-type" />
					<span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $common::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
				</div>
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

<div class="showhide_term" id="showhide_term" style="display:none">
	{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
	<div class="home-search-form">

		<div class="clearfix"></div>
		<div class="col-md-12 padding-none">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('term_from_location', '', ['id' => 'term_from_location','class' => 'top-text-fld form-control alphaonly_strVal', 'placeholder' => 'From Location*']) !!}
					{!! Form::hidden('term_from_city_id', '', array('id' => 'term_from_location_id')) !!}
					{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('term_to_location', '', ['id' => 'term_to_location','class' => 'top-text-fld form-control alphaonly_strVal', 'placeholder' => 'To Location*']) !!}
					{!! Form::hidden('term_to_city_id', '', array('id' => 'term_to_location_id')) !!}
				</div>
			</div>


			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters), null, ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 form-control-fld pull-left">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), request('lkp_vehicle_type_id'), ['class' => 'selectpicker','id' => 'vechile_type']) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<img src="{{asset('/images/truck.png')}}" class="truck-type" />
				<span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $common::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
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