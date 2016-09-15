@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content') 
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<?php
$buyerSessionFromcity='';
$buyerSessionFromcityId = '';
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
$vehicle_type_name='Not Specified';
$load_type_name ='Not Specified';
$weight_type_name = '';
$buyerSessionDistrictId='';
$weight_type=array();

$url_search= explode("?",HTTP_REFERRER);
$url_search_search = substr($url_search[0], strrpos($url_search[0], '/') + 1);

if (! empty ( Session::get ( 'buyerSessionFromcityId' ) ) && $url_search_search == 'byersearchresults') {
        $buyerSessionFromcityId = Session::get ( 'buyerSessionFromcityId' );
	$buyerSessionFromcity = Session::get ( 'buyerSessionFromcityLocation' );
        $buyerSessionDistrictId = Session::get ( 'buyerSessionDistrictId' );
	$buyerSessionRateType = Session::get ( 'buyerSessionRateType' );
	$buyerSessionFromLocationId = Session::get ( 'buyerSessionFromLocationId' );
	$buyerSessionToLocationId = Session::get ( 'buyerSessionToLocationId' );
	$buyerSessionFromLocationName = Session::get ( 'buyerSessionFromLocationName' );
	$buyerSessionToLocationName = Session::get ( 'buyerSessionToLocationName' );
	$buyerSessionFromDate = Session::get ( 'buyerSessionFromDate' );
	$buyerSessionFromTime = Session::get ( 'buyerSessionFromTime' );
	if (! empty ( Session::get ( 'buyerSessionLoadTypeId' ) )){
		$buyerSessionLoadTypeId = Session::get ( 'buyerSessionLoadTypeId');
		//$load_type_name = $load_type[$buyerSessionLoadTypeId];
                $load_type_name=$commonComponent->getLoadType($buyerSessionLoadTypeId);
	}
	else{$load_type_name ='Not Specified';}

	if (! empty ( Session::get ( 'buyerSessionVehicleTypeId' ) ) && Session::get ( 'buyerSessionVehicleTypeId' )!=""){
		$buyerSessionVehicleTypeId = Session::get ( 'buyerSessionVehicleTypeId' );
		//$vehicle_type_name = $vehicle_type[$buyerSessionVehicleTypeId];
                 $vehicle_type_name = $commonComponent->getVehicleType($buyerSessionVehicleTypeId);
	}
	else{$vehicle_type_name ='Not Specified';}
	$buyerSessionweight = Session::get ( 'buyerSessionweight' );
        if (! empty ( Session::get ( 'buyerSessionweightType' ) )){
	$buyerSessionweightType = Session::get ( 'buyerSessionweightType' );
	//$weight_type_name = $weight_type[$buyerSessionweightType];
        }
}
?>						
							
{!! Form::open(['url' =>'byersearchresults','id' => 'intracity_buyer_search_form','method'=>'get']) !!}
<div class="main">

		<div class="container">
				<div class="home-search gray-bg margin-top-none">
					<div class="col-md-12 padding-none">
						<input type="hidden" name="is_commercial" id="non_commercial" value="0" />
						<!--div class="col-md-12 form-control-fld margin-none">
							<div class="radio-block">
								<div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" checked /> <label for="is_commercial"><span></span>Commercial</label></div>
								<div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0" /> <label for="non_commercial"><span></span>Non Commercial</label></div>
							</div>
						</div-->

						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('intra_from_location', $buyerSessionFromcity, ['id' => 'intra_from_location','class' => 'form-control', 'placeholder' => 'Select City*']) !!}
                    {!! Form::hidden('lkp_city_id', $buyerSessionFromcityId, array('id' => 'lkp_city_id')) !!}
                    {!! Form::hidden('seller_district_id', $buyerSessionDistrictId, array('id' => 'seller_district_id')) !!}
								
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-rupee"></i></span>
								{!! Form::select('rate_type',array()+$rate_types,$buyerSessionRateType,['class'=>'selectpicker', 'id'=>'rate_type']) !!}
								
							</div>
						</div>
						
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', $buyerSessionFromLocationName,['id' => 'from_intra_location','class'=>'form-control', 'placeholder' => 'From Location*']) !!}
							{!! Form::hidden('from_location_id', $buyerSessionFromLocationId, array('id' =>'from_location_id')) !!}
					  
				
								</div>
							</div>
							
							<div class="clearfix"></div>
							
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', $buyerSessionToLocationName,['id' => 'to_intra_location','class'=>'form-control', 'placeholder' => 'To Location*']) !!}
							{!!	Form::hidden('to_location_id', $buyerSessionToLocationId, array('id' =>'to_location_id')) !!}								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('pickup_date', $buyerSessionFromDate, ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control', 'placeholder' => 'Pickup Date*','readonly' => true]) !!}
							<input type="hidden" name="is_flexiable_hidden" id="is_flexiable_hidden" value="0">
					   								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend date" id="pickup_time_search">
									<span class="add-on"><i class="fa fa-clock-o"></i></span>
							{!! Form::text('pickup_time', $buyerSessionFromTime,['id' => 'pickup_time','class'=>'form-control', 'placeholder' =>'Pickup Time*', 'data-default-time'=>'false','readonly' => true]) !!}		</div>
								<label for="pickup_time_search" class="error" id="err_pickup_time_datep"></label>
							</div>
							
							<div class="clearfix"></div>
							
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('load_type',(['11'=>'Load Type (Any)']+$load_type), $buyerSessionLoadTypeId,['class' =>'selectpicker form_control','id'=>'load_type']) !!}
								</div>
							</div>

							<div class="col-md-4 form-control-fld">
							<div class="col-md-9 padding-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									{!! Form::text('weight', $buyerSessionweight,['id' => 'weight','class'=>'form-control numberVal fourdigitsthreedecimals_deciVal', 'placeholder' =>'0.00*']) !!}								
								</div>
							</div>
							<div class="col-md-3 padding-none">
								<div class="input-prepend">
							
										<span class="add-on unit-days manage">
											<div class="input-prepend">
							{!! Form::select('weight_type',$weight_types, $buyerSessionweightType,['class' =>'selectpicker bs-select-hidden','id'=>'weight_type']) !!}
												
											</div>
										</span>
									</div>
								</div>	
							</div>

							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
{!! Form::select('lkp_vehicle_id',array('' => 'Vehicle Type')+$vehicle_types ,$buyerSessionVehicleTypeId,['class'=>'selectpicker','id'=>'vechile_type']) !!}
								</div>
							</div>
							
							<div class="col-md-6 form-control-fld">
								<img src="../images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions *<span id ='dimension'></span></span>
							</div>
                                                        

                                                        
						</div>
					</div>
				</div>
		
			<div class="container">
				<div class="col-md-4 col-md-offset-4">
                                    {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
			{!! Form::submit('Search', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}	
				</div>
			</div>
			{!! Form::Close() !!}					
							
							
							
							
							
							
							
							
							
							
							
	<!-- Include static content block on the search page and footer -->
	@include('partials.searchcontentblock')
	@include('partials.footer')


</div>		
@endsection