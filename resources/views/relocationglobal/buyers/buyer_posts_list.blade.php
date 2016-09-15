@extends('app') @section('content')
		<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">
		@if(Session::has('sumsg'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
                                    <!---------start srinu 29-04-2016 for save as draft message display change get msg name sumsg------------------------------>
					{{ Session::get('sumsg') }}
                                    <!---------End 29-04-2016-------------->
				</p>
			</div>
		@endif



		@if(Session::has('message_create_post_ptl'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_create_post_ptl') }}
				</p>
			</div>
		@endif



		<span class="pull-left"><h1 class="page-title">Posts (Relocation Global Mobility)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
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
										@if($lead_types==1)
										<select class="selectpicker" id="lead_types" name="lead_types">
												<option value="1" selected>My Posts</option>
												<option value="2">Market Leads</option>
												<option value="3">Term</option>
											    </select>
									    @else
									    <select class="selectpicker" id="lead_types" name="lead_types">
												<option value="1">My Posts</option>
												<option value="2">Market Leads</option>
												<option value="3" selected>Term</option>
											    </select>
									    @endif		    
									   </div>	
									   </div>	
										
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">																							
												@if(Session::get('post_type')=='term')
												<select name="status_id" id="status_id" class="selectpicker">
												<option value="">Status (All)</option>
												<option <?php if (isset ( $_REQUEST ['status_id'] ) && $_REQUEST ['status_id'] == 2) { ?> selected="selected" value="2" <?php } else { ?>value="2"<?php } ?> >Open</option>
												<option <?php if (isset ( $_REQUEST ['status_id'] ) && $_REQUEST ['status_id'] == 1) { ?> selected="selected" value="1" <?php } else { ?>value="1"<?php } ?> >Draft</option>
												<option <?php if (isset ( $_REQUEST ['status_id'] ) && $_REQUEST ['status_id'] == 5) { ?> selected="selected" value="5" <?php } else { ?>value="5"<?php } ?> >Deleted</option>
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
										
										<div class="col-md-3 form-control-fld pull-right">

											{!! Form::submit(' GO ', array( 'class'=>'btn add-btn pull-right')) !!}
										</div>


									</div>

								</div>
								{!! Form :: close() !!}

					@if($lead_types==3)
					
					<div class="gray-bg">
										<div class="col-md-12 padding-none filter">
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

										
					{!! $filter->close !!}
					@else
								<div class="gray-bg">
										<div class="col-md-12 padding-none filter">
											{!! $filter->open !!}
											{!! $filter->field('src') !!}
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!!	$filter->field('rbs.location_id') !!}
											</div>
										</div>

							<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-cog"></i></span>
										@if(!isset($_GET['relgm_service_type']))
											{{--*/ $filerby_service_type = ''  /*--}}
										@else 
											{{--*/ $filerby_service_type = $_GET['relgm_service_type']  /*--}}
										@endif

										{!!	Form::select('relgm_service_type',(['' => 'Services'] + $lkp_relgm_services),  $filerby_service_type,['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'this.form.submit()']) !!}
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
										<input type="hidden" name="lead_types" id="lead_types" value="1">
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
@endsection