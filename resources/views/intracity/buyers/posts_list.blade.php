@extends('app') @section('content')
@include('partials.page_top_navigation') 


@if (Session::has('transacId')
&& Session::get('transacId')!='')
<script type="text/javascript">
	
   	 $(document).ready(function() {
        $('#myModal').modal('show'); 
      
    	});
    </script>
@endif

<div class="main">

	<div class="container">
		<span class="pull-left"><h1 class="page-title">Posts (Intracity)</h1> <a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>


		
		<a onclick="return checkSession(3,'/intracity/buyer_post');" href="#">
		<button class="btn post-btn pull-right">Post &amp; Get Quote</button></a>

		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			<div class="main-inner">


				<!-- Right Section Starts Here -->

				<div class="main-right">

					<div class="gray-bg">
						{!! Form::open(array('url' => 'buyerposts/search', 'id'
						=>'buyer-post-search', 'class'=>'form-inline ' )) !!}

						<div class="col-md-12 padding-none filter">

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
							<div class="col-md-9 form-control-fld text-right">
								<button class="btn add-btn "> GO </button>
							</div>
						</div>
					</div>
					{!! Form :: close() !!}
			




				<div class="gray-bg">
					{!! $filter->open !!}
					 {!! $filter->field('src') !!}
					<div class="col-md-12 padding-none filter">

						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							
							{!!	$filter->field('bqi.ict_lkp_city_id') !!}</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
								
								{!!$filter->field('bqi.from_location_id') !!}</div>
						</div>

						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! $filter->field('bqi.to_location_id') !!}</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="normal-select">
							{!! $filter->field('bqi.lkp_load_type_id') !!}</div>
						</div>

						<div class="col-md-3 form-control-fld">
							<div class="normal-select">
							{!! $filter->field('bqi.lkp_vehicle_type_id') !!}</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							    @if(isset($_GET['start_pickup_date']))
                                                    {!! Form::text('start_pickup_date', $_GET['start_pickup_date'],['id' => 'start_pickup_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From']) !!}
                                                @else
                                                    {!! Form::text('start_pickup_date', '',['id' => 'start_pickup_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From']) !!}
                                                @endif</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								 @if(isset($_GET['end_pickup_date']))
                                                    {!! Form::text('end_pickup_date', $_GET['end_pickup_date'],['id' => 'end_pickup_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To']) !!}
                                                @else
                                                    {!! Form::text('end_pickup_date', '',['id' => 'end_pickup_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To']) !!}
                                                @endif</div>
						</div>

						<div class="col-md-3 form-control-fld displayNone">
							<button class="btn add-btn pull-right">Filter</button>
						</div>
					</div>
					{!! $filter->close !!}

				</div>


				<div
					class="page-results pull-left col-md-3 padding-none results-full">
					<div class="form-control-fld">
						<div class="displayNone"><input type="checkbox" id="globalbuyerpostlistcheck" 
							name="globalbuyerpostlistcheck"><span class="lbl padding-8"></span><a
							href="javascript:void(0)">Select All</a>
							</div>
						<div class="normal-select">
							<select class="selectpicker">
								<option value="0">10 Records Per page</option>
							</select>
						</div>
					</div>

				</div>
				<!--div class="pull-right margin-top">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> 200</a>
					

				</div-->

				<!-- Table Starts Here -->

				<div class="table-div">{!! $grid !!}</div>



			</div>



		</div>
	</div>

	<div class="clearfix"></div>

</div>
</div>



@include('partials.footer')

</div>


<!-- Modal -->

<!-- Success Transaction Modal -->

<div class="modal fade registeration" id="myModal" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button data-dismiss="modal" class="close" type="button">×</button>
			<div class="modal-body">
				<h3 class="sub-head margin-none red margin-bottom">Please confirm
					that you are booking for</h3>
				<div class="margin-top"></div>
				<div class="col-md-12 padding-none margin-top margin-bottom">
					<p class="pull-left user">
						<span>Your request for quote has been posted successfully to the
							relevant vendors. Your transaction Id is <span class="">
							 {!! Session::get('transacId') !!}</span>. You would be getting the
							quotes from the vendors online.
						</span>
					</p>
					<p class="pull-right"></p>
				
				</div>
				<div class="margin-top"></div>

				<div class="col-md-12 padding-none">
					<p class="text-left">
						<a>Need Help? Helpdesk</a> | <span class="round-circle"><i
							class="fa fa-headphones"></i> </span> <a>Call (040) 394 12345</a>
						| <a>Email</a> | <a>Chat</a> | <a>Address</a>

					</p></div>

				<div class="clearfix"></div>

			</div>
		</div>
	</div>
</div>


@endsection
