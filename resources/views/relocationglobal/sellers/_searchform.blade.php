
@if(isset($spot_or_term) && $spot_or_term==2)
	{{--*/ $is_term=true /*--}}
@else 
	{{--*/ $is_term=false /*--}}
@endif

@if($is_term && Session::get('session_spot_or_term') == 2)
	{{--*/ $spot_or_term_selected = "checked" /*--}}
@else
	{{--*/ $spot_or_term_selected = "" /*--}}
@endif

@if($is_term && Session::get('session_term_relgm_service_type'))
	{{--*/ $service_id = Session::get('session_term_relgm_service_type') /*--}}
@else
	{{--*/ $service_id = "" /*--}}
@endif


<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			<div class="col-md-3 padding-none text-center">
				<div class="col-md-12 form-control-fld">

					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" @if($is_term) {{$spot_or_term_selected}} @else checked @endif /><label for="spot_lead_type"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" {{$spot_or_term_selected}} /><label for="term_lead_type"> <span></span>Term</label></div>
					</div>

				</div>
			</div>
		</div>
		<div class="clearfix"></div>

<!-- Start Seller Spot Search -->
		<div class="showhide_spot" id="showhide_spot" @if($is_term) style="display:none" @endif>

			{!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_domestic_sellersearch_buyers','method'=>'get']) !!}
				<div class="home-search gray-bg margin-top-none border-top-none padding-top-none">
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', Session::get('session_to_location_relocation'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'Location *']) !!}
								{!! Form::hidden('to_location_id', Session::get('session_to_location_id_relocation'), array('id' => 'to_location_id')) !!}
                                                                {!! Form::hidden('seller_district_id', Session::get('session_seller_district_id_relocation'), array('id' => 'seller_district_id')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('valid_from', Session::get('session_valid_from_relocation'),  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'From Date*']) !!}
								<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
							</div>
						</div>
						{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
						{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_to', Session::get('session_valid_to_relocation') , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'To Date ']) !!}
								<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
							</div>
						</div>
						<div class="clearfix"></div>
						
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-cog"></i></span>
								{!!	Form::select('relgm_service_type',( $lkp_relgm_services), Session::get('session_service_type_relocation') ,['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'return getServiceTypeMeasurementUnit()']) !!}
							</div>
						</div>
						
					</div>
				</div>

				<div class="col-md-4 col-md-offset-4">
					<button class="btn theme-btn btn-block">Search</button>
				</div>
			{!! Form::close() !!}
		</div>


<!-- Start Seller Term Search -->
		
		<div class="showhide_term" id="showhide_term" @if(!$is_term) style="display:none" @endif>
			{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								@if(isset($from_location))	
									{!! Form::text('term_from_location', $from_location, ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'Location*']) !!}
									{!! Form::hidden('term_from_city_id', $from_city_id, array('id' => 'term_from_location_id')) !!}
									{!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id')) !!}
								@else
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'Location*']) !!}
									{!! Form::hidden('term_from_city_id', '', array('id' => 'term_from_location_id')) !!}
									{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
								@endif
								{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!!	Form::hidden('spot_or_term',2,array('class'=>'form-control')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-cog"></i></span>
								{!!	Form::select('relgm_service_type',(['' => 'Services'] + $lkp_relgm_services), $service_id  ,['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'return getServiceTypeMeasurementUnit()','required'=>true]) !!}
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

<!-- End Seller Term Search -->