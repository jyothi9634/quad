@extends('app')

@section('content')
	@include('partials.page_top_navigation')

	<div class="main">

		@if(Session::has('message_create_post_ptl') && Session::get('message_create_post_ptl')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_create_post_ptl') }}
				</p>
			</div>
		@endif
               
		@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_update_post') }}
				</p>
			</div>
		@endif

		<div class="container">
			<span class="pull-left"><h1 class="page-title">Posts ({{ $servicetype }})</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
			@include('partials.content_top_navigation_links')
			

			<div class="clearfix"></div>

			<div class="col-md-12 padding-none">
				<div class="main-inner">
					<div class="main-right">


						<div class="gray-bg">
							{!! Form::open(['url' => 'sellerlist','id'=>'seller_posts_search','method'=>'GET']) !!}
							<div class="col-md-12 padding-none filter">
								<div class="col-md-3 form-control-fld">
									<div class="normal-select">
										{!! Form::select('type', (['1' => 'My Posts', '2' => 'Market Leads']), $typeSelected , ['class' => 'selectpicker','id' => 'posts_type']) !!}
									</div>
								</div>
								@if($typeSelected==2)
                                {{--*/ $str="display: block"/*--}}
                                @else 
                                {{--*/$str="display: none" /*--}}
                                @endif
								<div class="col-md-3 form-control-fld" id="post_type_div" style="{{$str}}">
									<div class="normal-select">
										{!! Form::select('post_type', (['1' => 'Term', '2' => 'Spot']), $posttypeSelected , ['class' => 'selectpicker','id' => 'post_type']) !!}
									</div>
								</div>
								
								<div class="col-md-3 form-control-fld">
								<div class="normal-select">									
									{{--*/	$post_status = $statusSelected /*--}}
									<select name="status" id="status" class="selectpicker">
										<option value="0" {{ ($post_status==0)? 'selected="selected"':'' }}>Status (All)</option>
									@foreach($posts_status_list as $key => $st)

										<?php 
										if($typeSelected==2 && $posttypeSelected==2 && $key==1):
											continue;
										elseif($key==4):
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
								</div>
								</div>
								
								
								@if(Session::get('service_id') == COURIER)
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('destinationtype', (['1' => 'Domestic', '2' => 'International']), $destinationSelected , ['class' => 'selectpicker','id' => 'posts_destination']) !!}
										</div>
									</div>
									
								@endif
								@if(Session::get('service_id') == COURIER)
								<div class="col-md-3 form-control-fld pull-right">
								@else
								<div class="col-md-3 form-control-fld pull-right">
								@endif
									<!-- button class="btn add-btn pull-right">Filter</button -->
									{!! Form::submit(' GO ', ['class' => 'btn add-btn pull-right','name' => 'go','id' => 'go_seller_search']) !!}
								</div>
							</div>
							{!! Form::close() !!}
						</div>

						{!! $filter->open !!}
						
						@if((Session::get('service_id') == AIR_INTERNATIONAL) || (Session::get('service_id') == OCEAN) )
						<div class="gray-bg">
							<div class="col-md-12 padding-none filter">
							@if($typeSelected=='' || $typeSelected==1)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! $filter->field('spi.from_location_id') !!}	
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! $filter->field('spi.to_location_id') !!}	
									</div>
								</div>
                                                        @elseif($typeSelected==2 && $posttypeSelected==2)
                                                                     <div class="col-md-3 form-control-fld">
									<div class="normal-select">
										{!! $filter->field('ptlbq.from_location_id') !!}
									</div>
                                                                    </div>
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="normal-select">
                                                                                    {!! $filter->field('ptlbq.to_location_id') !!}
                                                                            </div>
                                                                    </div>               
                                                                
							@else
							
							<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('bqit.from_location_id') !!}	
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('bqit.to_location_id') !!}	
										</div>
									</div>
								
								
							@endif	
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										@if(isset($_GET['from_date']))
                                                                                    {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange dateRangeFrom', 'placeholder' => 'From']) !!}
                                                                                @else
                                                                                    {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange dateRangeFrom', 'placeholder' => 'From']) !!}
                                                                                @endif
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										@if(isset($_GET['to_date']))
                                                                                        {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To']) !!}
                                                                                @else
                                                                                        {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To']) !!}
                                                                                    @endif
									</div>
								</div>

								
							</div>
						</div>
						@else
						<div class="gray-bg">
							<div class="col-md-12 padding-none filter">
							@if($typeSelected=='' || $typeSelected==1)
								<div class="col-md-3 form-control-fld">
									<div class="normal-select">
										{!!	$filter->field('pspi.lkp_ptl_post_type_id') !!}	
									</div>
								</div>
							@endif
								
								@if($typeSelected=='' || $typeSelected==1)
								@if(!empty(Session::get('loc_type')))	
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('pspi.from_location_id') !!}	
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('pspi.to_location_id') !!}	
										</div>
									</div>
								@endif	
                                                                @elseif($typeSelected==2 && $posttypeSelected==2)
                                                                     <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! $filter->field('ptlbq.from_location_id') !!}
									</div>
                                                                    </div>
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
																				<span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                                                    {!! $filter->field('ptlbq.to_location_id') !!}
                                                                            </div>
                                                                    </div> 
                                                                @else
								<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('bqit.from_location_id') !!}	
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!!	$filter->field('bqit.to_location_id') !!}	
										</div>
									</div>
								
								
								
								@endif
								@if($typeSelected==1 && (Session::get('service_id') == COURIER))
								<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!!	$filter->field('pspi.lkp_courier_type_id') !!}	
										</div>
								</div>
								@endif
								@if($typeSelected==2 && (Session::get('service_id') == COURIER))
								<div class="col-md-3 form-control-fld">
										<div class="normal-select">
										@if($posttypeSelected==1)
											{!!	$filter->field('bqi.lkp_courier_type_id') !!}
										@else
											{!!	$filter->field('ptlbqi.lkp_courier_type_id') !!}
										@endif		
										</div>
								</div>
								@endif
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										@if(isset($_GET['from_date']))
			                            	{!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange dateRangeFrom', 'placeholder' => 'From']) !!}
			                            @else
			                            	{!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange dateRangeFrom', 'placeholder' => 'From Date']) !!}
			                            @endif
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										@if(isset($_GET['to_date']))
				                            {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
			                            @else
				                            {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To']) !!}
			                            @endif
									</div>
								</div>
							
							</div>
						</div>
						
						@endif
						<input type="hidden" name="type" id="type" value="{{$typeSelected}}">
                                                <input type="hidden" name="post_type"  value="{{$posttypeSelected}}">
                                                <input type="hidden" name="status"  value="{{$statusSelected}}">
						{!! $filter->close !!}

						<div class="table-div">
							{!! $grid !!}
						</div>

					</div>
				</div>
			</div>

			<div class="clearfix"></div>

		</div>
	</div>

	@include('partials.footer')

			<!-- Modal -->
	


	<script type="text/javascript">
		$( document ).ready(function() {
			$('.selectpicker').selectpicker('refresh');
		});

	</script>

@endsection