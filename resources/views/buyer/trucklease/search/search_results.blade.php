@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content') 
@include('partials.page_top_navigation')

<div class="main">
<div class="container">

	<h1 class="page-title">Search Results (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
	{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}				
				
	<div class="search-block inner-block-bg">
		
		<div class="from-to-area">
			<span class="search-result">
				<i class="fa fa-map-marker"></i>
				<span class="location-text">
					{!! Form::hidden('from_location_id', $from_location_id) !!}
					{!! Form::hidden('from_location', $from_location) !!}
                    {!! Form::hidden('delivery_flexible_hidden', session('searchMod.fdelivery_date_buyer')) !!}
                    {!! Form::hidden('dispatch_flexible_hidden', session('searchMod.fdispatch_date_buyer')) !!}
                     {!! Form::hidden('is_commercial', session('searchMod.is_commercial_date_buyer')) !!}
					{{$from_location}}
				</span>
			</span>
		</div>

		<div class="date-area">
			<div class="col-md-6 padding-none">
				<p class="search-head">From</p>
				<span class="search-result">
					<i class="fa fa-calendar-o"></i>
					{!! Form::hidden('from_date', session('searchMod.dispatch_date_buyer'), ['id' => 'from_date','class' => 'form-control']) !!}
					{{$fdispatch}}
				</span>
			</div>
			<div class="col-md-6 padding-none">
				<p class="search-head">To</p>
				<span class="search-result">
					<i class="fa fa-calendar-o"></i>
					{!! Form::hidden('to_date', session('searchMod.delivery_date_buyer'), ['id' => 'to_date','class' => 'form-control']) !!}
					@if($to_date!= '') {{$fdelivery}} @else NA @endif
				</span>
			</div>
		</div>

		<div>
			<p class="search-head">Vehicle Type</p>
			<span class="search-result">
                {!! Form::hidden('lkp_vehicle_type_id', $lkp_vehicle_type_id, ['id' => 'vehicle_type']) !!}
				{{$vehicle_type_name}}
			</span>
		</div>

		<div>
			<p class="search-head">Lease Term</p>
			{!! Form::hidden('lkp_trucklease_lease_term_id', $lkp_trucklease_lease_term_id, ['id' => 'load_type','class' => 'form-control']) !!}
			<span class="search-result">{{$lease_type_name}}</span>
		</div>

		<div>
			<p class="search-head">Driver</p>
			{!! Form::hidden('driver_availability', $driver_availability_id, ['id' => 'driver_availability','class' => 'form-control']) !!}
			<span class="search-result">{{$driver_availability_text}}</span>
		</div>

		<div class="search-modify" data-toggle="modal" data-target="#modify-search">
			<span>Modify Search +</span>
		</div>

	</div> 
	<!-- Search block -->

	<h2 class="side-head pull-left">Filter Results</h2>
	<!--button class="btn post-btn pull-right">Post & get Quote</button-->
	@include("partials.content_top_navigation_links")

	<div class="clearfix"></div>

	<div class="col-md-12 padding-none">
		<div class="main-inner">

			<!-- Left Section Starts Here -->
			<div class="main-left">
				<input type="hidden" id="driver_availability" name="driver_availability" 
					value="<?php echo request()->exists('driver_availability')? request('driver_availability'):1; ?>" />
	            
	            @if( ( session()->has('show_layered_filter') && session('show_layered_filter')!="" ) || ( !request()->exists('is_search') ) )
					@include("partials.filter._price")							
	            @endif     
				<?php
				$selectedPayment = request()->exists('selected_payments')? request('selected_payments'): array();
				?>
				@if (Session::has('show_layered_filter')&& session('show_layered_filter')!="")
					@if (Session::has('layered_filter_payments')&& session('layered_filter_payments')!="")
						<h2 class="filter-head">Payment Mode</h2>
						<div class="payment-mode inner-block-bg">
							@foreach (session('layered_filter_payments') as $paymentId => $paymentName)
							<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
							<div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8"> 
	                            @if ($paymentName == 'Advance') 
	                            {{--*/ $paymentType = 'Online Payment' /*--}}
	                            @else
	                            {{--*/ $paymentType = $paymentName /*--}}
	                            @endif
	                            {{$paymentType}}
	                        	</span>
	                        </div>
							@endforeach
						</div>
					@endif
				@endif

				<div class="tracking inner-block-bg">
					<div class="check-box"><input type="checkbox" name="ftltopseller_orders"><span class="lbl padding-8">Top Sellers (Orders) </span></div>
					<div class="check-box"><input type="checkbox" name="ftltopseller_rated"><span class="lbl padding-8">Top Sellers (Rated) </span></div>
				</div>

				<?php
				$selectedSellers = request()->exists('selected_users')? request('selected_users'):array();
				?>
				@if (Session::has('show_layered_filter') && session('show_layered_filter')!="")
					@if (Session::has('layered_filter') && session('layered_filter')!="")
					<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
					<div class="seller-list inner-block-bg">
						@foreach (session('layered_filter') as $userId => $userName)
						<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
						<div class="check-box"><input type="checkbox"  class="filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onClick="this.form.submit()" /><span class="lbl padding-8"> {{ $userName }}</span></div>
						@endforeach
					</div>
					@endif
				@endif

				<?php if( request()->exists('from_date') || request()->exists('to_date') ) { ?>
					<div class="clearfix"></div>
				<?php } ?>

			</div> 
			<!-- Left Filters section -->

			<!-- Wrong placement of the filter close, but loop has to be end here. -->
			<div class="main-right">
				<div class='table-data table-div' id="booknow_buyer_form">
					{!! $gridBuyer !!}
				</div>
			</div>
	
		</div>
	</div>
	
	{!! Form::close() !!}

</div>
</div>

@include('partials.footer')

<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        
	        {!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form_modify' , 'autocomplete'=>'off','method'=>'get']) !!}
	        
	        <div class="modal-body">
	          	<div class="col-md-12 padding-none">
	          	
		          	@include('buyer.trucklease.search._form')                                
                                                         
				</div>
			</div>

			<div class="container">
				<div class="col-md-4 col-md-offset-4">
					{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}
				</div>
			</div>

	        {!! Form::close() !!}

  		</div>
	</div>
</div>
@endsection