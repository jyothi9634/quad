@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->

<div class="main">
	<div class="container">
		@if(Session::has('succesmessage'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('succesmessage') }}
				</p>
			</div>
		@endif

		@if(Session::has('ptlsuccessupdate'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('ptlsuccessupdate') }}
				</p>
			</div>
		@endif

		@if(Session::has('sumsg'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('sumsg') }}
				</p>
			</div>
		@endif

		<span class="pull-left"><h1 class="page-title">Posts ({!! $commonComponent->getServiceName(Session::get('service_id')) !!})</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
		<!-- Content top navigation Starts Here-->
		@include('partials.content_top_navigation_links')
		<!-- Content top navigation ends Here-->

					<div class="clearfix"></div>

					<div class="col-md-12 padding-none">
						<div class="main-inner">
							<div class="main-right">


								<div class="gray-bg">
									<div class="col-md-12 padding-none filter">
										{!! Form::open(array('url' => 'buyerposts/search', 'id'=>'buyer-post-search', 'class'=>'form-inline ' )) !!}
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
											{{--*/ $spot_selected = "" /*--}}
											{{--*/ $term_selected = "" /*--}}
											{{--*/ $marketleads_selected = "" /*--}}
											@if(Session::get('post_type')=='term') 
											{{--*/ $term_selected = "selected" /*--}}
											@elseif(Session::get('post_type')=='marketleads')
											{{--*/ $marketleads_selected = "selected" /*--}}
											@else
											{{--*/ $spot_selected = "selected" /*--}}
											@endif
												<select class="selectpicker" id="post_type" name="post_type">
													<option value="spot" {{$spot_selected }}>Post Type (Spot)</option>
							                        <option value="term" {{ $term_selected }}>Post Type (Term)</option>
							                        <option value="marketleads" {{ $marketleads_selected }}>Post Type (Market Leads)</option>
												</select>
											</div>
										</div>
										@if(Session::get('service_id') == COURIER)
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
											{{--*/ $domestic_selected = "" /*--}}
											{{--*/ $international_selected = "" /*--}}
											@if(Session::get('delivery_type')=='1') 
											{{--*/ $domestic_selected = "selected" /*--}}
											@else
											 {{--*/ $international_selected = "selected" /*--}}
											@endif
												<select class="selectpicker" id="delivery_type" name="delivery_type">
													<option value="1" {{ $domestic_selected }}>Domestic</option>
													<option value="2" {{ $international_selected }}>International</option>
													
												</select>
											</div>
										</div>
										@endif
										
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">		
												
												@if(Session::get('post_type')=='term')
												<select name="status_id" id="status_id" class="selectpicker">
												<option value="">Status (All)</option>
												<option <?php echo (request('status_id')== 2 || !request('status_id'))? 'selected="selected"':'' ?> value="2">Open</option>
												<option <?php echo (request('status_id') == 1)? 'selected="selected"':'' ?> value="1">Draft</option>
												<option <?php echo (request('status_id') == 5)? 'selected="selected"':'' ?> value="1">Deleted</option>
												</select>												
												@else

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

												@endif									
												
											</div>
										</div>

										<div class="col-md-3 pull-right form-control-fld">
											{!! Form::submit(' GO ', array( 'class'=>'btn add-btn pull-right')) !!}
										</div>
										{!! Form :: close() !!}

									</div>

								</div>



								<div class="gray-bg">
									<div class="col-md-12 padding-none filter">
										@if(Session::get('post_type')=='term')
					
											{!! $filter->open !!}
											{!! $filter->field('src') !!}
										<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('bqi.from_location_id') !!}
										</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('bqi.to_location_id') !!}
											</div>
										</div>
										
										<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												@if(isset($_GET['bid_end_date']))
					                            {!! Form::text('bid_end_date', $_GET['bid_end_date'],['id' => 'bid_end_date','class'=>'form-control dateRange', 'placeholder' => 'Bid End Date']) !!}
					                            @else

					                          {!! Form::text('bid_end_date', '',['id' => 'bid_end_date','class'=>'form-control dateRange', 'placeholder' => 'Bid End Date']) !!}
										 @endif
										</div>
										</div>

										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												@if(isset($_GET['dispatch_date']))
					                            {!! Form::text('dispatch_date', $_GET['dispatch_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'Valid From']) !!}
					                            @else
					                            {!! Form::text('dispatch_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'Valid From']) !!}
					                            @endif
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												 @if(isset($_GET['delivery_date']))
					                            {!! Form::text('delivery_date', $_GET['delivery_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'Valid To']) !!}
					                            @else
					                            {!! Form::text('delivery_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'Valid To']) !!}
					                            @endif
											</div>
										</div>
										
										@if(Session::get('service_id') == COURIER)
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                    							<span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('bqi.lkp_courier_type_id') !!}														
											</div>
										</div>
										@endif
										
								{!! $filter->close !!}
								@else
										{!! $filter->open !!}
										{!! $filter->field('src') !!}
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('ptlbq.from_location_id') !!}														
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('ptlbq.to_location_id') !!}
											</div>
										</div>
									@if(Session::get('service_id') != COURIER)
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                    							<span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('ptlbqi.lkp_load_type_id') !!}														
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                    							<span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('ptlbqi.lkp_packaging_type_id') !!}														
											</div>
										</div>
									@endif
									@if(Session::get('service_id') == COURIER)
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
                    							<span class="add-on"><i class="fa fa-archive"></i></span>
												{!!	$filter->field('ptlbqi.lkp_courier_type_id') !!}														
											</div>
										</div>
									@endif
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												<!--input class="form-control form-control1" id="" type="text" placeholder="From Date"-->
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
												@if(isset($_GET['delivery_date']))
													{!! Form::text('delivery_date', $_GET['delivery_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
												@else
													{!! Form::text('delivery_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To Date']) !!}
												@endif
											</div>
										</div>
										@if(Session::get('service_id') == COURIER)
										<input type="hidden" name="delivery_type" id="delivery_type" value="{{$domestic_or_international_selected}}">
										@endif
										{!! $filter->close !!}
									@endif
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
@stop

@endsection