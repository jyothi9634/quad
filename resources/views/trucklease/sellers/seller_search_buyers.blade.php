@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
	<div class="container container-inner">

		<!-- Left Nav Starts Here -->
		<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			<div class="col-md-3 padding-none text-center">

			</div>
		</div>
		<div class="clearfix"></div>

		{{-- Seller/Home/Transportation/FTL/Search/Spot  --}}
		<div class="showhide_spot" id="showhide_spot">

			{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers-tl']) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								{!! Form::text('from_location', '', ['id' => 'from_location','class' => 'top-text-fld form-control clsLocation', 'placeholder' => 'Location*']) !!}
							    {!! Form::hidden('from_city_id', '', array('id' => 'from_location_id')) !!}
							    {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
							    {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
							    {!!	Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
							</div>
						</div>

						<div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('dispatch_date', '', ['id' => 'dispatch_date1','class' => 'form-control clsFromDate from-date-control','readonly'=>true, 'placeholder' => 'From Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">

								</div>
							</div>

							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('delivery_date', '', ['id' => 'delivery_date1','class' => 'clsToDate form-control to-date-control','readonly'=>true, 'placeholder' => 'To Date*']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">

								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!! Form::select('lkp_trucklease_lease_term_id', (['' => 'Lease Term*'] + $getAllleaseTypes ), null, ['class' => 'selectpicker','id' => 'lkp_trucklease_lease_term_id']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld pull-left">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!! Form::select('lkp_vehicle_type_id', (['' => 'Vehicle Type*'] + $vehicletypemasters +['20'=>'Vehicle Type (Any)']), null, ['class' => 'selectpicker','id' => 'vechile_type']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<img src="{{asset('/images/truck.png')}}" class="truck-type" />
								<span class="truck-type-text">Vehicle Dimensions *<span id ='dimension'></span></span>
							</div>
							<div class="clearfix"></div>

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