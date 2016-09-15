@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content') 
@include('partials.page_top_navigation') 
<?php
$buyerSessionFromcity='';
$buyerSessionDistrictId='';
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
$buyerSessionCommercialType = '';
$weight_type=array();
if (! empty ( Session::get ( 'buyerSessionFromcityId' ) )) {
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
        $buyerSessionCommercialType = Session::get ( 'buyerSessionCommercialType' );
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
<div class="main">

	<div class="container">
		<h1 class="page-title">Search Results (Intracity)</h1>
		<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>

		<!-- Search Block Starts Here -->

		<div class="search-block inner-block-bg">
			
			<div class="from-to-area">
				<span class="search-result"> <i class="fa fa-map-marker"></i> <span
					class="location-text">{!! $buyerSessionFromLocationName !!} to {!! $buyerSessionToLocationName !!}</span>
				</span>
			</div>
			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Pickup Date</p>
					<span class="search-result"> <i class="fa fa-calendar-o"></i>
					 {!! $buyerSessionFromDate !!}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">Pickup Time</p>
					<span class="search-result"> <i class="fa fa-clock-o"></i> 
					{!! $buyerSessionFromTime !!}
	
					</span>
				</div>
			</div>
			<div>
				<p class="search-head">Load Type</p>
				<span class="search-result">{!! $load_type_name !!}</span>
			</div>
			<div>
				<p class="search-head">Quantity</p>
				<span class="search-result">{!! $buyerSessionweight !!} {!! $weight_type_name !!}</span>
			</div>
			<div>
				<p class="search-head">Vehicle Type</p>
				<span class="search-result">{!! $vehicle_type_name!!}</span>
			</div>
			<div class="search-modify" data-toggle="modal"
				data-target="#modify-search">
				<span>Modify Search +</span>
			</div>
			
		</div>

		<!-- Search Block Ends Here -->



		<h2 class="side-head pull-left">
			Filter Results
			
		</h2>
		<div class="page-results pull-left col-md-2 padding-none">
			<div class="form-control-fld">
				<div class="normal-select">
					<select class="selectpicker">
						<option value="0">10 Records Per page</option>
					</select>
				</div>
			</div>
		</div>
		<a href="{{url('intracity/buyer_post')}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>

		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			<div class="main-inner">

				<!-- Left Section Starts Here -->


				<div class="main-left">
                                    {!! $filter->open !!}
                                @if ((Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="") || (!isset($_REQUEST['is_search'])))    
				<h2 class="filter-head">Price Range</h2>
                                <div class="price-range inner-block-bg">
                                        <div id="slider-range" class="margin-top"></div>
                                        <p class="margin-top">
                                        <input type="hidden" id="amount" class="form-control form-control1" readonly name="price">

                                        <?php
                                        $price_from = isset($_REQUEST['price_from']) ? $_REQUEST['price_from'] : 1000;
                                        $price_to = isset($_REQUEST['price_to']) ? $_REQUEST['price_to'] : 2000;
                                        $rang_slider = $price_from.','.$price_to;
                                        ?>
                                        <input type="hidden" id="price_from" name="price_from" value="<?php echo $price_from; ?>" />
                                        <input type="hidden" id="price_to" name="price_to" value="<?php echo $price_to; ?>" />
                                        <span class="pull-left"><?php echo $price_from; ?>/-</span>
                                        <span class="pull-right"><?php echo $price_to; ?>/-</span>
                                        </p>
                                </div>
                                @endif
                                    <div class="inner-block-bg">
                                    	<div class="normal-select margin-bottom margin-top">
                                        	{!! $filter->field('sqi.lkp_vehicle_type_id') !!}
                                    	</div>
                                    	<div class="normal-select margin-bottom">
	                                        {!! $filter->field('sqi.lkp_load_type_id') !!}
	                                        {!! Form::hidden('rate_type',$buyerSessionRateType, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('lkp_city_id',$buyerSessionFromcityId, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('from_location_id',$buyerSessionFromLocationId, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('to_location_id',$buyerSessionToLocationId, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('from_location',$buyerSessionFromLocationName, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('to_location',$buyerSessionToLocationName, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('load_type',$buyerSessionLoadTypeId, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('lkp_vehicle_id',$buyerSessionVehicleTypeId, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('pickup_date',$buyerSessionFromDate, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('pickup_time',$buyerSessionFromTime, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('weight',$buyerSessionweight, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('weight_type',$buyerSessionweightType, ['class' =>'form_control']) !!}
	                                        {!! Form::hidden('seller_district_id', $buyerSessionDistrictId, array('id' => 'seller_district_id')) !!}
	                                        {!! Form::hidden('intra_from_location', $buyerSessionFromcity) !!}
                                                {!! Form::hidden('is_commercial', $buyerSessionCommercialType) !!}
                                    	</div>
                                    </div>
                                {!! $filter->close !!}   
				<?php  /* $selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array();
                                        ?>
                                @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
                                    @if (Session::has('layered_filter_payments'))
                                        <h2 class="filter-head">Payment Mode</h2>
                                        <div class="payment-mode inner-block-bg">
                                                @foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
                                                <?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
                                                <div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8"> {{ $paymentName }}</span></div>
                                                @endforeach
                                        </div>
                                    @endif
                                @endif
				<h2 class="filter-head">Tracking</h2>
                                <div class="tracking inner-block-bg">
                                        <div class="check-box">
                                                <input type="checkbox" name="tracking" value="1" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']) { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">Milestone</span>
                                                </div>
                                        <div class="check-box"><input type="checkbox" name="tracking1" value="2" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Real time</span></div>
                                </div>


                                <div class="tracking inner-block-bg">
                                        <div class="check-box"><input type="checkbox" name="ftltopseller_orders" onClick="this.form.submit()" <?php if(isset($_REQUEST['ftltopseller_orders']) && $_REQUEST['ftltopseller_orders']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Orders) </span></div>
                                        <div class="check-box"><input type="checkbox" name="ftltopseller_rated" onClick="this.form.submit()" <?php if(isset($_REQUEST['ftltopseller_rated']) && $_REQUEST['ftltopseller_rated']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Rated) </span></div>
                                </div>
				
                                <?php $selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
					?>
                                @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
                                        @if (Session::has('layered_filter'))
                                            <h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
                                            <div class="seller-list inner-block-bg">
                                                    @foreach (Session::get('layered_filter') as $userId => $userName)
                                                    <?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
                                                    <div class="check-box"><input type="checkbox"  class="filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onClick="this.form.submit()" /><span class="lbl padding-8"> {{ $userName }}</span></div>
                                                    @endforeach
                                            </div>
                                        @endif
                                @endif                           
<?php */
	//Session::forget('show_layered_filter');
?>
        
			</div>

				<!-- Left Section Ends Here -->


				<!-- Right Section Starts Here -->

				<div class="main-right">
				<div class="col-md-12 padding-none">

					<!-- Table Starts Here -->

					<div class='table-data' id="intra_booknow_buyer_form">
						{!! $gridBuyer !!}			
					</div>
					

				</div>

				</div>

				

		

			</div>
                    <a href="{{url('intracity/buyer_post')}}"><button class="btn post-btn pull-right">Post & get Quote</button></a>
		</div>

		
		
		
	</div>
</div>

<div class="modal fade" id="modify-search" role="dialog">
    
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			{!! Form::open(['url' =>'byersearchresults','id' => 'intracity_buyer_search_form','method'=>'get']) !!}	
                        <div class="modal-body">
				<div class="col-md-12 modal-form">
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> 
					{!! Form::text('intra_from_location', $buyerSessionFromcity, ['id' => 'intra_from_location','class' => 'form-control', 'placeholder' => 'Select City*']) !!}
                    {!! Form::hidden('lkp_city_id', $buyerSessionFromcityId, array('id' => 'lkp_city_id')) !!}
                    {!! Form::hidden('seller_district_id', $buyerSessionDistrictId, array('id' => 'seller_district_id')) !!}
					{!! Form::hidden('is_commercial', $buyerSessionCommercialType) !!}
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
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> 
                                                                {!! Form::text('to_location', $buyerSessionToLocationName,['id' => 'to_intra_location','class'=>'form-control', 'placeholder' => 'To Location*']) !!}
							{!!	Form::hidden('to_location_id', $buyerSessionToLocationId, array('id' =>'to_location_id')) !!}
							</div>
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
							<div class="col-md-8 padding-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									{!! Form::text('weight', $buyerSessionweight,['id' => 'weight','class'=>'form-control numberVal fourdigitsthreedecimals_deciVal', 'placeholder' =>'0.00*']) !!}
	                            </div>
                            </div>
                            <div class="col-md-4 padding-none">
	                            <div class="input-prepend">    
	                                <span class="add-on unit-days">
										<div class="normal-select">
											{!! Form::select('weight_type',$weight_types, $buyerSessionweightType,['class' =>'selectpicker bs-select-hidden','id'=>'weight_type']) !!}
										</div>
									</span>
								</div>
							</div>
						</div>

						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span> 
                                                                {!! Form::select('lkp_vehicle_id',array('' => 'Vehicle Type')+$vehicle_type ,$buyerSessionVehicleTypeId,['class'=>'selectpicker','id'=>'vechile_type']) !!}
							</div>
						</div>

						<div class="col-md-6 form-control-fld">
							<img src="../images/truck.png" class="truck-type" /> <span
								class="truck-type-text">Vehicle Dimensions *</span>
						</div>
                                                
                                                @if($buyerSessionCommercialType == 1)
                                                            {{--*/ $is_commercial = "checked" /*--}}
                                                    @else
                                                            {{--*/ $is_commercial = "" /*--}}
                                                    @endif
                                                    @if($buyerSessionCommercialType == 0)
                                                            {{--*/ $is_noncommercial = "checked" /*--}}
                                                    @else
                                                            {{--*/ $is_noncommercial = "" /*--}}
                                                @endif
                                                
                                                <!--div class="col-md-12 form-control-fld margin-none">
                                                    <div class="radio-block">
                                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" {{ $is_commercial }} /> <label for="is_commercial"><span></span>Commercial</label></div>
                                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0"  {{ $is_noncommercial }}/> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                                    </div>
                                                </div-->
						<input type="hidden" name="is_commercial" id="non_commercial" value="0" />
						
					</div>
				</div>
				
				<div class="clearfix"></div>
				
				<div class="col-md-4 col-md-offset-4 margin-bottom">
                                {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                                    {!! Form::submit('Search', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}
                            </div>
                            
                            
			</div>
                        
                            
			
                        {!! Form::Close() !!}	
		</div>

	</div>
    
</div>

<div class="clearfix"></div>
<!-- Right Starts Here -->
@include('partials.footer')
<!-- Right Ends Here -->

@endsection
