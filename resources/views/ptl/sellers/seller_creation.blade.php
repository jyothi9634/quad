@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- This code for popu-late the zone or location  and courier delivery type and courier type,what the user enter in serch form,if the user navigate from seller search resuls page starts-->
@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '1') 
	{{--*/ $zone_selected = true /*--}}
@else
	{{--*/ $zone_selected = false /*--}}
@endif

@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '2') 
	{{--*/ $location_selected = true /*--}}
@else
	{{--*/ $location_selected = false /*--}}
@endif

@if(Session::has('post_or_delivery_type_ptl') && Session::get('post_or_delivery_type_ptl') == '1') 
	{{--*/ $domestic_selected = true /*--}}
	{{--*/ $domestic_international_selected = 1 /*--}}
@else
	{{--*/ $domestic_selected = false /*--}}
@endif

@if(Session::has('post_or_delivery_type_ptl') && Session::get('post_or_delivery_type_ptl') == '2') 
	{{--*/ $international_selected = true /*--}}
	{{--*/ $domestic_international_selected = 2 /*--}}
@else
	{{--*/ $international_selected = false /*--}}
@endif

@if(Session::has('courier_or_types_ptl') && Session::get('courier_or_types_ptl') == '1')
	{{--*/ $document_selected = true /*--}}
	{{--*/ $document_parcel_selected = 1 /*--}}
@else
	{{--*/ $document_selected = false /*--}}
@endif

@if(Session::has('courier_or_types_ptl') && Session::get('courier_or_types_ptl') == '2') 
	{{--*/ $parcel_selected = true /*--}}
	{{--*/ $document_parcel_selected = 2 /*--}}
@else
	{{--*/ $parcel_selected = false /*--}}
@endif

{{--*/ $serviceId = Session::get('service_id') /*--}}


<!-- This code for popu-late the zone or location  and courier delivery type and courier type ,what the user enter in serch form,if the user navigate from seller search resuls page ends-->

<div class="main">
			<div class="container">
				<span class="pull-left"><h1 class="page-title">Post 
				@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
				@elseif(Session::get('service_id') == RAIL)
				(RAIL)
				@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
				@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
				@elseif(Session::get('service_id') == COURIER)
				(COURIER)
				@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
				@endif
				
				</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
				@if ($url_search_search == 'buyersearchresults')
				<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
 				@endif
				<div class="clearfix"></div>

				<div class="col-md-12 inner-block-bg single-layout padding-none">
					<div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">

					{!! Form::open(['url' => 'sellerpostcreation','id'=>'posts-form-lines_ptl']) !!}
					<div class="col-md-12 padding-none inner-form">
						<div class="col-md-12 padding-none">
							<div class="col-md-12 form-control-fld">
								<div class="padding-top1">
									<span class="data-head">Post Type : Spot</span>
									<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
								</div>
							</div>
							<div class="col-md-12 form-control-fld">
							@if(Session::get('service_id') != COURIER)
								{!! Form::hidden('post_or_delivery_type', '1', array('id' => 'post_delivery_type')) !!}
							@endif
							@if(Session::get('service_id') == COURIER)
								<div class="col-md-3 form-control-fld">
										<div class="radio-block">
										@if ($session_search_values_create[1] != '')
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
										   </div>
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
											{!! Form::hidden('zone_or_location', $session_search_values_create[8], array('id' => 'zone_or_location')) !!}
											</div>
										@else	
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
										   </div>
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
											{!! Form::hidden('zone_or_location', '1', array('id' => 'zone_or_location')) !!}
											</div>
										@endif	
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="radio-block">
										@if ($session_search_values_create[1] != '')
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', $domestic_selected, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', $international_selected, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
											{!! Form::hidden('post_or_delivery_type',$domestic_international_selected, array('id' => 'post_delivery_type')) !!}
											</div>
										@else	
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
											{!! Form::hidden('post_or_delivery_type', '1', array('id' => 'post_delivery_type')) !!}
											</div>
										@endif
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="radio-block">
										@if ($session_search_values_create[1] != '')
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', $document_selected, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', $parcel_selected, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types',$document_parcel_selected, array('id' => 'courier_types')) !!}
											</div>
										@else	
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', true, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types', '1', array('id' => 'courier_types')) !!}
											</div>
										@endif
										</div>
									</div>
							@else
								<div class="radio-block">
						@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
									@if ($session_search_values_create[1] != '')
									<div class="radio_inline">									
									{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
									</div>
									<div class="radio_inline">									
									{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
									{!! Form::hidden('zone_or_location', $session_search_values_create[8], array('id' => 'zone_or_location')) !!}
									</div>
									@else
									<div class="radio_inline">									
									{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
									</div>
									<div class="radio_inline">									
									{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
									{!! Form::hidden('zone_or_location', '1', array('id' => 'zone_or_location')) !!}
									</div>
									@endif
					    @endif
								</div>
								@endif
							</div>
						</div>

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('valid_from', $session_search_values_create[1], ['id' => 'datepicker','readonly' => true,'class' => 'calendar form-control from-date-control', 'placeholder' => 'Valid From*']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('valid_to', $session_search_values_create[0], ['id' => 'datepicker_to_location','readonly' => true,'class' => 'calendar form-control to-date-control', 'placeholder' => 'Valid To*']) !!}
								</div>
							</div>

							<div class="clearfix"></div>
				@if(Session::get('service_id') == AIR_INTERNATIONAL)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Airport*']) !!}
                                    {!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_airport_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_location',$session_search_values_create[7], ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Airport*']) !!}
                                    {!! Form::hidden('to_location_id', $session_search_values_create[5], array('id' => 'to_airport_id')) !!}
								</div>
							</div>
				@elseif(Session::get('service_id') == OCEAN)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_occean','class' => 'form-control', 'placeholder' => 'From Ocean*']) !!}
                                    {!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_occean_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_location',$session_search_values_create[7], ['id' => 'to_occean','class' => 'form-control', 'placeholder' => 'To Ocean*']) !!}
                                    {!! Form::hidden('to_location_id', $session_search_values_create[5], array('id' => 'to_occean_id')) !!}</div>
							</div>
				@else		
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_location_ptl','class' => 'form-control alphanumeric_withSpace', 'placeholder' => 'From Zone*','maxlength'=>'10']) !!}
                                    {!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_location_id')) !!}
									
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_location', $session_search_values_create[7], ['id' => 'to_location_ptl','class' => 'form-control alphanumeric_withSpace', 'placeholder' => 'To Zone*','maxlength'=>'10']) !!}
                                    {!! Form::hidden('to_location_id', $session_search_values_create[5], array('id' => 'to_location_id')) !!}
								</div>
							</div>
				@endif
					@if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == COURIER )
					
							<div class="col-md-3 form-control-fld padding-left-none">
								<div class="col-md-8 padding-none">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
											{!! Form::text('transitdays',null,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays_ptl','placeholder'=>'Transit Days*']) !!}
									</div>	
								</div>
								<div class="col-md-4 padding-none">
									<div class="input-prepend">
									<span class="add-on unit-days manage">
											<div class="normal-select">
												{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_ptl', 'data-serviceId' => $serviceId]) !!}	
											</div>
										</span>
									</div>
								</div>
							</div>
							
						@else	
							<div class="col-md-3 form-control-fld padding-left-none">
								<div class="col-md-8 padding-none">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
										{!! Form::text('transitdays',null,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays_ptl','placeholder'=>'Transit Days*']) !!}
										
									</div>	
								</div>
								<div class="col-md-4 padding-none">
									<div class="input-prepend">
									<span class="add-on unit-days manage">
											<div class="normal-select">
											{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_ptl','data-serviceId' => $serviceId]) !!}	
											</div>
										</span>
									</div>
								</div>
								
							</div>
						@endif	
							@if(Session::get('service_id') != COURIER)
							<div class="col-md-2 padding-none">
								<div class="col-md-12 form-control-fld padding-left-none">
									<div class="input-prepend">
									<span class="add-on"><i class="fa fa-rupee "></i></span>
									{!! Form::text('price',null,['class'=>'form-control numberVal fourdigitstwodecimals_deciVal','id'=>'price_ptl','placeholder'=>'Rate per kg (Rs)*']) !!}
								</div>	
								</div>
								
							</div>
							@endif

							
							<div class="col-md-1 form-control-fld padding-left-none">
								<input type="button" id="add_more_ptl" value="Add" class="btn add-btn">
							</div>
						</div>
					</div>
					{!!	Form::hidden('update_ptl_seller_line',0,array('class'=>'','id'=>'update_ptl_seller_line'))!!}
				    {!!	Form::hidden('update_ptl_seller_row_count','',array('class'=>'','id'=>'update_ptl_seller_row_count'))!!}
 					{!! Form::close() !!}
 					
 					 <div class="clearfix"></div>
 					 
 					 
					{!! Form::open(['url' => 'sellerpostcreation','id'=>'posts-form_ptl']) !!}
                    {!! Form::hidden('labeltext[]', 'Cancellation Charges', array('id' => '')) !!}
                    {!! Form::hidden('labeltext[]', 'Other Charges', array('id' => '')) !!}
                    {!! Form::hidden('post_type_id', '', array('id' => 'post_type_id')) !!}
                    @if(Session::get('service_id') == COURIER)
                    {!! Form::hidden('post_or_delivery_type_id', '', array('id' => 'post_or_delivery_type_id')) !!}
                    {!! Form::hidden('courier_or_types_id', '', array('id' => 'courier_or_types_id')) !!}
                    @endif
                    {!! Form::hidden('valid_from_val', '', array('id' => 'valid_from_val')) !!}
                    {!! Form::hidden('valid_to_val', '', array('id' => 'valid_to_val')) !!}
                    {!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
                    {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                    {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                    {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                    <input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='0'>
                    <input type="hidden" name ='price_slap_hidden_value' id='price_slap_hidden_value' value='0'>
					
					<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">

						<div class="col-md-12 form-control-fld margin-none">
							<div class="main-inner"> 
							

							<!-- Right Section Starts Here -->

							<div class="main-right">

								<!-- Table Starts Here -->

								<div class="table-div table-style1 padding-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">From<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">To<i class="fa fa-caret-down"></i></div>
										@if(Session::get('service_id') != COURIER)
										<div class="col-md-3 padding-left-none">Rate Per KG<i class="fa fa-caret-down"></i></div>
										@endif
										<div class="col-md-2 padding-left-none">Transit Days<i class="fa fa-caret-down"></i></div>
										<div class="col-md-1 padding-left-none"></div>
									</div>
									<input type="hidden" id='next_add_more_id_ptl' value='0'>
									<input type="hidden" id='update_ptl_remove_count' name='update_ptl_remove_count' value='0'>
									<!-- Table Head Ends Here -->
									<div class="table-data">
										<!-- Table Row Starts Here -->
										<div id ="ptl_multi-line-itemes" class="multi-line-itemes">
											<div class="table-data request_rows_ptl" id=""></div>
										</div>
										<!-- Table Row Ends Here -->
									</div>
								</div>	

								<!-- Table Starts Here -->

							</div>

							<!-- Right Section Ends Here -->

						</div>
					</div>
				</div>	
					
				<div class="col-md-12 inner-block-bg inner-block-bg1">
				@if(Session::get('service_id') == COURIER)
<!-- 				<h2 class="filter-head1">Courier</h2> -->
				<div class="col-md-12 padding-none">
				
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('conversion_factor_text',null,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'conversion_factor','placeholder'=>'Conversion Factor (CCM/KG)*']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld padding-right-none">
								<div class="col-md-8 padding-none">
									{!! Form::text('max_weight_accepted_text',null,['class'=>'form-control form-control1 clsIDmax_weight_accepted clsCOURMaxWeightGms','id'=>'max_weight_accepted','placeholder'=>'Maximum Weight Accepted*']) !!}
								</div>
							
							<div class="col-md-4 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days manage">
											{!! Form::select('units_max_weight',($volumeWeightTypes), null, ['id' => 'units_max_weight','class' => 'selectpicker clsSelMaxwgtAptType bs-select-hidden']) !!}
										</span>
									</div>	
								</div>
								</div>
								<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>
										<div class="col-md-3 padding-left-none">Price</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none slabtable price-slap-add">

										<!-- Table Row Starts Here -->

										<div class="add-price-slap table-row inner-block-bg">
										 <div class="price-slap">
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													<input type="text" class="form-control form-control1" id = 'low_price' readonly value ='0.00' name = 'low_price' placeholder="0.00" />
												</div>
											</div>
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													<input type="text" class="form-control form-control1 clsROMMinKm" id = 'high_price' name = 'high_price' placeholder="0.00" />
												</div>
											</div>
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													<input type="text" class="form-control form-control1 fivedigitstwodecimals_deciVal numberVal" id = 'actual_price' name = 'actual_price' placeholder="0.00" />
												</div>
											</div>
											<div class="col-md-1 form-control-fld padding-left-none">
											<input type="button" class="btn add-box add-btn" value="Add">
											</div>
										</div>
										
										</div>

										<!-- Table Row Ends Here -->

									</div>
									<div class="col-md-5 form-control-fld padding-none">
										<input type="hidden" value="0" id ='check_max_weight_assign' name="check_max_weight_assign">
										<div class="col-md-1 form-control-fld">
										<div class="checkbox_inline padding-top-8">
										{!! Form::checkbox('check_max_weight', 1, false,array('id'=>'check_max_weight','disabled'=>'disabled')) !!}
										<span class="lbl padding-8"></span></div>
										</div>

										<div class="col-md-5 form-control-fld padding-none">
										{!! Form::text('incremental_weight_text',null,['class'=>'form-control form-control1 numberVal','id'=>'incremental_weight','placeholder'=>'Incremental Weight*','readonly']) !!}
										</div>	
										<div class="col-md-5 form-control-fld">
										{!! Form::text('rate_per_increment_text',null,['class'=>'form-control form-control1 fourdigitstwodecimals_deciVal numberVal','id'=>'rate_per_increment','placeholder'=>'Rate Per Increment*','readonly']) !!}
										</div>
									</div>	
								</div>	

								<!-- Table Starts Here -->
							</div>
							<div class="clearfix"></div>
							<h5 class="caption-head">Additional Charges</h5>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">	
									{!! Form::text('fuel_surcharge_text',null,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'fuel_surcharge','placeholder'=>'Fuel Surcharge %*']) !!}
								</div>
							</div>	
						
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('check_on_delivery_text',null,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'check_on_delivery','placeholder'=>'Check on Delivery %*']) !!}
								</div>
							</div>	
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('freight_collect_text',null,['class'=>'form-control form-control1 fivedigitstwodecimals_deciVal numberVal','id'=>'freight_collect','placeholder'=>'Freight Collect*']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('arc_text',null,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'arc','placeholder'=>'ARC %*']) !!}
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('maximum_value_text',null,['class'=>'form-control form-control1 fivedigitstwodecimals_deciVal numberVal','id'=>'maximum_value','placeholder'=>'Maximum Value*']) !!}
								</div>
							</div>
				
					</div>
				
				@else
				
					<div class="col-md-12 padding-none">
						<div class="col-md-3 padding-none">
						<h5 class="caption-head"></h5>
						<div class="col-md-12 form-control-fld">
							<div class="input-prepend">						
								@if(Session::get('service_id') == AIR_DOMESTIC )
	                            {!! Form::text('kgpercft',null,['class'=>'form-control form-control1 numberVal clsKGperCCM','id'=>'kgpercft_ptl','placeholder'=>'Kg per CCM*']) !!}
	                            @elseif(Session::get('service_id') == AIR_INTERNATIONAL )
	                            {!! Form::text('kgpercft',null,['class'=>'form-control form-control1 numberVal clsKGperCCM','id'=>'kgpercft_ptl','placeholder'=>'Kg per CCM*']) !!}
	                            @elseif(Session::get('service_id') == OCEAN)
	                                    {!! Form::text('kgpercft',null,['class'=>'form-control form-control1 numberVal fourdigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CBM*']) !!}
	                            @elseif(Session::get('service_id') == RAIL)	
	                            {!! Form::text('kgpercft',null,['class'=>'form-control form-control1  clsRailKGpCFT','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*']) !!}
	                            @else
	                            <!--h5 class="caption-head"></h5-->
	                             {!! Form::text('kgpercft',null,['class'=>'form-control form-control1 numberVal fourdigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*']) !!}	
                            @endif
							</div>	
						</div>
						</div>
						@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
						
						<div class="col-md-9 padding-none">
							<h5 class="caption-head">Additional Charges</h5>
							<div class="col-md-3 form-control-fld">
							<div class="input-prepend">	
								{!! Form::text('pickup',null,['class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'pickup_ptl','placeholder'=>'Pick Up*']) !!}
							</div>	
							</div>
							<div class="col-md-3 form-control-fld">
							<div class="input-prepend">	
								{!! Form::text('delivery',null,['class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'delivery_ptl','placeholder'=>'Delivery*']) !!}
							</div>
							</div>
							<div class="col-md-3 form-control-fld">
							<div class="input-prepend">	
								{!! Form::text('oda',null,['class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'oda_ptl','placeholder'=>'ODA*']) !!}
							</div>
							</div>
						</div>
						@endif	
					</div>
				@endif
					<div class="clearfix"></div>

					<div class="col-md-3 form-control-fld">
						<div class="normal-select">
                                                    @if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)
							{!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_ptl','class' => 'selectpicker']) !!}
                                                    @else
                                                        {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_ptl','class' => 'selectpicker']) !!}
                                                    @endif
						</div>
					</div>


					<div class="clearfix"></div>


					<h2 class="filter-head1">Payment Terms</h2>

					<div class="col-md-3 form-control-fld">
						<div class="normal-select">
							{!! Form::select('paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
						</div>
					</div>

					<div class="col-md-12 form-control-fld" id = 'show_advanced_period'>
						<div class="check-block">
							<div class="checkbox_inline">
							{!! Form::checkbox('accept_payment_ptl[]', 1, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">NEFT/RTGS</span>
							</div>
							<div class="checkbox_inline">
							{!! Form::checkbox('accept_payment_ptl[]', 2, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Credit Card</span>
							</div>
							<div class="checkbox_inline">
							{!! Form::checkbox('accept_payment_ptl[]', 3, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Debit Card</span>
							</div>
						</div>
					</div>

					<div class="col-md-12 form-control-fld" style ='display: none;' id = 'show_credit_period'>
						<div class="col-md-3 form-control-fld padding-left-none">
						
						<div class="col-md-7 padding-none">
							<div class="input-prepend">
							{!! Form::text('credit_period_ptl',null,['class'=>'form-control form-control1 clsIDCredit_period clsCreditPeriod','placeholder'=>'Credit Period']) !!}
						</div>
						</div>
						<div class="col-md-5 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days manage">
											<div class="normal-select">
												{!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden']) !!}		
											</div>
										</span>
							</div>
						</div>
						
						
						</div>
						<div class="col-md-12 padding-none">
							<div class="checkbox_inline">
							
							{!! Form::checkbox('accept_credit_netbanking[]', 1, false) !!} <span class="lbl padding-8">Net Banking</span>
							</div>
							<div class="checkbox_inline">
							{!! Form::checkbox('accept_credit_netbanking[]', 2, false) !!} <span class="lbl padding-8">Cheque / DD</span>
							</div>

						</div>
					</div>
				</div>
				
				
				
					<div class="col-md-12 inner-block-bg inner-block-bg1">

                    <h2 class="filter-head1">Additional Charges</h2>
					<div class="form-control-fld terms-and-conditions-block">
                        <div class="col-md-3 padding-none tc-block-fld"><div class="input-prepend"><input type="text" name="terms_condtion_types1"  class="form-control form-control1 numberVal fourdigitstwodecimals_deciVal" placeholder ='Cancellation Charges' id="cancellation1" /><span class="add-on unit">Rs.</span></div></div>
                        <div class="col-md-3 tc-block-btn"></div>
                    </div>

                    <div class="my-form">
                        <div class="text-box form-control-fld terms-and-conditions-block">
                            <div class="col-md-3 padding-none tc-block-fld"><div class="input-prepend"><input type="text" name="terms_condtion_types2"  placeholder ='Other Charges' class="form-control form-control1 numberVal fourdigitstwodecimals_deciVal" id="cancellation2" /><span class="add-on unit">Rs.</span></div></div>
                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                        </div>
                    </div>
					

				<div class="col-md-6 form-control-fld">
                          {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
                    </div>
                                        <div class="clearfix"></div>
</div> 
<div class="col-md-12 inner-block-bg inner-block-bg1">
                    <div class="col-md-12 form-control-fld">
                        <div class="radio-block">
                            <div class="radio_inline"><input type="radio" name="optradio" id="post-public" value="1" checked="checked" class="create-posttype-service" /> <label for="post-public"><span></span>Post Public</label></div>
                            <div class="radio_inline"><input type="radio" name="optradio" id="post-private" value="2" class="create-posttype-service" /> <label for="post-private"><span></span>Post Private</label></div>
                        </div>
                    </div>

                    <div class="col-md-3 form-control-fld demo-input_buyers" style="display:none">
                    <div class="input-prepend">
                        <input type="hidden" id="demo-input" name="buyer_list_for_sellers" class="form-control" />
                    </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="check-box form-control-fld margin-none">
                    {!! Form::checkbox('agree', '', '',array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                    </div>
</div>
				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">					
				    {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_ptl','onclick'=>"updatepoststatus(1)"]) !!}		
					{!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_ptl','onclick'=>"updatepoststatus(0)"]) !!}
				</div>

</div>
			</div>
		</div>
		 {!! Form::close() !!}
</div>
@include('partials.footer')
@endsection
