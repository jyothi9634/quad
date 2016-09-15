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



		<span class="pull-left"><h1 class="page-title">Posts (Relocation International)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
		<!--button class="btn post-btn pull-right">Post & get Quote</button-->

		<!-- Page top navigation Starts Here-->
		@include('partials.content_top_navigation_links')

					<div class="clearfix"></div>

					<div class="col-md-12 padding-none">
						<div class="main-inner">
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
												{!! Form::select('international_types',$international_types,$rel_int_type,['class'=>'selectpicker','id'=>'international_types'])!!}
											</div>
										</div>
										
										
										<div class="col-md-6 form-control-fld">

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
												{!! $filter->field('rsp.from_location_id') !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!! $filter->field('rsp.to_location_id') !!}
											</div>
										</div>

										

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
												<!--input class="form-control form-control1" id="" type="text" placeholder="To Date"-->

												<!--  {!! $filter->field('bqi.delivery_date') !!} -->
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

					
                                                            <div class="table-div">
                                                                <div class="table-data">
                                                                        {!! $grid !!}
                                                                </div>
                                                            </div>
							</div> <!-- main-right -->


						</div> <!-- main-inner -->
					</div> <!-- 		col-md-12 padding-none -->


	</div><!-- container div -->
</div> <!-- Main div -->



@include('partials.footer')
@endsection