@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="main">
	<div class="container">
		<h1 class="page-title">Search Results (Relocation)</h1>
		<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
		<!-- Search Block Starts Here -->
		<div class="search-block inner-block-bg">
			<div class="from-to-area">
				<span class="search-result">
					<i class="fa fa-map-marker"></i>
					<span class="location-text">
						{{ request('from_location') }} to {{ request('to_location') }}
					</span>
				</span>
			</div>
			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">From Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{{ request('valid_from') }}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">To Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>								
                       @if( request()->has('valid_to') )
						{{ request('valid_to') }}
                       @else
                         NA
                       @endif
					</span>
				</div>
			</div>
			<div>
				<p class="search-head">Post For</p>
				<span class="search-result">
				@if( request('post_type')==1 )
					HHG 
				@else 
					Vehicle
				@endif
				</span>
			</div>

			<div class="search-modify" data-toggle="modal" data-target="#modify-search">
				<span>Modify Search +</span>
			</div>
		</div>
		<!-- Search Block Ends Here -->

		<h2 class="side-head pull-left">Filter Results </h2>
		<div class="page-results pull-left col-md-2 padding-none">
			<div class="form-control-fld">
				<div class="normal-select">
					<select class="selectpicker">
						<option value="0">10 Records Per page</option>
					</select>
				</div>
			</div>
		</div>
		
		<a onclick="return checkSession(15,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

		<div class="clearfix"></div>
				
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				
				<!-- Left Section Start Here -->
				<div class="main-left">
				{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
					{!! Form::hidden('from_location_id', $from_location_id) !!}
					{!! Form::hidden('to_location_id', $to_location_id) !!}
					{!! Form::hidden('from_location', $from_location) !!}
					{!! Form::hidden('to_location', $to_location) !!}
					{!! Form::hidden('valid_from', $valid_from) !!}
					{!! Form::hidden('valid_to', $valid_to) !!}
					{!! Form::hidden('post_type', $post_type) !!}

				<h2 class="filter-head">Form Filter</h2>
				<div class="seller-list inner-block-bg">
					
					<div class="form-control-fld margin-top">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							<select class="selectpicker">
								<option>Enquiry Type (All)</option>
							</select>
						</div>
					</div>
					
					<div class="form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('from_location', $from_location , ['id' => '','class' => 'form-control', 'placeholder' => 'From Location*','readonly'=>'true']) !!}
						</div>
					</div>
					
					<div class="form-control-fld">
						<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('to_location', $to_location,  ['id' => '','class' => 'form-control', 'placeholder' => 'To Location*','readonly'=>'true']) !!}
						</div>
					</div>

					<div class="form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							<input type="text" class="form-control" placeholder="Dispatch Date" value="{{ $valid_from }}" readonly />
						</div>
					</div>

					<div class="form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							<input type="text" class="form-control" placeholder="Delivery Date" value="{{ $valid_to }}" readonly />
						</div>
					</div>
								
					@if( request('post_type') ==1)
					<?php $load_type = isset($_REQUEST['load_type']); ?>
					@if(session()->has('show_layered_filter') && session('show_layered_filter')!="")	
					@if (session()->has('layered_filter_loadtype')&& session('layered_filter_loadtype')!="")								
					<div class="form-control-fld margin-top">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							<select class="selectpicker" name="load_type" onChange="this.form.submit()" >
							<option value="">Load Type</option>
							@foreach (session('layered_filter_loadtype') as $loadtypeId => $loadtypeName)										
								<option <?php if (isset ( $_REQUEST ['load_type'] ) && $_REQUEST ['load_type'] == $loadtypeId) { ?> selected="selected" value="{{$loadtypeId}}" <?php } else { ?>value="{{$loadtypeId}}"<?php } ?>   > {{ $loadtypeName }}</option>
							@endforeach
							</select>
						</div>
					</div>		
					@endif
					@endif	
							
					<?php $propertType = isset($_REQUEST['property_type'])  ?>
					@if (session()->has('show_layered_filter')&& session('show_layered_filter')!="")	
						@if (session()->has('layered_filter_propertytype')&& session('layered_filter_propertytype')!="")								
							<div class="form-control-fld margin-top">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									<select class="selectpicker" name="property_type" onChange="this.form.submit()" >
									<option value="">Property Type</option>
									@foreach (session('layered_filter_propertytype') as $propId => $propName)										
										<option <?php if (isset ( $_REQUEST ['property_type'] ) && $_REQUEST ['property_type'] == $propId) { ?> selected="selected" value="{{$propId}}" <?php } else { ?>value="{{$propId}}"<?php } ?> > {{ $propName }}</option>
									@endforeach
									</select>
								</div>
							</div>		
							@endif
						@endif	
					@endif
						
					<?php $selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
					?>		
					@if (session()->has('show_layered_filter')&& session('show_layered_filter')!="")
						@if (session()->has('layered_filter')&& session('layered_filter')!="")
							<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
							<div class="seller-list inner-block-bg">
								@if(session()->has('layered_filter') && is_array(session('layered_filter')))
									@foreach (session('layered_filter') as $userId => $userName)
										<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
										<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
										<div class="col-xs-12 padding-none"> </div>
									@endforeach
								@endif
							</div>
						@endif
					@endif
						
				</div>		
				{!! Form::close() !!}
							
				</div>
				<!-- Left Section Ends Here -->

				<div class="main-right">
					<div class="table-div">	
						<input type="hidden" id="from_search_page" name="from_search_page" value="1">							
						{!! $gridBuyer !!}
					</div>	
				</div>
				<!-- Right Section Ends Here -->

			</div>
		</div>

		<div class="clearfix"></div>
		<a onclick="return checkSession(15,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>
	</div>
</div>
@include('partials.footer')

<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
			  	<div class="col-md-12 modal-form">

					<div class="home-search-modfy">
						
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">
								<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ request('lead_type')==1? 'checked="checked"':'' }} /><label for="spot_lead_type"><span></span>Spot</label></div>
								<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" {{ request('lead_type')==2? 'checked="checked"':'' }} /><label for="term_lead_type"> <span></span>Term</label></div>
							</div>
						</div>

						@include('seller.relocation.home.domestic.search._form')
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection