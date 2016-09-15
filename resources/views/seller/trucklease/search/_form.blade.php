{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers-tl']) !!}
<div class="home-search-form">
	<div class="col-md-12 padding-none">
		<div class="col-md-4 form-control-fld">
			<div class="input-prepend">
				<span class="add-on"><i class="fa fa-map-marker"></i></span>
				{!! Form::text('from_location', request('from_location'), ['id' => 'from_location','class' => 'top-text-fld form-control clsLocation', 'placeholder' => 'Location*']) !!}
				{!! Form::hidden('from_city_id', request('from_city_id'), array('id' => 'from_location_id')) !!}
				{!! Form::hidden('seller_district_id', request('seller_district_id'), array('id' => 'seller_district_id')) !!}
				{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
			</div>
		</div>

		<div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('dispatch_date', request('dispatch_date'), ['id' => 'dispatch_date1','class' => 'form-control clsFromDate from-date-control','readonly'=>true, 'placeholder' => 'From Date*']) !!}
					<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
				</div>
			</div>

			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('delivery_date', request('delivery_date'), ['id' => 'delivery_date1','class' => 'clsToDate form-control to-date-control','readonly'=>true, 'placeholder' => 'To Date*']) !!}
					<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_trucklease_lease_term_id', (['' => 'Lease Term*'] + $getAllleaseTypes ), request('lkp_trucklease_lease_term_id'), ['class' => 'selectpicker','id' => 'lkp_trucklease_lease_term_id']) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld pull-left">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-truck"></i></span>
					{!! Form::select('lkp_vehicle_type_id', (['' => 'Vehicle Type*'] + $vehicletypemasters +['20'=>'Vehicle Type (Any)']), request('lkp_vehicle_type_id'), ['class' => 'selectpicker','id' => 'vechile_type']) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<img src="{{asset('/images/truck.png')}}" class="truck-type" />
				<span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
			</div>
			<div class="clearfix"></div>

		</div>
	</div>
</div>
<div class="submit_container">
	<div class="col-md-4 col-md-offset-4">
		<!--button class="btn theme-btn btn-block">Get Quote</button-->
		<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
	</div>
</div>
{!! Form::close() !!}