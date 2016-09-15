@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

		<div class="main">

			<div class="container">
				<span class="pull-left"><h1 class="page-title">Posts (Haul)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
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
											{{--*/ $spot_selected = "" /*--}}											
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
											</div>
										</div>

										<div class="col-md-3 form-control-fld">
											<div class="normal-select">	
												
												<?php
												# ------------------------------------------
												$post_status = session('status_search');
												?>
												<select name="status_id" id="post_status" class="selectpicker">
													<option value="0" {{ ($post_status==0)? 'selected="selected"':'' }}>Status (All)</option>
												@foreach($status as $key => $st)
													@if(request('status_id') == $key || $post_status == $key)
													<option value="{{$key}}" selected="selected">{{$st}}</option>
													@elseif($post_status == '')
													<option value="{{$key}}" selected="selected">{{$st}}</option>
													@else
													<option value="{{$key}}">{{$st}}</option>
													@endif	
												@endforeach
												</select>
												<?php
												# ------------------------------------------
												?>

												
											</div>
										</div>
										
										<div class="col-md-6 form-control-fld">

											{!! Form::submit(' GO ', array( 'class'=>'btn add-btn pull-right')) !!}
										</div>
									</div>
								</div>
							{!! Form :: close() !!}
							{!! $filter->open !!}
					 		{!! $filter->field('src') !!}
							<div class="gray-bg">
								<div class="col-md-12 padding-none filter">
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('spi.from_location_id') !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('spi.to_location_id') !!}
											</div>
										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('spi.lkp_load_type_id') !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-truck"></i></span>
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
												 @if(isset($_GET['to_date']))
					                            {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
					                            @else
					                            {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
					                            @endif
											</div>
										</div>

									</div>

							</div>
							{!! $filter->close !!} 
							
								<div class="table-div">
									<div class="table-data">
										{!! $grid !!}
									</div>
								</div>
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>

@include('partials.footer')
@endsection