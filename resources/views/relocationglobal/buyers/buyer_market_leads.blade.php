@extends('app') @section('content')
		<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">

<div class="container">

	@if(Session::has('sumsg'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('sumsg') }}
				</p>
			</div>
		@endif



		@if(Session::has('succmsg'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('succmsg') }}
				</p>
			</div>
		@endif
	<span class="pull-left"><h1 class="page-title">Posts (Relocation Global Mobility)</h1><a href="#" class="change-service">Change Service</a></span>
	
	
	<!-- Page top navigation Starts Here-->
		@include('partials.content_top_navigation_links')

	<div class="clearfix"></div>
	
	<div class="col-md-12 padding-none">
		<div class="main-inner"> 
			
			
			<!-- Right Section Starts Here -->

			<div class="main-right">
			{!! Form::open(array('url' => 'buyerposts', 'id'=>'buyer-post-search', 'class'=>'form-inline ' )) !!}
				<div class="gray-bg">
					<div class="col-md-12 padding-none filter">
							<div class="col-md-3 form-control-fld">
								<div class="normal-select">
									<select class="selectpicker" id="lead_types" name="lead_types">
										<option value="1">My Posts</option>
										<option value="2" selected="selected">Market Leads</option>
										<option value="3">Term</option>
									</select>
								</div>
							</div>

							<div class="col-md-3 form-control-fld">
								<div class="normal-select">
									  {!! Form::select('status_id',array('' => 'Status (All)') + $status,$post_status,['class'=>'selectpicker','id'=>'post_status'])!!}		
								</div>
							</div>
							
							<div class="col-md-3 form-control-fld pull-right">
								{!! Form::submit(' GO ', array( 'class'=>'btn add-btn pull-right')) !!}
							</div>
						</div>

				</div>
			{!! Form :: close() !!}
			
				<div class="gray-bg">
					<div class="col-md-12 padding-none filter">
							{!! $filter->open !!}
							{!! $filter->field('src') !!}

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!!	$filter->field('rsp.location_id') !!}
								</div>
							</div>
						<!-- 	<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-cog"></i></span>
									{!!	Form::select('relgm_service_type',(['' => 'Services'] + $lkp_relgm_services), '',['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'this.form.submit()']) !!}
								</div>
							</div> -->
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									@if(isset($_GET['dispatch_date']))
										{!! Form::text('dispatch_date', $_GET['dispatch_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
									@else
										{!! Form::text('dispatch_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
									@endif
								</div>
							</div>
							

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									@if(isset($_GET['delivery_date']))
										{!! Form::text('delivery_date', $_GET['delivery_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To']) !!}
									@else
										{!! Form::text('delivery_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
									@endif
								</div>
							</div>
									<input type="hidden" name="lead_types" id="lead_types" value="2">
										{!! $filter->close !!}
						</div>

				</div>
				
				<!-- Table Starts Here -->
					<div class="table-div">					
						<div class="table-data">
							{!! $grid !!}
						</div>
					</div>

				<!-- Table Ends Here -->

			</div>

			<!-- Right Section Ends Here -->

		</div>
	</div>

	<div class="clearfix"></div>

</div>
</div>

@include('partials.footer')
@endsection