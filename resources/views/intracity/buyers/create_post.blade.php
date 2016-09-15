@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<?php
$serverpreviUrL =$_SERVER['HTTP_REFERER'];
$serverRefer = explode("?",HTTP_REFERRER);
$lastPageName = substr($serverRefer[0], strrpos($serverRefer[0], '/') + 1);
$buyerSessionFromcityId = '';
$buyerSessionFromcityLocation = '';
$buyerSessionRateType = 1;
$buyerSessionFromLocationId = '';
$buyerSessionToLocationId = '';
$buyerSessionFromLocationName = '';
$buyerSessionToLocationName = '';
$buyerSessionFromDate = '';
$buyerSessionFromTime = '';
$buyerSessionLoadTypeId = '';
$buyerSessionVehicleTypeId = '';
$buyerSessionweight = '';
$buyerSessionweightType = '';
$url_array = array();
$url_array = explode ( '/', $_SERVER['HTTP_REFERER'] );
$previousURL = end ( $url_array );
if ($lastPageName == 'byersearchresults') {
	
	// Getting search session variables and stored in seperate varibles
	if (! empty ( Session::get ( 'buyerSessionFromcityId' ) )) {
		$buyerSessionFromcityId = Session::get ( 'buyerSessionFromcityId' );
		$buyerSessionFromcityLocation = Session::get ( 'buyerSessionFromcityLocation' );
		$buyerSessionRateType = Session::get ( 'buyerSessionRateType' );
		$buyerSessionFromLocationId = Session::get ( 'buyerSessionFromLocationId' );
		$buyerSessionToLocationId = Session::get ( 'buyerSessionToLocationId' );
		$buyerSessionFromLocationName = Session::get ( 'buyerSessionFromLocationName' );
		$buyerSessionToLocationName = Session::get ( 'buyerSessionToLocationName' );
		$buyerSessionFromDate = Session::get ( 'buyerSessionFromDate' );
		$buyerSessionFromTime = Session::get ( 'buyerSessionFromTime' );
		if (! empty ( Session::get ( 'buyerSessionLoadTypeId' ) ))
			$buyerSessionLoadTypeId = Session::get ( 'buyerSessionLoadTypeId' );
		if (! empty ( Session::get ( 'buyerSessionVehicleTypeId' ) ))
			$buyerSessionVehicleTypeId = Session::get ( 'buyerSessionVehicleTypeId' );
		$buyerSessionweight = Session::get ( 'buyerSessionweight' );
		$buyerSessionweightType = Session::get ( 'buyerSessionweightType' );
	}
}else{}

?>

<div class="main">

	<div class="container">
		@if (Session::has('success_message'))
		<div class="flash ">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
				Session::get('success_message') }}</p>
		</div>
		@endif @if (Session::has('error_message'))
		<div class="flash ">
			<p class="text-alert col-sm-12 text-center flash-txt alert-danger">{{
				Session::get('error_message') }}</p>
		</div>
		@endif {!! Form::open(array('url' => 'intracity/create_buyer_post',
		'id' => 'intracity-buyer-post', 'class'=>'form-group','enctype' =>
		'multipart/form-data' )) !!} <span class="pull-left"><h1
				class="page-title">Post & Get Quote (Intracity)</h1> <a href="#"
			class="change-service" data-toggle="modal"
			data-target="#change-service">Change Service</a></span>
			
			@if ($lastPageName == 'byersearchresults')
				<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
				@endif

		<div class="clearfix"></div>


		<div class="col-md-12 inner-block-bg inner-block-bg1">


			<div class="col-md-12 padding-none margin-top">
				<!--div class="col-md-12 form-control-fld margin-none">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  clas="is_commercial" value="1" checked /> <label for="is_commercial"><span></span>Commercial</label></div>
						<div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" clas="is_commercial" value="0" /> <label for="non_commercial"><span></span>Non Commercial</label></div>
					</div>
				</div-->
				<input type="hidden" name="is_commercial" id="non_commercial" value="0" />
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						 
					{!! Form::text('intra_from_location', $buyerSessionFromcityLocation, ['id' => 'intra_from_location','class' => 'form-control', 'placeholder' => 'Select City*']) !!}
                    {!! Form::hidden('lkp_city_id', $buyerSessionFromcityId, array('id' => 'lkp_city_id')) !!}
                    {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
					</div>
				</div>
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-rupee"></i></span> {!!
						Form::select('lkp_rate_type',array()+$rate_type,
						$buyerSessionRateType,['class'=>'selectpicker',
						'id'=>'rate_type']) !!}
					</div>
				</div>
				<div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span> {!!
							Form::text('from_location', $buyerSessionFromLocationName,['id'
							=> 'from_intra_location', 'class'=>'form-control', 'placeholder'
							=> 'From Location*']) !!} {!! Form::hidden('from_location_id',
							$buyerSessionFromLocationId, array('id' => 'from_location_id'))
							!!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span> {!!
							Form::text('to_location', $buyerSessionToLocationName,['id' =>
							'to_intra_location', 'placeholder' => 'To Location*',
							'class'=>'form-control',]) !!} {!! Form::hidden('to_location_id',
							$buyerSessionToLocationId, array('id' => 'to_location_id')) !!}
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span> {!!
							Form::text('pickup_date', $buyerSessionFromDate,['id' =>
							'pickup_date', 'placeholder' => 'Pickup Date*',
							'class'=>'form-control calendar pickup_date from-date-control','readonly' => true])
							!!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend date" id="pickup_time_datep">
							<span class="add-on"><i class="fa fa-clock-o"></i></span> {!!
							Form::text('pickup_time', $buyerSessionFromTime,['id' =>
							'pickup_time', 'class'=>'form-control',
							'placeholder' => 'Pickup Time*',
							'data-default-time'=>'false','readonly' => true]) !!}
						</div>
						<label for="pickup_time_datep" class="error" id="err_pickup_time_datep"></label>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span> {!!
							Form::select('load_type',(['' => 'Load Type*']+$load_type),
							$buyerSessionLoadTypeId,['class' =>'selectpicker
							form_control','id'=>'load_type']) !!}
						</div>
					</div>

					<div class="col-md-3 form-control-fld">
						<div class="col-md-9 padding-none">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-balance-scale"></i></span>
								{!! Form::text('units', $buyerSessionweight,['id' =>
								'weight','placeholder' => '0.00*','class'=>'form-control clsIDWeight_accepted clsIntraWeightGms']) !!} 
							</div>
						</div>
						
						<div class="col-md-3 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days manage">
									<div class="normal-select">
										{!! Form::select('lkp_ict_weight_parameter_id',array()+$weight_type,
										$buyerSessionweightType,['class' =>'selectpicker
										form_control clsSelMaxIntraWeight','id'=>'weight_type']) !!}</div>
								</span>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span> {!!
							Form::select('lkp_vehicle_id',array('' => 'Vehicle
							Type*')+$vehicle_types,
							$buyerSessionVehicleTypeId,['class'=>'selectpicker',
							'id'=>'vechile_type']) !!}
						</div>
					</div>

					<div class="col-md-6 form-control-fld">
						<img src="../images/truck.png" class="truck-type" /> <span
							class="truck-type-text">Vehicle Dimensions *<span id ='dimension'></span></span>
					</div>

				</div>
			</div>


		</div>

		<div class="clearfix"></div>

		<div class="container">
			<div class="col-md-4 col-md-offset-4">{!! Form::submit('Post',
				['name' => 'confirm','class'=>'btn theme-btn btn-block']) !!}</div>
		</div>
		{!! Form::Close() !!}

	</div>
</div>


<!-- Modal -->

@include('partials.footer')

</div>
@endsection
