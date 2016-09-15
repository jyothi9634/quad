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



		<span class="pull-left"><h1 class="page-title">Posts (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
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
											{{--*/ $spot_selected = "" /*--}}
											{{--*/ $term_selected = "" /*--}}
											{{--*/ $marketleads_selected = "" /*--}}
											@if(Session::get('post_type')=='marketleads')
											{{--*/ $marketleads_selected = "selected" /*--}}
											@else
											{{--*/ $spot_selected = "selected" /*--}}
											@endif
												<select class="selectpicker" id="post_type" name="post_type">
													<option value="spot" {{$spot_selected }}>Post Type (Spot)</option>
							                        <option value="marketleads" {{ $marketleads_selected }}>Post Type (Market Leads)</option>
												</select>
												<!-- {!!
											Form::select('lkp_enquiry_type_id',array('' => 'Enquiry Type (All)')+
											$enquiry_types,$enquiry_type,['class'=>'selectpicker','id'=>'enquiry_types'])
											!!}-->
											</div>
										</div>

										<div class="col-md-3 form-control-fld">
											<div class="normal-select">																							
												
												{!! Form::select('status_id',array('' => 'Status (All)') + $status,$post_status,['class'=>'selectpicker','id'=>'post_status'])!!}
												
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
												{!!	$filter->field('spi.from_location_id') !!}
											</div>
										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('spi.lkp_vehicle_type_id') !!}
											</div>
										</div>

										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												@if(isset($_GET['from_date']))
                                                    {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                @else
                                                    {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                @endif
											</div>
										</div>

										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												<!--input class="form-control form-control1" id="" type="text" placeholder="To Date"-->

												<!--  {!! $filter->field('bqi.delivery_date') !!} -->
                                                
                                                @if(isset($_GET['to_date']))
                                                   	{!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
                                                @else
                                                    {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
                                               	@endif

											</div>
										</div>

										{!! $filter->close !!}
						 
									</div>

								</div> <!--gray-bg -->
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