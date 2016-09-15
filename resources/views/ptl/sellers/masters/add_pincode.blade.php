@extends('app') @section('content')
<div class="main-container">
	<div class="container container-inner">

		@if(Session::has('ptl_success_message') &&
		Session::get('ptl_success_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
				{{ Session::get('ptl_success_message') }}</p>
		</div>
		@endif @if(Session::has('ptl_error_message') &&
		Session::get('ptl_error_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-danger">
				{{ Session::get('ptl_error_message') }}</p>
		</div>
		@endif
		<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
		<!-- Left Nav Ends Here -->

		<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
			<div class="tab-nav underline">
                    @include('partials.page_top_navigation')
                </div>
				<div class="tab-nav">
					<ul id="tabs">
						<li><a href="zone">Zone</a></li>
						<li><a href="tier">Tier</a></li>
						<li><a href="transit_matrix">Transit Days Matrix</a></li>
						<li><a href="sector">Sector</a></li>
						<li class="active"><a href="pincode">Pincode</a></li>
					</ul>
				</div><div class="block">


				{!! Form::open(array('url' => 'ptlmasters/add_pincode', 'id' =>
				'ptl-add-pincode', 'class'=>'form-group','enctype' =>
				'multipart/form-data' )) !!}
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
					<h4 class="pull-left">Add Pincode</h4>
				</div>

				<div class="clearfix"></div>
				<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
					{!! Form::text('ptl_pincode_id', '',['id' => 'ptl_pincode_id',
					'class'=>'form-control', 'placeholder' => 'Pincode *']) !!} {!!
					Form::hidden('ptlPincodeId', '', array('id' => 'ptlPincodeId')) !!}
				</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none ">
					{!! Form::select('ptl_sector_id',array('' => 'Select
					Sector')+$sectorsList ,'',['class'=>'selectpicker',
					'id'=>'ptl_sector_id']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
					{!! Form::select('oda_pincode',array('' =>
					'ODA')+[1=>'Yes',0=>'No'] ,'',['class'=>'selectpicker',
					'id'=>'oda_pincode']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_location', '',['id' => 'pincode_location',
					'class'=>'form-control', 'placeholder' => 'Location Name *']) !!}</div>


				<div class="clearfix"></div>
				<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
					{!! Form::text('pincode_city', '',['id' => 'pincode_city',
					'class'=>'form-control', 'placeholder' => 'City *']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('postal_division', '',['id' => 'postal_division',
					'class'=>'form-control', 'placeholder' => 'Postal Division *']) !!}
				</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_district', '',['id' => 'pincode_district',
					'class'=>'form-control', 'placeholder' => 'District *']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_state', '',['id' => 'pincode_state',
					'class'=>'form-control', 'placeholder' => 'State *']) !!}</div>
				<div class="clearfix"></div>
				<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
					{!! Form::text('lkp_zone_id', '',['id' => 'lkp_zone_id','readonly'
					=> 'readonly', 'diasbled' => 'diasbled', 'class'=>'form-control',
					'placeholder' => 'Zone *']) !!}</div>

				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('lkp_tier_id', '',['id' => 'lkp_tier_id','readonly'
					=> 'readonly','diasbled' => 'diasbled', 'class'=>'form-control',
					'placeholder' => 'Tier *']) !!}</div>


				<div class="clearfix"></div>

				<div class="col-md-3 col-sm-3 col-xs-12 padding-none margin-top">

					{!! Form::submit(' Add ', ['name' => 'confirm','class'=>'btn
					btn-black ']) !!}</div>
				{!! Form::close() !!}
				<div class="clearfix"></div>

			</div>
		</div>


		<!-- Right Starts Here -->
		@include('partials.right')
		<!-- Right Ends Here -->
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".dropdown_data h3").click(function(){
		    $(this).next("ul").slideToggle();
		    $(this).find(".fa-caret-right").hide();
		    $(this).find(".fa-caret-down").show();   
		    $(this).find("a").toggleClass("bg-red");
	    });
	    $(".dropdown_active h3").click(function(){
		    $(this).find(".fa-caret-down").hide();
		    $(this).find(".fa-caret-right").show();
	    });
	    $(".dropdown_data ul li.inner-dropdown").click(function(){
	    	$(this).toggleClass("active");
		    $(this).find(".fa-caret-down").hide();
		    $(this).find(".fa-caret-right").show();
		    $(this).find("ol").slideToggle();
	    });
	});
</script>

@endsection
