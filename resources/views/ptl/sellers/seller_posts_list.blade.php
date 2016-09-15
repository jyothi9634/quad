@extends('app')
@section('content')
@include('partials.page_top_navigation')

{{--*/ $strAll =  explode("&search=",URL::previous())  /*--}}
@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@elseif(isset($strAll[1]) && $strAll[1] == 1 && $strAll[1]!='')
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@elseif(!str_contains("sellerlist",URL::previous()))
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

<div class="main">

	<div class="container">
		@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
			<div class="alert alert-info">
				{{Session::get('message_update_post')}}
			</div>
		@endif

		@include('partials.content_top_navigation_links')
		<div class="clearfix"></div>
		
	{{--*/ $currentpagename = Request::segment(1) /*--}}

		<span class="pull-left"><h1 class="page-title">Spot Transaction - {{ $transactionid }}</h1></span>
		@if($currentpagename!='buyermarketleads')
		<span class="pull-right">
			<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $allcountview }}</a>
			@inject('commonComponent', 'App\Components\CommonComponent')
			{{--*/ $SellerPostDelete=$commonComponent->getSellerPostDelete($seller_post_id) /*--}}
			@if($SellerPostDelete != 0)
			<a href="/ptl/updatesellerpost/{!! $seller_post_id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
			@endif
				{{--*/  $deletestr = array() /*--}}
				{{--*/  $deleteqry = '' /*--}}
				@foreach($seller_post_items as $spi)
				{{--*/ $deletestr[]=$spi->id /*--}}
				@endforeach
				{{--*/ $deleteqry = implode(',',$deletestr) /*--}}
		
		<!-- a href="javascript:sellerpostcancel('items','{{$deleteqry}}')" class="delete-icon"><i class="fa fa-trash red" title="Delete"></i></a -->
			
			<!-- a href="/sellerlist" class="back-link1" onclick="history.go(-1);">Back to Posts</a> -->
			
			<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
			
		</span>
		@endif
		
		<div class="filter-expand-block">
			<div class="filter-expand-block">
				<div class="col-md-12 padding-none filter">
				{!! $postdetails !!}
				</div>
			</div>	
			@if((Session::get('service_id') == ROAD_PTL) || (Session::get('service_id') == RAIL) || (Session::get('service_id') == COURIER) || (Session::get('service_id') == AIR_DOMESTIC) || (Session::get('service_id') == AIR_INTERNATIONAL) || (Session::get('service_id') == AIR_INTERNATIONAL) || (Session::get('service_id') == OCEAN) )
			{!! Form::open(['url' => 'sellerposts/'.$postId,'id'=>'seller_posts_search']) !!}
				<div class="gray-bg">
					<div class="col-md-12 padding-none filter">
						<div class="col-md-3 form-control-fld">
							<div class="normal-select">
								<select name="status" id="posts_status" class="selectpicker">
									<option value="" selected="selected">Status (All)</option>
									<option <?php if (isset ( $_REQUEST ['status'] ) && $_REQUEST ['status'] == 2) { ?> selected="selected" value="2" <?php } else { ?>value="2"<?php } ?> >Open</option>									
									<option <?php if (isset ( $_REQUEST ['status'] ) && $_REQUEST ['status'] == 5) { ?> selected="selected" value="5" <?php } else { ?>value="5"<?php } ?> >Deleted</option>
									<option <?php if (isset ( $_REQUEST ['status'] ) && $_REQUEST ['status'] == 3) { ?> selected="selected" value="3" <?php } else { ?>value="3"<?php } ?> >Closed</option>
								</select>																	
							</div>
						</div>
						<div class="col-md-9 form-control-fld">
							<!-- button class="btn add-btn pull-right">Filter</button -->
							{!! Form::submit(' GO ', ['class' => 'btn add-btn pull-right','name' => 'go','id' => 'go_seller_search']) !!}
						</div>
					</div>
				</div>
			{!! Form::close() !!}
			
			<div class="gray-bg">
				{!! $filter->open !!}
					<div class="col-md-12 padding-none filter">
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
					</div>
				{!! $filter->close !!}
			</div>
				
				
			@else	
				<div class="gray-bg">
					<div class="col-md-12 padding-none filter">

						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<input type="text" class="form-control form-control1" placeholder="{{ Session::get('postedType') }}" value="{{ Session::get('postedType') }}" readonly="true">
							</div>
						</div>
	
						{!! $filter->open !!}	
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
						{!! $filter->close !!}	
					</div>
				</div>				
			@endif			
				
			


			<!--toggle div ends-->
		</div>

		 <!-- Search Block Ends Here -->
		
		
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				<div class="main-right">
					<div class="table-div">
						<div class="table-data">
							{!! $grid !!}
						</div>
					</div>	
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

	</div>
</div>


<!-- Modal -->

@include('partials.footer')
@endsection