@extends('app') 
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">
		
		@if(Session::has('buyer_reloc_pet_move_status'))
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('buyer_reloc_pet_move_status') }}
				</p>
			</div>
		@endif

		<span class="pull-left"><h1 class="page-title">Posts (Relocation Pet)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		
		<a href="{{ URL::to('relocation/creatbuyerrpost') }}">
			<span class="btn post-btn pull-right"> Post &amp; Get Quote</span>
		</a>
		
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
                                        <option value="1" selected>My Posts</option>
                                        <option value="2">Market Leads</option>
                                    </select>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="normal-select">
	                                
									<?php
									# ------------------------------------------
									$post_status = session('status_search');
									?>
									<select name="status_id" id="status_id" class="selectpicker">
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
									{!!	$filter->field('from_location_id') !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!!	$filter->field('to_location_id') !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-link"></i></span>
									{!!	$filter->field('cage_type') !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-paw"></i></span>
									{!!	$filter->field('pet_type') !!}
								</div>
							</div>

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!!	$filter->field('from_date') !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!!	$filter->field('to_date') !!}
								</div>
							</div>

							{!! $filter->close !!}
						</div>
					</div>

					<!-- Table Starts Here -->
					<div class="table-div">
						<div class="table-data">
							{!! $grid !!}
						</div>
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