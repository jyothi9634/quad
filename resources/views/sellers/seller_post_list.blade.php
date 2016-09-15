<?php die;?>
@extends('app')
@section('content')
<div class="main-container">	
		<div class="container container-inner">
			<!-- Left Nav Starts Here -->
			@include('partials.seller_leftnav')
			<!-- Left Nav Ends Here -->
			<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
					<div class="alert alert-info">
						{{Session::get('message_update_post')}}
					</div>
				@endif
				<div class="block">
					<div class="tab-nav underline">
						@include('partials.page_top_navigation')
					</div>
					
					<div class="col-md-12 col-sm-12 col-xs-12 padding-top">
						
						
						<div class="gray_bg">
							<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
								<select class="selectpicker">
									<option>Leads (My Posts)</option>
									<option>Leads (Market Leads)</option>
									
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<select class="selectpicker">
									<option>Lead Type (Spot)</option>
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<select class="selectpicker">
									<option>Service Type (FTL)</option>
								</select>
							</div>
								<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<select class="selectpicker">
									<option>Status</option>
									<option>Open</option>
									<option>Closed</option>
									<option>Draft</option>
									<option>Cancelled</option>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="gray_bg">

							
							<div class="col-md-4 col-sm-4 col-xs-12 padding-none">
								<select class="selectpicker">
									<option>From Location</option>
								</select>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">
								<select class="selectpicker">
									<option>To Location</option>
								</select>
								
							</div>
							<div class="clearfix"></div>
							
							
							<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
								<select class="selectpicker">
									<option>Vehicle Type</option>
									<option>Vehicle Type</option>
									<option>Vehicle Type</option>
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<select class="selectpicker">
									<option>Load Type</option>
									<option>Load Type</option>
									<option>Load Type</option>
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<input type="text" placeholder="From" class="calendar">
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">
								<input type="text" placeholder="To" class="calendar">
							</div>
							
						</div>
						<div class="clearfix"></div>
						
						<div class="table-top col-md-12 col-sm-12 col-xs-12 padding-none"><input type="checkbox"> 
							<a href="#">Select All</a> <a href="#">Cancel</a> 
							<span class="pull-right">
								View 1-50
							</span>
						</div>
						



					</div>
					
					<div class="table table-head text-center" width="100%">
						<div class="col-md-1 col-sm-1 col-xs-5 padding-left-none">Post ID</div>
						<div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">Valid From</div>
						<div class="col-md-3 col-sm-3 col-xs-5 padding-left-none">Valid To</div>
						<div class="col-md-3 col-sm-3 col-xs-5 padding-right-none">Post Visibility</div>
						<div class="col-md-2 col-sm-2 col-xs-4 padding-none text-right pull-right">Status</div>
						
					</div>
					{{--*/ $i =0 /*--}}
					@foreach($seller_post as $sellerpostvalue)
						
					{{--*/ $i = $i+1 /*--}}
					<div class="table-data text-center">
						<div class="col-md-12 col-sm-12 col-xs-12 padding-left-none padding-right-none table-row">
						<div class="col-md-1 col-sm-1 col-xs-5 padding-left-none"><input type="checkbox" /> {!! $i !!}</div>
							<a href='/sellerpostdetail/{{ $sellerpostvalue->id}}'>
							<div class="col-md-3 col-sm-3 col-xs-5 padding-none text-left">
								{!! $sellerpostvalue->from_date !!}
							</div>
							<div class="col-md-3 col-sm-3 col-xs-3 padding-none">{!! $sellerpostvalue->to_date !!}</div>
							<div class="col-md-3 col-sm-3 col-xs-5 padding-right-none">Public</div>
							<div class="col-md-2 col-sm-2 col-xs-12 padding-none text-right pull-right">open</div>
							</a>
								<div class="clearfix"></div>
								<div class="col-md-12 col-sm-12 col-xs-12 pull-right padding-none">
									<div class="col-md-2 col-sm-2 col-xs-5 padding-none margin-top text-center">
										<a href="#">
											<div class="margin-center">
												<i class="fa fa-envelope"></i> 
												<span class="red superscript-table">9</span>
											</div>
											Messages
										</a>
									</div>
									<div class="col-md-2 col-sm-2 col-xs-5 padding-none margin-top text-center">
										<a href="#">
											<div class="margin-center">
												<i class="fa fa-file-text-o"></i> 
												<span class="red superscript-table">9</span>
											</div>
											Enquiries
										</a>
									</div>
									
									<div class="col-md-3 col-sm-3 col-xs-5 padding-none margin-top text-center">
										<a href="#">
											<div class="margin-center">
												<i class="fa fa-bar-chart-o"></i> 
												<span class="red superscript-table">9</span>
											</div>
											Market Analytics
										</a>
									</div>
									<div class="col-md-2 col-sm-2 col-xs-5 padding-none margin-top text-center">
										
										<div class="margin-center">
											
											<span class="red superscript-table">9</span>
										</div>
										Views
										
									</div>
									<div class="col-md-1 col-sm-1 col-xs-5 padding-right-none margin-top text-right pull-right underline_link">

										<a href="#">edit</a>
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			
			<!-- Right Starts Here -->
			@include('partials.seller_rightnav')
			<!-- Right Ends Here -->
		</div>
	</div>
</div>
@endsection
