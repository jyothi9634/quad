@extends('app')

@section('content')
<div class="container container-inner">
	<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
	<!-- Left Nav Ends Here -->
	<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.page_top_navigation')
			</div>
			
			<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
				{!! Form::open(['url' => 'sellerlist','id'=>'seller_posts_search','method'=>'GET']) !!}

				<div class="gray_bg">
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
						<select class="selectpicker" name="lead_name">
							@if(Session::get('leads') &&  Session::get('leads')==2)
							<option value='1' >Leads (My Posts)</option>
							<option value='2' selected>Leads (Market Leads)</option>
							@else
							<option value='1' selected>Leads (My Posts)</option>
							<option value='2' >Leads (Market Leads)</option>
							@endif
							
						</select>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! Form::select('service', (['' => 'Service Type (FTL)'] + $services_seller), Session::get('service_id'), ['class' => 'selectpicker','id' => 'service_seller']) !!}
					</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! Form::select('status', (['' => 'Status'] + $posts_status_list), $statusSelected , ['class' => 'selectpicker','id' => 'posts_status']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
						{!! Form::submit(' GO ', ['class' => 'btn','name' => 'go','id' => 'go_seller_search']) !!}
					</div>
					</div>
					<div class="clearfix"></div>
				
				{!! Form::close() !!}
				<div class="gray_bg">

					{!! $filter->open !!}
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
						{!! $filter->field('ptlbq.from_location_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! $filter->field('ptlbq.to_location_id') !!}

					</div>
					<div class="clearfix"></div>


					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
						{!! $filter->field('ptlbqi.lkp_vehicle_type_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! $filter->field('ptlbqi.lkp_load_type_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						<div class="form-group">{!! $filter->field('ptlbq.from_date') !!}</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						<div class="form-group">{!! $filter->field('ptlbq.to_date') !!}</div>
					</div>



					{!! $filter->close !!}
				</div>
				<div class="clearfix"></div>

				<div class="table-top col-md-12 col-sm-12 col-xs-12 padding-none"><input type="checkbox"> 
					<a href="#">Select All</a> <a href="#">Cancel</a> 
					<span class="pull-right">
						View 1-50
					</span>
				</div>
			</div>
			{!! $grid !!}
		</div>
	</div>
		
	<!-- Page Center Content Ends Here -->
	<!-- Right Starts Here -->
		@include('partials.right')
	<!-- Right Ends Here -->
</div>
@endsection

