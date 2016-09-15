@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


<div class="main">
	<div class="container">
		<!-- Left Nav Starts Here -->
		<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			<div class="col-md-3 padding-none text-center">
				<div class="col-md-12 form-control-fld">

					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /><label for="spot_lead_type"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" /><label for="term_lead_type"> <span></span>Term</label></div>
					</div>

				</div>
			</div>
		</div>
		<div class="clearfix"></div>

		{{-- Seller/Home/Transportation/FTL/Search/Spot  --}}
		<div class="showhide_spot" id="showhide_spot">

			{!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_domestic_sellersearch_buyers','method'=>'get']) !!}
				<div class="home-search gray-bg margin-top-none border-top-none padding-top-none">
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
		<div class="showhide_term" id="showhide_term" style="display:none">
			{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">

								<input type="radio" id="term_post_rate_card_type_1" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1" checked>
								<label for="term_post_rate_card_type_1"><span></span>HHG</label>

								<input type="radio" id="term_post_rate_card_type_2" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="2">
								<label for="term_post_rate_card_type_2"><span></span>Vehicle</label>
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								{!! Form::text('term_from_location', '', ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', '', array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
								{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!!	Form::hidden('spot_or_term',2,array('class'=>'form-control')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="To Location"-->
								{!! Form::text('term_to_location', '', ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', '', array('id' => 'term_to_location_id')) !!}
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
</div>

@include('partials.footer')
@endsection



