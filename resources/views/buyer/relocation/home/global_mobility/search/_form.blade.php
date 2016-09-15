{!! Form::open(['url' => 'byersearchresults','id'=>'search-form_buyer_relocationgm','method'=>'get']) !!}
	<div class="home-search gray-bg margin-top-none">
	{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
		<div class="col-md-12 padding-none">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('to_location',  request('to_location'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'City *']) !!}
                        {!! Form::hidden('to_location_id', request('to_location_id'), array('id' => 'to_location_id')) !!}
				</div>
			</div>

			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('from_date', request('from_date') ,  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Date *']) !!}
					<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-cog"></i></span>
					{!!	Form::select('relgm_service_type',(['' => 'Services'] + $lkp_relgm_services), request('relgm_service_type') ,['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'return addMeasurementValidation(this.value,"measurement")']) !!}
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend" id="measures_div">
					{!! Form::text('measurement', request('measurement') ,  ['id' => 'measurement','class' => 'form-control form-control1 clsGMSNoOfDays']) !!}
					<span class="add-on unit1 manage">
						{!! Form::text('measurement_unit', request('measurement_unit') ,  ['id' => 'measurement_unit','class' => 'form-control form-control1 valid','readonly' => true, 'placeholder' => 'Day(s)']) !!}
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-md-offset-4">
		<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
	</div>	
{!! Form::close() !!}	
