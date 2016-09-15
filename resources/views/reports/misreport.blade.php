@extends('app')
@section('content')

	<!-- Page top navigation Starts Here-->
	@include('partials.page_top_navigation')
	<div class="main">
		<div class="container container-inner">

			<!-- Left Nav Starts Here -->
			<div class="clearfix"></div>

			{{-- Seller/Home/Transportation/FTL/Search/Spot  --}}
			<div class="showhide_spot" id="showhide_spot">

				{!! Form::open(['url' =>'dailymisreport','method'=>'GET','id'=>'posts-form-lines']) !!}
				<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
					<div class="home-search-form">

						<div class="clearfix"></div>
						<div class="col-md-12 padding-top">


							<div class="padding-top">
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										{!! Form::text('valid_from', '', ['id' => 'start_dispatch_date','class' => 'form-control calendar from-date-control','readonly'=>true, 'placeholder' => 'From Date*']) !!}
									</div>
								</div>

								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										{!! Form::text('valid_to', '', ['id' => 'end_dispatch_date','class' => 'calendar form-control to-date-control','readonly'=>true, 'placeholder' => 'To Date']) !!}
									</div>
								</div>


							</div>
						</div>
					</div>
				</div>
				<div class="submit_container">
					<div class="col-md-4 col-md-offset-4">
						<!--button class="btn theme-btn btn-block">Get Quote</button-->
						<input type="submit" id ='buyersearchresults' value="&nbsp; Generate MIS Report &nbsp;" class="btn theme-btn btn-block">
					</div>
				</div>
				{!! Form::close() !!}
			</div>

			{{-- Seller/Home/Transportation/FTL/Search/Term  --}}


			<div class="clearfix"></div>

		</div>



		@include('partials.footer')

	</div>

@endsection