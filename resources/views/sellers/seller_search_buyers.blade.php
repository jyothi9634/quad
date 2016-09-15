@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
	<div class="container container-inner">

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

			{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers']) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
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
							    {!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
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
									{!! Form::text('dispatch_date', '', ['id' => 'datepicker_search','class' => 'form-control calendar from-date-control','readonly'=>true, 'placeholder' => 'Dispatch Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('delivery_date', '', ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly'=>true, 'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), null, ['class' => 'selectpicker','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">

									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('qty', '', ['id' => 'qty','search'=>'1','class' => 'form-control clsFTLQuantity', 'placeholder' => 'Qty']) !!}

										<span class="add-on unit1">
										<!--input class="form-control" id="" type="text" placeholder="Capacity"-->
										{!!	Form::text('capacity',null,array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}
										</span>
									</div>

							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld pull-left">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), null, ['class' => 'selectpicker','id' => 'vechile_type']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<img src="{{asset('/images/truck.png')}}" class="truck-type" />
								<span class="truck-type-text">Vehicle Dimensions *<span id ='dimension'></span></span>
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
			{!! Form::close() !!}
		</div>

		{{-- Seller/Home/Transportation/FTL/Search/Term  --}}
		<div class="showhide_term" id="showhide_term" style="display:none">
			{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								{!! Form::text('term_from_location', '', ['id' => 'term_from_location','class' => 'top-text-fld form-control clsFTLFromLocation', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', '', array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
								{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="To Location"-->
								{!! Form::text('term_to_location', '', ['id' => 'term_to_location','class' => 'top-text-fld form-control clsFTLtoLocation', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', '', array('id' => 'term_to_location_id')) !!}
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
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld pull-left">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!! Form::select('lkp_vehicle_type_id', (['20'=>'Vehicle Type (Any)'] + $vehicletypemasters ), null, ['class' => 'selectpicker','id' => 'vechile_type_1']) !!}
											<!--select class="selectpicker">
									<option value="0">Select Vehicle Type</option>
								</select-->
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<img src="{{asset('/images/truck.png')}}" class="truck-type" />
								<span class="truck-type-text">Vehicle Dimensions *<span id ='dimension_1'></span></span>
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

	@include('partials.footer')

</div>
<script type="text/javascript">
$(document).ready(function(){
	var ftlSearchType = $("input[name=lead_type]:checked").val();
	if(ftlSearchType == 1){
		$("#showhide_spot").show();
		$("#showhide_term").hide();
	}
	if(ftlSearchType == 2){
		$("#showhide_spot").hide();
		$("#showhide_term").show();
	}
});
</script>
@endsection