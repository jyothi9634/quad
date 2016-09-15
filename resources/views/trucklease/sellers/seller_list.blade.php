@extends('app')

@section('content')

	@include('partials.page_top_navigation')

	<div class="main">

		@if(Session::has('message_create_post') && Session::get('message_create_post')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_create_post') }}
				</p>
			</div>
		@endif
                @if(Session::has('success') && Session::get('success')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('success') }}
				</p>
			</div>
		@endif

		<div class="container">
			<span class="pull-left"><h1 class="page-title">Posts (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>

			<a onclick="return checkSession(5,'/trucklease/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

			<div class="clearfix"></div>

			<div class="col-md-12 padding-none">
				<div class="main-inner">

					<!-- Right Section Starts Here -->

					<div class="main-right">
						{!! Form::open(['url' => 'sellerlist','id'=>'seller_posts_search','method'=>'GET']) !!}
						<div class="gray-bg">
							<div class="col-md-12 padding-none filter">
								<div class="col-md-3 form-control-fld">
									<div class="normal-select">
										{!! Form::select('type', (['1' => 'My Posts', '2' => 'Market Leads']), $typeSelected , ['class' => 'selectpicker','id' => 'posts_type']) !!}
									</div>
								</div>
                              
								<div class="col-md-3 form-control-fld">
								<div class="normal-select">
								{{--*/	$post_status = $statusSelected /*--}}
								<select name="status" id="status" class="selectpicker">
									<option value="0" {{ ($post_status==0)? 'selected="selected"':'' }}>Status (All)</option>
								@foreach($posts_status_list as $key => $st)
									<?php 
									if($key == 4) continue;									
									if($typeSelected==2 && $key==1):
										continue;
									endif;
									?>
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


								<div class="col-md-3 form-control-fld pull-right">
									<!-- button class="btn add-btn pull-right">Filter</button -->
									{!! Form::submit(' GO ', ['class' => 'btn add-btn pull-right','name' => 'go','id' => 'go_seller_search']) !!}
								</div>
							</div>
							{!! Form::close() !!}
						</div>


						<div class="gray-bg">
							<div class="col-md-12 padding-none filter">
								{!! $filter->open !!}
								@if($typeSelected=='' || $typeSelected==1)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-map-marker"></i>
										</span>
										{!! $filter->field('spi.from_location_id') !!}
									</div>
								</div>
							  
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-truck"></i>
										</span>
										{!! $filter->field('spi.lkp_trucklease_lease_term_id') !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-truck"></i>
										</span>
										{!! $filter->field('spi.lkp_vehicle_type_id') !!}
									</div>
								</div>
								
								
							@else
							<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-map-marker"></i>
										</span>
										{!! $filter->field('bqi.from_city_id') !!}
									</div>
								</div>
							  <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-truck"></i>
										</span>
										{!! $filter->field('bqi.lkp_vehicle_type_id') !!}
									</div>
								</div>
							
																
                            @endif	
                           
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-calendar-o"></i>
										</span>
                            @if(isset($_GET['from_date']))
                            {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'dateRange dateRangeFrom form-control', 'placeholder' => 'From Date']) !!}
                            @else
                            {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'dateRange dateRangeFrom form-control', 'placeholder' => 'From Date']) !!}
                            @endif
                                                                                
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on">
											<i class="fa fa-calendar-o"></i>
										</span>
										@if(isset($_GET['to_date']))
											{!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
										@else
											{!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
										@endif
									</div>
								</div>
							</div>
							<input type="hidden" name="type" id="type" value="{{$typeSelected}}">
                            <input type="hidden" name="post_type"  value="{{$posttypeSelected}}">
                            <input type="hidden" name="status"  value="{{$statusSelected}}">
							{!! $filter->close !!}
						</div>


						<!-- Table Starts Here -->

						<div class="table-div">

							<!-- Table Head Starts Here -->

							<!-- Table Head Ends Here -->

							<!--div class="table-data" -->

							{!! $grid !!}

									<!--/div-->
						</div>

						<!-- Table Starts Here -->

					</div>

					<!-- Right Section Ends Here -->

				</div>
			</div>

			<div class="clearfix"></div>

		</div>
	</div>

@include('partials.footer')
@endsection
