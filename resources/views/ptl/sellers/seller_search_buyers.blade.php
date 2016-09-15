@extends('app')
@section('content')
		<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">

	<div class="container container-inner">

			<div class="home-search gray-bg border-bottom-none margin-bottom-none padding-bottom-none margin-top-none">
				<div class="col-md-12 padding-none">
					<div class="col-md-12 form-control-fld">
						<div class="radio-block">
	                        <div class="radio_inline">
	                        <input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /> <label for="spot_lead_type"><span></span>Spot</label></div>
	                        
							<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" /> <label for="term_lead_type"><span></span>Term</label></div>
							
						</div>


					</div>
				</div>
			</div>
		
			{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers-ptl']) !!}
			{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
			<div class="showhide_spot" id="showhide_spot">
			<div class="home-search gray-bg border-top-none margin-none padding-top-none">

			


			<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
			@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
				<div class="col-md-12 padding-none ">
					<div class="col-md-12 form-control-fld">
					@if(Session::get('service_id') == COURIER)
								<div class="col-md-4 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
										   </div>
											<div class="radio_inline">
											{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
											{!! Form::hidden('zone_or_location', '1', array('id' => 'zone_or_location')) !!}
											</div>
										</div>
									</div>
									<div class="col-md-4 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
											{!! Form::hidden('post_or_delivery_type', '1', array('id' => 'post_delivery_type')) !!}
											</div>
										</div>
									</div>
									<div class="col-md-4 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', true, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types', '1', array('id' => 'courier_types')) !!}
											</div>
										</div>
									</div>
							@else
						<div class="radio-block">
							<div class="radio_inline">
								{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'zone_wise_ptl']) !!}
								<label for="zone_wise_ptl"><span></span>Zone wise</label>
							</div>
							<div class="radio_inline">
								{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!}

								<label for="location_wise_ptl"><span></span>Location wise</label>
								{!! Form::hidden('zone_or_location', '1', array('id' => 'zone_or_location')) !!}
							</div>
						</div>
						@endif
					</div>
				</div>
				<div class="clearfix"></div>
			@endif

			<div class="col-md-12 padding-none clear-block">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('dispatch_date', '', ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control', 'readonly'=>true,'placeholder' => 'Dispatch Date*']) !!}
					<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
				</div>
			</div>

			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('delivery_date', '', ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control', 'readonly'=>true,'placeholder' => 'Delivery Date']) !!}
					<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
				</div>
			</div>

			@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('from_location', '', ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control alphanumeric_withSpace', 'placeholder' => 'From Location with pincode / Zone','maxlength'=>10]) !!}
						{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
					</div>
				</div>
				
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('to_location', '', ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control alphanumeric_withSpace', 'placeholder' => 'To Location with pincode / Zone','maxlength'=>10]) !!}
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

				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('from_location', '', ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control from-date-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
						{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
					</div>
				</div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('to_location', '', ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control to-date-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
						{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
					</div>
				</div>
			@endif
			
			@if(Session::get('service_id') != COURIER)
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), null, ['class' => 'selectpicker bs-select-hidden','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_packaging_type_id']) !!}
				</div>
			</div>
			@endif



			@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-archive"></i></span>
						{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
					</div>
				</div>
				<div class="col-md-4 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-archive"></i></span>
						{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
					</div>
				</div>

			@else
				<input type="hidden" id="lkp_air_ocean_shipment_type_id" class="lkp_air_ocean_shipment_type_id" value="1">
				<input type="hidden" id="lkp_air_ocean_sender_identity_id" class="lkp_air_ocean_sender_identity_id" value="1">
			@endif
			<div class="clearfix"></div>

			</div>
			</div>


			<div class="submit_container padding-top">
				<div class="col-md-4 col-md-offset-4">
					<!--button class="btn theme-btn btn-block">Get Quote</button-->
					<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
				</div>
			</div>


			</div>

			{!! Form::close() !!}

		




		
				{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers-ptl']) !!}

				{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
			<div class="showhide_term" id="showhide_term" style="display:none">
			
			<div class="home-search gray-bg border-top-none margin-none padding-top-none border-bottom-none padding-bottom-none">	
				
				@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
					
						@if(Session::get('service_id') != COURIER) 
						<div class="col-md-12 padding-none">
							<div class="col-md-12 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
										{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'term_zone_wise_ptl']) !!}
										<label for="term_zone_wise_ptl"><span></span>Zone wise</label>
									</div>
									<div class="radio_inline">
										{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'term_location_wise_ptl']) !!}
										<label for="term_location_wise_ptl"><span></span>Location wise</label>
										{!! Form::hidden('zone_or_location', '1', array('id' => 'term_zone_or_location')) !!}
									</div>
								</div>
							</div>
						</div>
						@else
						<div class="col-md-12 padding-none">
							<div class="col-md-4 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
										{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'term_zone_wise_ptl']) !!}
										<label for="term_zone_wise_ptl"><span></span>Zone wise</label>
									</div>
									<div class="radio_inline">
										{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'term_location_wise_ptl']) !!}
										<label for="term_location_wise_ptl"><span></span>Location wise</label>
										{!! Form::hidden('zone_or_location', '1', array('id' => 'term_zone_or_location')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'term_domestic']) !!}
									<label for="term_domestic"><span></span>Domestic</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'term_international']) !!} 
									<label for="term_international"><span></span>International</label>
									{!! Form::hidden('post_or_delivery_type', '1', array('id' => 'term_post_delivery_type')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Documents', true, ['id' => 'term_documents']) !!} <label for="term_documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'term_parcel']) !!} <label for="term_parcel"><span></span>Parcel</label>
									{!! Form::hidden('courier_or_types', '1', array('id' => 'term_courier_types')) !!}
									</div>
								</div>
							</div>
						</div>
						@endif
					
					
				@endif
			</div>


			<div class="home-search gray-bg border-top-none margin-none padding-top-none clear-block">


				@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
					@if(Session::get('service_id') == COURIER)
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_from_location', '', ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Zone','maxlength'=>10]) !!}
								{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_to_location', '', ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Zone','maxlength'=>10]) !!}
								{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
							</div>
						</div>
					@else
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_from_location', '', ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Zone','maxlength'=>10]) !!}
								{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_to_location', '', ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Zone','maxlength'=>10]) !!}
								{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
							</div>
						</div>
					@endif
				@else
					@if(Session::get('service_id') == AIR_INTERNATIONAL)
						{{--*/ $plceholder = 'Airports' /*--}}
					@elseif(Session::get('service_id') == OCEAN)
						{{--*/ $plceholder = 'Ocean' /*--}}
					@else
						{{--*/ $plceholder = '' /*--}}
					@endif

					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('term_from_location', '', ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
							{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
						</div>
					</div>
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('term_to_location', '', ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
							{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
						</div>
					</div>
				@endif
				@if(Session::get('service_id') != COURIER)
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters), null, ['class' => 'selectpicker bs-select-hidden','id' => 'term_load_type','onChange'=>'return GetCapacity()']) !!}
						</div>
					</div>
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters), null, ['class' => 'selectpicker bs-select-hidden','id' => 'term_package_type']) !!}
						</div>
					</div>
				@endif




				@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)


					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
						</div>
					</div>
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
						</div>
					</div>


				@endif

				</div>	

				<div class="submit_container padding-top">
					<div class="col-md-4 col-md-offset-4">
						<!--button class="btn theme-btn btn-block">Get Quote</button-->
						<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
					</div>
				</div>

						

			</div>				

				{!! Form::close() !!}

		



</div>




<div class="clearfix"></div>

<div class="home-blocks">
	<div class="container">
		<div class="blocks block1">
			<div></div>
			<h2>Build a Strong Network</h2>
			<p>On Board and manage your current service provider / customer Discover new Partnerships</p>
		</div>
		<div class="blocks block2">
			<div></div>
			<h2>Member Services</h2>
			<p>Offer and manage spot sales and purchases Offer and manage term sales and purchases</p>
		</div>
		<div class="blocks block3">
			<div></div>
			<h2>Market & Transaction Insights</h2>
			<p>Offer and manage spot sales and purchases Offer and manage term sales and</p>
		</div>
	</div>
</div>
@include('partials.footer');

</div>
@endsection