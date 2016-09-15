@extends('app') @section('content')

		<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
@inject('commoncomponent', 'App\Components\CommonComponent')
@if((Session::get ( 'service_id' ) == ROAD_PTL)  || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN))

{{--*/ $packagename=$commoncomponent->getPackageType($lkp_packaging_type_id) /*--}}
@endif	


		<!-- Page top navigation Ends Here-->
<div class="clearfix"></div>
<div class="main">

	<div class="container">
		<h1 class="page-title">Search Results
			@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
			@elseif(Session::get('service_id') == RAIL)
				(RAIL)
			@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
			@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
			@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
			@elseif(Session::get('service_id') == COURIER)
				(COURIER)
                        @elseif(Session::get('service_id') == RELOCATION_INTERNATIONAL)
				(RELOCATION INTERNATIONAL)
			@endif</h1>
		<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>

		<!-- Search Block Starts Here -->

		<div class="search-block inner-block-bg">
			<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $from_location }} @if(isset($to_location))to {{ $to_location }}@endif</span>
						</span>
			</div>
			@if(isset($ratecarttype) && $ratecarttype !='')
				<div>
					<p class="search-head">Post For</p>
					<span class="search-result">{{ $ratecarttype }}</span>
				</div>
			@endif
			@if(isset($lkp_load_type_id) && $lkp_load_type_id !='')
				<div>
					<p class="search-head">Load Type</p>
					  <span class="search-result">{{ $commoncomponent::getLoadType($lkp_load_type_id) }}</span>
				</div>
			@endif
			@if((Session::get ( 'service_id' ) == COURIER) )
			<div>
			<p class="search-head">Destination Type</p>
				@if(isset($courier_delivery_type) && $courier_delivery_type !='')
					<span class="search-result">{{ $courier_delivery_type }}</span>
				@endif
			</div>
			@endif
			<div>
			@if(Session::get ( 'service_id' ) == ROAD_FTL)
				<p class="search-head">Vehicle Type</p>
				@if(isset($lkp_vehicle_type_id) && $lkp_vehicle_type_id !='')
					<span class="search-result">{{ $commoncomponent::getVehicleType($lkp_vehicle_type_id) }}</span>
				@endif
			@elseif((Session::get ( 'service_id' ) == ROAD_PTL)  || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN) )
				<p class="search-head">Packaging Type</p>
				@if(isset($lkp_packaging_type_id) && $lkp_packaging_type_id !='')
					<span class="search-result">{{ $packagename }}</span>
				@endif
			@elseif((Session::get ( 'service_id' ) == COURIER) )
				
				<p class="search-head">Courier Type</p>
				@if(isset($courier_types) && $courier_types !='')
					<span class="search-result">{{ $courier_types }}</span>
				@endif
			@endif

			@if((Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY) )
				<div>
					<p class="search-head">Service</p>
					<span class="search-result">{{ $lkp_relgm_services[Session::get('session_term_relgm_service_type')] }}</span>
				</div>
			@endif

			</div>
			
			

			<div class="search-modify" data-toggle="modal" data-target="#modify-search">
				<span>Modify Search +</span>
			</div>
		</div>

		<!-- Search Block Ends Here -->



		<h2 class="side-head pull-left">Filter Results</h2>


		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			<div class="main-inner">

				<!-- Left Section Starts Here -->

				<div class="main-left">
					
                                        @if((Session::get ( 'service_id' )!=RELOCATION_DOMESTIC) && (Session::get ( 'service_id' )!=RELOCATION_INTERNATIONAL) && (Session::get ( 'service_id' )!=RELOCATION_GLOBAL_MOBILITY))
                                        <h2 class="filter-head">Search Filter</h2>
						<div class="inner-block-bg">
							<div class="gray_bg">

								{!! Form::open(['url' => 'termsellersearchresults','id'=>'seller_term_posts_buyers_search_filter','method'=>'get','class'=>'filter_form']) !!}
								<div class="col-xs-12 padding-none margin-bottom  displayNone">
									{!! Form::text('term_from_location', $from_location, ['class' => 'form-control form-control1 ','id'=>'term_from_location1', 'readonly' => true]) !!}
									{!! Form::hidden('term_from_location_id', $from_city_id, array('id' => 'term_from_location1_id1')) !!}
									{!! Form::hidden('term_from_city_id', $from_city_id, array('id' => 'term_from_city_id1')) !!}
									{!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id1')) !!}
									{!! Form::hidden('spot_or_term', 2, array('id' => 'spot_or_term')) !!}
									<input type="hidden" name="zone_or_location" id="zone_or_location" value="<?php echo isset($_REQUEST['zone_or_location']) ? $_REQUEST['zone_or_location'] : ""; ?>"/>

								</div>
								@if(isset($to_location))
								<div class="col-xs-12 padding-none margin-bottom displayNone">
									{!! Form::text('term_to_location', $to_location, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									{!! Form::hidden('term_to_location_id', $to_city_id, array('id' => 'term_to_location1_id')) !!}
									{!! Form::hidden('term_to_city_id', $to_city_id, array('id' => 'term_to_city_id')) !!}
								</div>
								@endif
								<div class="clearfix"></div>
								<div class="col-xs-12 padding-none margin-bottom">
									<div class="normal-select">
									@if(Session::get ( 'service_id' ) != COURIER)
									   @if(isset($load_type_name) && $load_type_name !='')
											{!! Form::text('load_type_name', $load_type_name, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
											@if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id'] != '')
												{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_id'], array('id' => 'lkp_load_type_ids')) !!}
											@elseif(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids'] != '')
												{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_ids'], array('id' => 'lkp_load_type_ids')) !!}
											@endif
										@else
											{!! $filter->field('bqi.lkp_load_type_id') !!}
											@if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id'] != '')
												{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_id'], array('id' => 'lkp_load_type_ids')) !!}
											@elseif(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids'] != '')
												{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_ids'], array('id' => 'lkp_load_type_ids')) !!}
											@endif
										@endif
									@endif
									</div>
								</div>
								<div class="col-xs-12 padding-none">
									<div class="normal-select">

									@if(Session::get ( 'service_id' ) == ROAD_FTL)
										@if(isset($vehicle_type_name) && $vehicle_type_name !='')
											{!! Form::text('vehicle_type_name', $vehicle_type_name, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
											@if($_REQUEST['lkp_vehicle_type_id'] != '')
												{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_id'], array('id' => 'lkp_vehicle_type_ids')) !!}
											@elseif(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids'] != '')
												{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_ids'], array('id' => 'lkp_vehicle_type_ids')) !!}
											@endif
										@else
											{!! $filter->field('bqi.lkp_vehicle_type_id') !!}

											@if($_REQUEST['lkp_vehicle_type_id'] != '')
												{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_id'], array('id' => 'lkp_vehicle_type_ids')) !!}
											@elseif(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids'] != '')
												{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_ids'], array('id' => 'lkp_vehicle_type_ids')) !!}
											@endif
										@endif
									@elseif((Session::get ( 'service_id' ) == ROAD_PTL)  || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN))
											@if(isset($packaging_type_name) && $packaging_type_name !='')
												{!! Form::text('packaging_type_name', $packagename, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
												@if($_REQUEST['lkp_packaging_type_id'] != '')
													{!! Form::hidden('lkp_packaging_type_id', $_REQUEST['lkp_packaging_type_id'], array('id' => 'lkp_packaging_type_id')) !!}
												@elseif(isset($_REQUEST['lkp_packaging_type_ids']) && $_REQUEST['lkp_packaging_type_ids'] != '')
													{!! Form::hidden('lkp_packaging_type_id', $_REQUEST['lkp_packaging_type_id'], array('id' => 'lkp_packaging_type_id')) !!}
												@endif
											@else
												{!! $filter->field('bqi.lkp_packaging_type_id') !!}

												@if($_REQUEST['lkp_packaging_type_id'] != '')
													{!! Form::hidden('lkp_packaging_type_id', $_REQUEST['lkp_packaging_type_id'], array('id' => 'lkp_packaging_type_id')) !!}
												@elseif(isset($_REQUEST['lkp_packaging_type_id']) && $_REQUEST['lkp_packaging_type_id'] != '')
													{!! Form::hidden('lkp_packaging_type_id', $_REQUEST['lkp_packaging_type_id'], array('id' => 'lkp_packaging_type_id')) !!}
												@endif
											@endif
										 @if((Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN))
										 
											@if(isset($_REQUEST['lkp_air_ocean_shipment_type_id']) && $_REQUEST['lkp_air_ocean_shipment_type_id'] != '')
											{!! Form::hidden('lkp_air_ocean_shipment_type_id', $_REQUEST['lkp_air_ocean_shipment_type_id'], array('id' => 'lkp_air_ocean_shipment_type_id')) !!}
											@endif
											@if(isset($_REQUEST['lkp_air_ocean_sender_identity_id']) && $_REQUEST['lkp_air_ocean_sender_identity_id'] != '')
									   		{!! Form::hidden('lkp_air_ocean_sender_identity_id', $_REQUEST['lkp_air_ocean_sender_identity_id'], array('id' => 'lkp_air_ocean_sender_identity_id')) !!}
									   		@endif
									   	@endif	
									@endif

									</div>
								</div>
							</div>
						</div>

						<h2 class="filter-head">Bid Type</h2>
						<div class="inner-block-bg">
							<div class="payment-mode">
								{{--*/ $bidopen = ''; /*--}}
								{{--*/ $bidclose = ''; /*--}}

								@if(isset($bid_type_value) && $bid_type_value!='')

								@if(strlen($bid_type_value)>1)
								{{--*/ $bidopen = 'checked' /*--}}
								{{--*/ $bidclose = 'checked' /*--}}

								@else

								@if($bid_type_value==1)
								{{--*/ $bidopen = 'checked' /*--}}
								@endif

								@if($bid_type_value==2)
								{{--*/ $bidclose = 'checked' /*--}}
								@endif

								@endif

								@endif

									<div class="check-box"><input class="bidcheckopen" type="checkbox" name="bid_type" id="bid_type" onclick="javascript:bidTypeChange(1)" {{$bidopen}}><span class="lbl padding-8">Open</span></div>
									<div class="check-box"><input class="bidcheckclose" type="checkbox" name="bid_type" id="bid_type" onclick="javascript:bidTypeChange(2)" {{$bidclose}}><span class="lbl padding-8">Close</span></div>

								</div>
								<input type="hidden" name="bid_type_value" id="bid_type_value" value=""/>
								<div class="clearfix"></div>
								<input type="hidden" name="selected_users" id="selected_users" value="<?php echo isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : ""; ?>"/>
								<input type="hidden" name="courier_types" id="courier_types" value="<?php echo isset($courier_types) ? $courier_types : ""; ?>"/>
								<input type="hidden" name="post_delivery_type" id="post_delivery_type" value="<?php echo isset($courier_delivery_type) ? $courier_delivery_type : ""; ?>"/>
								{!! Form::close() !!}
							</div>
						@endif
					

					<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
					<div class="seller-list inner-block-bg">

						<?php
						
						$selectedSellers = isset($_REQUEST['selected_users']) ? explode(",",$_REQUEST['selected_users']) : array();
						
						?>

						
							@if (Session::has('layered_filter'))
							
								<div class="layered_nav  margin-top col-xs-12 padding-none">
									@if(Session::get('layered_filter'))
										@foreach (Session::get('layered_filter') as $userId => $userName)

											<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
											<div class="check-box">
											
												<input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="search_by_user"/><span class="lbl padding-8">{{ $userName }}</span>
											</div>
										@endforeach
									@endif
								</div>
							@endif
						
					</div>
					<?php
					Session::forget('show_layered_filter');
					?>


				</div>

				<!-- Left Section Ends Here -->


				<!-- Right Section Starts Here -->

				<div class="main-right">

					<!-- Table Starts Here -->

					{!! $grid !!}

							<!-- Table Starts Here -->

				</div>

				<!-- Right Section Ends Here -->

			</div>

		</div>

		<div class="clearfix"></div>
		
	</div>
</div>
</div>
@include('partials.footer')
</div>



<!-- Modal -->
<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">commoncomponent
		
		@if(Session::get('session_spot_or_term') == 2)
			{{--*/ $spot_or_term_selected = "checked" /*--}}
		@else
			{{--*/ $spot_or_term_selected = "" /*--}}
		@endif
		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
				<div class="col-md-12 modal-form">
					@if(Session::get ( 'service_id' ) == ROAD_FTL)
					
					
						<div class="home-search-modfy">

							<div class="col-md-3 text-center">
								<div class="col-md-12 form-control-fld">
									<div class="radio-block">
										<div class="radio_inline">
										<!-- input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /> --> 
										<input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ $spot_or_term_selected }} />
										<label for="spot_lead_type"><span></span>Spot</label>
										</div>
										<div class="radio_inline">
										<input type="radio" name="lead_type" id="term_lead_type"  {{ $spot_or_term_selected }} value="2" /> 
										<label for="term_lead_type"><span></span>Term</label>
										</div>
									</div>


								</div>
							</div>
							@if(Session::get('session_spot_or_term') == 2)
							<div style = 'display:none;' class="showhide_spot" id="showhide_spot">
							@endif
								{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers']) !!}
								
								<div class="home-search-form">

									<div class="clearfix"></div>
									<div class="col-md-12 padding-none">
										<div class="col-md-4 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												<!--input class="form-control" id="" type="text" placeholder="From Location"-->
												{!! Form::text('from_location', '', ['id' => 'from_location','class' => 'top-text-fld form-control clsFTLFromLocation', 'placeholder' => 'From Location*']) !!}
												{!! Form::hidden('from_city_id', '', array('id' => 'from_location_id')) !!}
												{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
												{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
												{!!	Form::hidden('spot_or_term',Session::get('session_spot_or_term'),array('class'=>'form-control spot_or_term')) !!}
											</div>
										</div>
										<div class="col-md-4 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												<!--input class="form-control" id="" type="text" placeholder="To Location"-->
												{!! Form::text('to_location', '', ['id' => 'to_location','class' => 'top-text-fld form-control clsFTLtoLocation', 'placeholder' => 'To Location*']) !!}
												{!! Form::hidden('to_city_id', '', array('id' => 'to_location_id')) !!}
											</div>
										</div>
										<div>
											<div class="col-md-4 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-calendar-o"></i></span>
													<!--input class="form-control" id="" type="text" placeholder="Dispatch Date"-->
													{!! Form::text('dispatch_date', '', ['id' => 'datepicker_search','class' => 'form-control calendar from-date-control','readonly'=>true, 'placeholder' => 'Dispatch Date*']) !!}
													<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
													<!--<input type="checkbox" name="is_flexiable" value="1">-->
												</div>
											</div>
											<div class="col-md-4 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-calendar-o"></i></span>
													<!--input class="form-control" id="" type="text" placeholder="Delivery Date"-->
													{!! Form::text('delivery_date', '', ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly'=>true, 'placeholder' => 'Delivery Date']) !!}
													<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
												</div>
											</div>
											<div class="col-md-4 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-archive"></i></span>
													{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), null, ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
															<!--select class="selectpicker">
										<option value="0">Select Load Type</option>
									</select-->
												</div>
											</div>
											<div class="col-md-4 form-control-fld">

												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-balance-scale"></i></span>
													<!--input class="form-control" id="" type="text" placeholder="Qty"-->
													<!--input type="text" placeholder="Qty" class="form-control"-->
													{!! Form::text('qty', '', ['id' => 'qty','class' => 'form-control fivedigitsthreedecimals_deciVal', 'placeholder' => 'Qty']) !!}

													<span class="add-on unit1">
										<!--input class="form-control" id="" type="text" placeholder="Capacity"-->
														{!!	Form::text('capacity',null,array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}
										</span>
												</div>

											</div>
											<div class="clearfix"></div>
											<div class="col-md-4 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-archive"></i></span>
													{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), null, ['class' => 'selectpicker','id' => 'vechile_type']) !!}
												</div>
											</div>
											<div class="col-md-4 form-control-fld">
												<img src="{{asset('/images/truck.png')}}" class="truck-type" />
												<span class="truck-type-text">Vehicle Dimensions *<span id ='dimension'>{{ $commoncomponent->getVehicleReqCol($lkp_vehicle_type_id, 'dimension') }}</span></span>
											</div>
										</div>
									</div>
								</div>
								<div class="submit_container">
									<div class="col-md-4 col-md-offset-4">
										<!--button class="btn theme-btn btn-block">Get Quote</button-->
										<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
									</div>
								</div>

								<!--<div class="col-md-12 col-sm-12 col-xs-12 padding-top"><a class="pull-right" href="#">Helpdesk</a></div>-->
								{!! Form::close() !!}
							</div>




							@if(Session::get('session_spot_or_term') == 2)

							<div class="showhide_term" id="showhide_term">
							
							@endif
								{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
								<div class="home-search-form">

									<div class="clearfix"></div>
									<div class="col-md-12 padding-none">
										<div class="col-md-4 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												<!--input class="form-control" id="" type="text" placeholder="From Location"-->
												{!! Form::text('term_from_location', Session::get('session_from_location'), ['id' => 'term_from_location','class' => 'top-text-fld form-control alphaonly_strVal', 'placeholder' => 'From Location*']) !!}
												{!! Form::hidden('term_from_city_id', Session::get('session_from_city_id'), array('id' => 'term_from_location_id')) !!}
												{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
												{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
												{!!	Form::hidden('spot_or_term',Session::get('session_spot_or_term'),array('class'=>'form-control spot_or_term')) !!}
												<input type="hidden" name="zone_or_location" id="zone_or_location" value="<?php echo isset($_REQUEST['zone_or_location']) ? $_REQUEST['zone_or_location'] : ""; ?>"/>
											</div>
										</div>
										<div class="col-md-4 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												<!--input class="form-control" id="" type="text" placeholder="To Location"-->
												{!! Form::text('term_to_location', Session::get('session_to_location'), ['id' => 'term_to_location','class' => 'top-text-fld form-control alphaonly_strVal', 'placeholder' => 'To Location*']) !!}
												{!! Form::hidden('term_to_city_id', Session::get('session_to_city_id'), array('id' => 'term_to_location_id')) !!}
											</div>
										</div>


										<div class="col-md-4 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-archive"></i></span>
												{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), Session::get('session_load_type'), ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-md-4 form-control-fld pull-left">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-archive"></i></span>
												{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), Session::get('session_vehicle_type'), ['class' => 'selectpicker','id' => 'vechile_type_1']) !!}
											</div>
										</div>
										<div class="col-md-4 form-control-fld">
											<img src="{{asset('/images/truck.png')}}" class="truck-type" />
											<span class="truck-type-text">Vehicle Dimensions *<span id ='dimension_1'>{{ $commoncomponent->getVehicleReqCol($lkp_vehicle_type_id, 'dimension') }}</span></span>
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
						</div>
	@elseif(Session::get ( 'service_id' ) == RELOCATION_DOMESTIC)
	
                @if(Session::get('session_post_rate_card_type') == 2)
			{{--*/ $card_type = "checked" /*--}}
		@else
			{{--*/ $card_type = "" /*--}}
		@endif
		
		@if(Session::get('session_post_rate_card_type') == 1)
			{{--*/ $card_type_hhg = "checked" /*--}}
		@else
			{{--*/ $card_type_hhg = "" /*--}}
		@endif
	
		<div class="home-search-modfy">
		<!-- Left Nav Starts Here -->
		<div class="col-md-12 padding-none">
			<div class="col-md-3 padding-none text-center">
				<div class="col-md-12 form-control-fld">

					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ $spot_or_term_selected }} /><label for="spot_lead_type"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" {{ $spot_or_term_selected }} /><label for="term_lead_type"> <span></span>Term</label></div>
					</div>

				</div>
			</div>
		</div>
		<div class="clearfix"></div>

		{{-- Seller/Home/Transportation/FTL/Search/Spot  --}}
		<div class="showhide_spot" id="showhide_spot" style="display:none">

			{!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_domestic_sellersearch_buyers','method'=>'get']) !!}
				<div class="col-md-12 padding-none">
					<div class="col-md-12 padding-none">
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
							</div>
						</div>
						<div class="clearfix"></div>
						{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
						{!!	Form::hidden('spot_or_term',Session::get('session_spot_or_term'),array('class'=>'form-control')) !!}
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_to', '' , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date*']) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span>
								<select class="selectpicker" id="post_type" name="post_type">
									<option value="">Post Type</option>
									<option value="1">HHG</option>
									<option value="2">Vehicle</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-md-offset-4">
					<button class="btn theme-btn btn-block">Search</button>
				</div>
			{!! Form::close() !!}
		</div>

		{{-- Seller/Home/Transportation/FTL/Search/Term  --}}
		<div class="showhide_term" id="showhide_term">
			{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
			<div class="col-md-12 padding-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">

								<input type="radio" id="term_post_rate_card_type_1" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1" {{ $card_type_hhg }} />
								<label for="term_post_rate_card_type_1"><span></span>HHG</label>

								<input type="radio" id="term_post_rate_card_type_2" name="term_post_rate_card_type" class="termratetype_selection_buyer" {{ $card_type }} value="2">
								<label for="term_post_rate_card_type_2"><span></span>Vehicle</label>
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								{!! Form::text('term_from_location', $from_location, ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', $from_city_id, array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
								{!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!! Form::hidden('spot_or_term',2,array('class'=>'form-control spot_or_term')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="To Location"-->
								{!! Form::text('term_to_location', $to_location, ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', $to_city_id, array('id' => 'term_to_location_id')) !!}
							</div>
						</div>

					</div>
				</div>
			</div>


			<div class="submit_container">
				<div class="col-md-4 col-md-offset-4">
					<!--button class="btn theme-btn btn-block">Get Quote</button-->
					<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
				</div>
			</div>
			<!--<div class="col-md-12 col-sm-12 col-xs-12 padding-top"><a class="pull-right" href="#">Helpdesk</a></div>-->
			{!! Form::close() !!}
		</div>

		<div class="clearfix"></div>



	</div>
        @elseif(Session::get ( 'service_id' )==RELOCATION_INTERNATIONAL)        
        
                <div class="col-md-12 padding-none">
                    <div class="col-md-3 padding-none text-center">
                        <div class="col-md-12 form-control-fld">
                            <div class="radio-block">
                                    <div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ $spot_or_term_selected }} /><label for="spot_lead_type"><span></span>Spot</label></div>
                                    <div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" {{ $spot_or_term_selected }} /><label for="term_lead_type"> <span></span>Term</label></div>
                            </div>
                        </div>
                    </div>
		</div>
        
                {{-- Seller/Home/Relocation/International/Search/Spot/  --}}
		<div class="showhide_spot" id="showhide_spot" style="display:none">
                    {!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_international_sellersearch_buyers_spot','method'=>'get']) !!}
                        {!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
			<div class="">
				<div class="home-search-form">
					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld radio-devider">
                                    <div class="radio-block">
                                    @if(isset($request['service_type']) && $request['service_type'] == '1') 
                                         {{--*/ $air_selected = true /*--}}
                                         {{--*/ $ocean_selected = false /*--}}
                                    @else
                                         {{--*/ $air_selected = false /*--}}
                                         {{--*/ $ocean_selected = true /*--}}
                                    @endif



                                         {!! Form::radio('service_type', '1', $air_selected, ['id' => 'spot_service_air']) !!}
                                         <label for="spot_service_air"><span></span>Air</label>

                                         {!! Form::radio('service_type', '2', $ocean_selected, ['id' => 'spot_service_ocean']) !!}
                                         <label for="spot_service_ocean"><span></span>Ocean</label>
                                    </div>
                               </div>
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

						<!--	
							{{-- Seller/Home/Relocation/International/Search/Spot/Air  --}}
								<div class="show_spot_air" id="show_spot_air">
			                    	    {!! Form::hidden('service_type', '1', array('id' => 'post_type')) !!}
											@include('relocationint.airint.sellers.seller_search_buyers')
								</div>	
							{{-- Seller/Home/Relocation/International/Search/Spot/Ocean  --}}
								<div class="show_spot_ocean" id="show_spot_ocean" style="display:none">
									<div class="clearfix"></div>
											@include('relocationint.ocean.sellers.seller_search_buyers')
								</div>	
						-->		
					</div>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-4">
				<button class="btn theme-btn btn-block">Search</button>
			</div>
			{!! Form::close() !!}
		</div>
		<div class="clearfix"></div>
		{{-- Seller/Home/Relocation/International/Search/Term/  --}}
		<div class="showhide_term" id="showhide_term" >
               {!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'term_relocint_air_ocean']) !!}
			{!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
			<div class="">
				<div class="home-search-form">
					<div class="clearfix"></div>
					<div class="col-md-12 padding-none radio-devider">
						<div class="col-md-12 form-control-fld">
                                    <div class="radio-block">
                                    @if(isset($request['service_type']) && $request['service_type'] == '1') 
                                         {{--*/ $air_selected = true /*--}}
                                         {{--*/ $ocean_selected = false /*--}}
                                    @else
                                         {{--*/ $air_selected = false /*--}}
                                         {{--*/ $ocean_selected = true /*--}}
                                    @endif



                                         {!! Form::radio('term_service_type', '1', $air_selected, ['id' => 'term_air']) !!}
                                         <label for="term_air"><span></span>Air</label>

                                         {!! Form::radio('term_service_type', '2', $ocean_selected, ['id' => 'term_ocean']) !!}
                                         <label for="term_ocean"><span></span>Ocean</label>
                                    </div>
                               </div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_from_location', $from_location , ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', $from_city_id , array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '' , array('id' => 'seller_district_id')) !!}
								{!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!! Form::hidden('spot_or_term',2,array('class'=>'form-control')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_to_location', $to_location , ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', $to_city_id , array('id' => 'term_to_location_id')) !!}
							</div>
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
		</div>
                
                
	@elseif((Session::get ( 'service_id' ) == ROAD_PTL)  || (Session::get ( 'service_id' )==RAIL) || (Session::get ( 'service_id' )==AIR_DOMESTIC)|| (Session::get ( 'service_id' )==AIR_INTERNATIONAL)|| (Session::get ( 'service_id' )==OCEAN) || (Session::get ( 'service_id' )==COURIER))
	<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
						<div class="home-search-modfy">
							<div class="col-md-12 padding-none">
								<div class="col-md-12 form-control-fld">
									<div class="radio-block">
										<div class="radio_inline">
										<input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ $spot_or_term_selected }} /> 
										<label for="spot_lead_type"><span></span>Spot</label>
										</div>
										<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" {{ $spot_or_term_selected }} />
										<label for="term_lead_type"><span></span>Term</label>
										</div>
									</div>


								</div>
							</div>
							<div class="clearfix"></div>
							@if(Session::get('session_spot_or_term') == 2)
							<div style = 'display:none;' class="showhide_spot" id="showhide_spot">
							@endif
							
								{!! Form::open(['url' =>'buyersearchresults','method'=>'POST','id'=>'sellers-posts-buyers-ptl']) !!}
								@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN && Session::get('service_id') != COURIER)
								@if(Session::get('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '1') 
									{{--*/ $zone_selected = true /*--}}
								@else
									{{--*/ $zone_selected = false /*--}}
								@endif

								@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '2') 
									{{--*/ $location_selected = true /*--}}
								@else
									{{--*/ $location_selected = false /*--}}
								@endif
									<div class="col-md-12 text-center padding-none">
										<div class="col-md-12 form-control-fld">
											<div class="radio-block">
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'zone_wise_ptl']) !!}
													<label for="zone_wise_ptl"><span></span>Zone wise</label>
												</div>
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'location_wise_ptl']) !!}

													<label for="location_wise_ptl"><span></span>Location wise</label>
													{!! Form::hidden('zone_or_location', Session::get('zone_or_location_ptl'), array('id' => 'zone_or_location')) !!}
												</div>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
								@endif
								
								@if(Session::get('service_id') == COURIER)
								
								<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
								   </div>
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
									{!! Form::hidden('zone_or_location', 1, array('id' => 'zone_or_location')) !!}
									</div>
								</div>
							</div>
								
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
									{!! Form::hidden('post_or_delivery_type', 1, array('id' => 'post_delivery_type')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Documents', true, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
									{!! Form::hidden('courier_or_types', 1, array('id' => 'courier_types')) !!}
									</div>
								</div>
							</div>
								<div class="clearfix"></div>
							@endif
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										{!! Form::text('dispatch_date', '', ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control', 'readonly'=>true,'placeholder' => 'Dispatch Date*']) !!}
										<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										{!! Form::text('delivery_date', '', ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control', 'readonly'=>true,'placeholder' => 'Delivery Date']) !!}
										<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
									</div>
								</div>


								@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('from_location', '', ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From Location with pin code / Zone']) !!}
											{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('to_location', '', ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To Location with pin code / Zone']) !!}
											{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
										</div>
									</div>
								@else
									@if(Session::get('service_id') == AIR_INTERNATIONAL)
										{{--*/ $plceholder = 'Airports' /*--}}
									@elseif(Session::get('service_id') == OCEAN)
										{{--*/ $plceholder = 'Ocean' /*--}}
									@else
										{{--*/ $plceholder = '' /*--}}
									@endif

									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('from_location', '', ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
											{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('to_location', '', ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
											{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
										</div>
									</div>
								@endif
								@if(Session::get ( 'service_id' )!=COURIER)
								<div class="clearfix"></div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), null, ['class' => 'selectpicker bs-select-hidden','id' => 'spot_load_type','onChange'=>'return GetCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters), null, ['class' => 'selectpicker bs-select-hidden','id' => 'spot_package_type']) !!}
									</div>
								</div>
								@endif



								@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)


									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
											{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
											{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
										</div>
									</div>


								@endif
								<div class="submit_container">
									<div class="col-md-4 col-md-offset-4">
										<!--button class="btn theme-btn btn-block">Get Quote</button-->
										<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
									</div>
								</div>

								{!! Form::close() !!}

							</div>



							@if(Session::get('session_spot_or_term') == 2)

							<div class="showhide_term" id="showhide_term">
							
							@endif
							
								{!! Form::open(['url' =>'termsellersearchresults','method'=>'POST','id'=>'sellers-search-buyers-ptl']) !!}
								@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
								@if(Session::get('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '1') 
									{{--*/ $zone_selected = true /*--}}
								@else
									{{--*/ $zone_selected = false /*--}}
								@endif

								@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '2') 
									{{--*/ $location_selected = true /*--}}
								@else
									{{--*/ $location_selected = false /*--}}
								@endif
									@if(Session::get ( 'service_id' )!=COURIER)
									<div class="col-md-12 text-center padding-none">
										<div class="col-md-12 form-control-fld">
											<div class="radio-block">
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'term_zone_wise_ptl']) !!}
													<label for="term_zone_wise_ptl"><span></span>Zone wise</label>
												</div>
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'term_location_wise_ptl']) !!}

													<label for="term_location_wise_ptl"><span></span>Location wise</label>
													{!! Form::hidden('zone_or_location', Session::get('zone_or_location_ptl'), array('id' => 'term_zone_or_location')) !!}
												</div>
											</div>
										</div>
									</div>
									@else
									
									
									@if(Session::has('session_courier_delivery_type') && Session::get('session_courier_delivery_type') == '1') 
									{{--*/ $domestic_selected = true /*--}}
									{{--*/ $domestic_international_selected = 1 /*--}}
								@else
									{{--*/ $domestic_selected = false /*--}}
								@endif
								
								@if(Session::has('session_courier_delivery_type') && Session::get('session_courier_delivery_type') == '2') 
									{{--*/ $international_selected = true /*--}}
									{{--*/ $domestic_international_selected = 2 /*--}}
								@else
									{{--*/ $international_selected = false /*--}}
								@endif
								
								@if(Session::has('session_courier_types') && Session::get('session_courier_types') == '1')
									{{--*/ $document_selected = true /*--}}
									{{--*/ $document_parcel_selected = 1 /*--}}
								@else
									{{--*/ $document_selected = false /*--}}
								@endif
								
								@if(Session::has('session_courier_types') && Session::get('session_courier_types') == '2') 
									{{--*/ $parcel_selected = true /*--}}
									{{--*/ $document_parcel_selected = 2 /*--}}
								@else
									{{--*/ $parcel_selected = false /*--}}
								@endif
									
									
									
									<div class="col-md-12 text-center padding-none">
										<div class="col-md-4 form-control-fld">
											<div class="radio-block">
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'term_zone_wise_ptl']) !!}
													<label for="term_zone_wise_ptl"><span></span>Zone wise</label>
												</div>
												<div class="radio_inline">
													{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'term_location_wise_ptl']) !!}

													<label for="term_location_wise_ptl"><span></span>Location wise</label>
													{!! Form::hidden('zone_or_location', Session::get('zone_or_location_ptl'), array('id' => 'term_zone_or_location')) !!}
												</div>
											</div>
										</div>
										<div class="col-md-4 form-control-fld">
											<div class="radio-block">
												<div class="radio_inline">
												{!! Form::radio('post_delivery_type', 'Domestic', $domestic_selected, ['id' => 'term_domestic']) !!}
												<label for="term_domestic"><span></span>Domestic</label>
												</div>
												<div class="radio_inline">
												{!! Form::radio('post_delivery_type', 'International', $international_selected, ['id' => 'term_international']) !!} 
												<label for="term_international"><span></span>International</label>
												{!! Form::hidden('post_or_delivery_type',$domestic_international_selected, array('id' => 'term_post_delivery_type')) !!}
												</div>
											</div>
										</div>
										<div class="col-md-4 form-control-fld">
											<div class="radio-block">
												<div class="radio_inline">
												{!! Form::radio('courier_types', 'Documents', $document_selected, ['id' => 'term_documents']) !!} <label for="term_documents"><span></span>Documents</label>
												</div>
												<div class="radio_inline">
												{!! Form::radio('courier_types', 'Parcel',$parcel_selected, ['id' => 'term_parcel']) !!} <label for="term_parcel"><span></span>Parcel</label>
												{!! Form::hidden('courier_or_types', $document_parcel_selected, array('id' => 'term_courier_types')) !!}
												</div>
											</div>
										</div>
									</div>
									@endif
									<div class="clearfix"></div>
								@endif



								@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('term_from_location', Session::get('session_from_location'), ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From Zone']) !!}
											{!! Form::hidden('term_from_location_id', Session::get('session_from_city_id'), array('id' => 'term_from_location1_id')) !!}
											{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
											{!!	Form::hidden('spot_or_term',Session::get('session_spot_or_term'),array('class'=>'form-control spot_or_term')) !!}
											
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('term_to_location', Session::get('session_to_location'), ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To Zone']) !!}
											{!! Form::hidden('term_to_location_id', Session::get('session_to_city_id'), array('id' => 'term_to_location_id')) !!}
										</div>
									</div>
								@else
									@if(Session::get('service_id') == AIR_INTERNATIONAL)
										{{--*/ $plceholder = 'Airports' /*--}}
									@elseif(Session::get('service_id') == OCEAN)
										{{--*/ $plceholder = 'Ocean' /*--}}
									@else
										{{--*/ $plceholder = '' /*--}}
									@endif

									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('term_from_location', Session::get('session_from_location'), ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
											{!! Form::hidden('term_from_location_id',Session::get('session_from_city_id'), array('id' => 'term_from_location1_id')) !!}
											{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
											{!!	Form::hidden('spot_or_term',Session::get('session_spot_or_term'),array('class'=>'form-control spot_or_term')) !!}
											
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('term_to_location', Session::get('session_to_location'), ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
											{!! Form::hidden('term_to_location_id', Session::get('session_to_city_id'), array('id' => 'term_to_location_id')) !!}
										</div>
									</div>
								@endif
								@if(Session::get ( 'service_id' )!=COURIER)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend normal-select">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), Session::get('session_load_type'), ['class' => 'selectpicker bs-select-hidden','id' => 'term_load_type','onChange'=>'return GetCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend normal-select">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters), Session::get('session_vehicle_type'), ['class' => 'selectpicker bs-select-hidden','id' => 'term_package_type']) !!}
									</div>
								</div>
								@endif



								@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)


									<div class="col-md-3 form-control-fld">
										<div class="input-prepend normal-select">
											<span class="add-on"><i class="fa fa-archive"></i></span>
											{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), Session::get('session_shipment_type'), ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend normal-select">
											<span class="add-on"><i class="fa fa-archive"></i></span>
											{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), Session::get('session_sender_identity'), ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
										</div>
									</div>


								@endif
								<div class="submit_container">
									<div class="col-md-4 col-md-offset-4">
										<!--button class="btn theme-btn btn-block">Get Quote</button-->
										<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
									</div>
								</div>

								{!! Form::close() !!}

							</div>



						</div>
					@endif
				</div>
				@if(Session::get ( 'service_id' ) == RELOCATION_GLOBAL_MOBILITY)
					<!-- Search Form Partial -->
					@include('relocationglobal.sellers._searchform')
				@endif
			</div>
		</div>
	</div>
</div>
@endsection