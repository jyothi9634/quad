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
					<span class="location-text">{{ request('from_location') }}</span>
				</span>
			</div>
			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Dispatch Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{{ request('valid_from') }}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">Delivery Date</p>
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
			<div></div>

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

		<a onclick="return checkSession(20,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>
		<div class="clearfix"></div>
				
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
						
				<!-- Left Section Starts Here -->
				<div class="main-left">
					{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
					{!! Form::hidden('from_location_id', $from_location_id) !!}
					{!! Form::hidden('from_location', $from_location) !!}
					{!! Form::hidden('valid_from', $valid_from) !!}
					{!! Form::hidden('valid_to', $valid_to) !!}
					
					<div class="seller-list inner-block-bg">
						<div class="form-control-fld margin-top">

							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('from_location', $from_location , ['id' => '','class' => 'form-control', 'placeholder' => 'From Location*','readonly'=>'true']) !!}
							</div>
						</div>
						<div class="form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input type="text" class="form-control"  placeholder="Dispatch Date" value="{{ $valid_from }}" onChange="this.form.submit()" readonly />
							</div>
						</div>
						<div class="form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input type="text" class="form-control" placeholder="Delivery Date" value="{{ $valid_to }}" onChange="this.form.submit()" readonly />
							</div>
						</div>
					</div>
					{{--*/
						$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
					/*--}}
						@if (session()->has('show_layered_filter')&& session('show_layered_filter')!="")
							@if (session()->has('layered_filter')&& session('layered_filter')!="")
								<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
								<div class="seller-list inner-block-bg">
									@foreach (session('layered_filter') as $userId => $userName)
										<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
										<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ ucwords($userName) }}</span></div>
										<div class="col-xs-12 padding-none"> </div>
									@endforeach
								</div>
							@endif
						@endif
				{!! Form::close() !!}
				</div>
				<!-- Left Section Ends Here -->

				<!-- Right Section Starts Here -->
				<div class="main-right">
					<!-- Table Starts Here -->
					<div class="table-div">	
					<input type="hidden" id="from_search_page" name="from_search_page" value="1">							
						{!! $gridBuyer !!}
					</div>	
				</div>
				<!-- Right Section Ends Here -->

			</div>
		</div>

		<div class="clearfix"></div>
		<a onclick="return checkSession(20,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

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
						@include('seller.relocation.office.domestic.search._form')
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection