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

	@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
			<div class="alert alert-info">
				{{Session::get('message_update_post')}}
			</div>
	@endif 

	<div class="container">
	<!-- Content top navigation Starts Here-->
        @include('partials.content_top_navigation_links')
        <!-- Content top navigation ends Here-->
	<div class="clearfix"></div>

	<span class="pull-left"><h1 class="page-title">Spot Transaction - {!! $seller_post[0]->transaction_id !!}</h1></span>
	<span class="pull-right text-right">
		<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $allcountview }}</a>
		@inject('commonComponent', 'App\Components\CommonComponent')
		{{--*/ $SellerPostDelete=$commonComponent->getSellerPostDelete($seller_post[0]->id) /*--}}
		@if($SellerPostDelete != 0)
		<a href="/updateseller/{!! $seller_post_id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
		@endif
		{{--*/  $deletestr = array() /*--}}
		{{--*/  $deleteqry = '' /*--}}
		@foreach($seller_post_items as $spi)
		{{--*/ $deletestr[]=$spi->id /*--}}
		@endforeach
		{{--*/ $deleteqry = implode(',',$deletestr) /*--}}
		
		<!-- a href="javascript:sellerpostcancel('items','{{$deleteqry}}')" class="delete-icon"><i class="fa fa-trash red" title="Delete"></i></a -->

		<!-- <a href="#" class="back-link1" onclick="history.go(-1);">Back to Posts</a>-->
		<a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a>
	</span>
	
 	<!-- Search Block Starts Here -->

	<div class="filter-expand-block">

		<div class="search-block inner-block-bg margin-bottom-less-1">

			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Valid From</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{!! date('d/m/Y', strtotime($seller_post[0]->from_date)) !!}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">Valid To</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{!! date('d/m/Y', strtotime($seller_post[0]->to_date)) !!}
					</span>
				</div>
			</div>
			<div>
				<p class="search-head">Payment</p>
				<span class="search-result">
					@if($seller_post[0]->lkp_payment_mode_id == 1)
						{{--*/ $payment_type = 'Advance'; /*--}}
						@if($seller_post[0]->accept_payment_netbanking == 1)
							{{--*/ $payment_type .= ' | NEFT/RTGS'; /*--}}
						@endif
						@if($seller_post[0]->accept_payment_credit == 1)
							{{--*/ $payment_type .= ' | Credit Card'; /*--}}
						@endif
						@if($seller_post[0]->accept_payment_debit == 1)
							{{--*/ $payment_type .= ' | Debit Card'; /*--}}
						@endif
					@elseif($seller_post[0]->lkp_payment_mode_id == 2)
						{{--*/ $payment_type = 'Cash on delivery'; /*--}}
					@elseif($seller_post[0]->lkp_payment_mode_id == 3)
						{{--*/  $payment_type = 'Cash on pickup'; /*--}}
					@else
						{{--*/  $payment_type = 'Credit'; /*--}}
						@if($seller_post[0]->accept_credit_netbanking == 1)
							{{--*/ $payment_type .= ' | Net Banking'; /*--}}
						@endif
						@if($seller_post[0]->accept_credit_cheque == 1)
							{{--*/ $payment_type .= ' | Cheque'; /*--}}
						@endif
						
						{{--*/ $payment_type .= ' | '; /*--}}
						{{--*/ $payment_type .= $seller_post[0]->credit_period; /*--}}
						{{--*/ $payment_type .= ' '; /*--}}
						{{--*/ $payment_type .= $seller_post[0]->credit_period_units ; /*--}}
						
					@endif

					{!! $payment_type !!}
				</span>
			</div>
			<div>
				<p class="search-head">					
                          {{ $commonComponent->getQuoteAccessById($seller_post[0]->lkp_access_id) }}
				</p>
				<span class="search-result">
				@if($seller_post[0]->lkp_access_id == 1)
				Yes
				@else
					@foreach($sellerselectingbuyers as $privatebuyerdetails)
						{{ $privatebuyerdetails->username }}  |
					@endforeach
				@endif
				</span>
			</div>
			<div>
				<p class="search-head">Tracking</p>
				<span class="search-result">					
                         {{ $commonComponent->getTrackingType($seller_post[0]->tracking) }}
				</span>
			</div>						
			<div class="text-right filter-details">
				<div class="info-links">
					<a class="transaction-details-expand"><span class="show-icon">+</span>
						<span class="hide-icon">-</span> Details
					</a>
				</div>
			</div>

		</div>

		<!-- Search Block Ends Here -->

		<!--toggle div starts-->
		<div class="show-trans-details-div-expand trans-details-expand"> 
		   	<div class="expand-block">
		   		<div class="col-md-12">
		   			@if($seller_post[0]->cancellation_charge_price != "" && $seller_post[0]->cancellation_charge_price != "0.00")
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Cancellation Charges</span>
							<span class="data-value">{!! $commonComponent->getPriceType($seller_post[0]->cancellation_charge_price) !!}</span>
						</div>
					@endif	
					@if($seller_post[0]->docket_charge_price != "" && $seller_post[0]->docket_charge_price != "0.00")
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Docket Charges</span>
							<span class="data-value">{!! $commonComponent->getPriceType($seller_post[0]->docket_charge_price) !!}</span>
						</div>
					@endif
					@if($seller_post[0]->other_charge1_price != "" && $seller_post[0]->other_charge1_price != "0.00")
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{!! $seller_post[0]->other_charge1_text !!}</span>
							<span class="data-value">{!! $commonComponent->getPriceType($seller_post[0]->other_charge1_price) !!}</span>
						</div>
					@endif
					@if($seller_post[0]->other_charge2_price != "" && $seller_post[0]->other_charge2_price != "0.00")
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{!! $seller_post[0]->other_charge2_text !!}</span>
							<span class="data-value">{!! $commonComponent->getPriceType($seller_post[0]->other_charge2_price) !!}</span>
						</div>
					@endif
					@if($seller_post[0]->other_charge3_price != "" && $seller_post[0]->other_charge3_price != "0.00")
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{!! $seller_post[0]->other_charge3_text !!}</span>
							<span class="data-value">{!! $commonComponent->getPriceType($seller_post[0]->other_charge3_price) !!}</span>
						</div>
					@endif	
					<div class="col-md-2 padding-left-none data-fld">
						<span class="data-head">Documents</span>
						<span class="data-value">
						{{--*/ $serviceId = Session::get('service_id') /*--}}
{!! count($commonComponent->getGsaDocuments(SELLER,$serviceId,$seller_post[0]->id)) !!}
						</span>
					</div>


					<div class="clearfix"></div>
					
					@if($seller_post[0]->terms_conditions != "")
					<div class="col-md-12 padding-left-none data-fld">
						<span class="data-head">Terms & Conditions</span>
						<span class="data-value">{!! $seller_post[0]->terms_conditions !!}</span>
					</div>
					@endif	
				</div>
				<div class="clearfix"></div>
			</div>
  		</div>
		<!--toggle div ends-->
	</div>

	 <!-- Search Block Ends Here -->
	

	<!--  Filters start -->
	
	<div class="filter-expand-block">
		<div class="gray-bg">
			<div class="col-md-12 padding-none filter">
				{!! Form::open(['url' => 'sellerposts/'.$postId,'id'=>'seller_posts_search']) !!}
				<div class="col-md-3 form-control-fld">
					<div class="normal-select">
                               {{--*/ unset($posts_status_list[1]) /*--}}
						{!! Form::select('status', (['' => 'Status (All)'] + $posts_status_list), $statusSelected , ['class' => 'selectpicker','id' => 'posts_status']) !!}
					</div>
				</div>
				<div class="col-md-9 form-control-fld">
					{!! Form::submit(' GO ', ['class' => 'btn add-btn pull-right','name' => 'go','id' => 'go_seller_search']) !!}
				</div>
				<div class="clearfix"></div>
				
				{!! Form::close() !!}
				
	
				</div>
			</div>
			<div class="gray-bg">
				
				{!! $filter->open !!}
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
	
	
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
					<span class="add-on"><i class="fa fa-archive"></i></span>
					{!! $filter->field('spi.lkp_load_type_id') !!}
					</div>
				</div>
	
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
					<span class="add-on"><i class="fa fa-truck"></i></span>
					{!! $filter->field('spi.lkp_vehicle_type_id') !!}
					</div>
				</div>

				<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										
			                            @if(isset($_GET['vehicle_number']))
			                            {!! Form::text('vehicle_number', $_GET['vehicle_number'],['id' => 'vehicle_number','class'=>'form-control form-control1 clsVehicleno', 'placeholder' => 'Vehicle number']) !!}
			                            @else
			                            {!! Form::text('vehicle_number', '',['id' => 'vehicle_number','class'=>'form-control form-control1 clsVehicleno', 'placeholder' => 'Vehicle number']) !!}
			                            @endif
                                        <span class="input-group-addon cursor-hover searchSubmit"><i class="fa fa-search"></i></span>                                        
									</div>
								</div>
	
	
				{!! $filter->close !!}
			</div>
		</div>
				<div class="clearfix"></div>
		<!-- End Filter -->
	
	<div class="col-md-12 padding-none">
		<div class="main-inner"> 
			
			
			<!-- Right Section Starts Here -->

			<div class="main-right">

				
				<!-- Table Starts Here -->

				<div class="table-div">
					
					{!! $grid !!}	
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

<!-- Modal -->
	  <div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">
	          <div class="col-md-12 modal-form">
				<div class="col-md-4 padding-none">
					<div class="col-md-12 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-user"></i></span>
							<select class="selectpicker">
								<option value="0">Select Service</option>
								<option value="1">Full Truck (FTL)</option>
								<option value="2">Full Truck (LTL)</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-8 padding-none">
					<div class="col-md-6 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							<input class="form-control" id="" type="text" placeholder="From Location">
						</div>
					</div>
					<div class="col-md-6 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							<input class="form-control" id="" type="text" placeholder="To Location">
						</div>
					</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input class="form-control" id="" type="text" placeholder="Dispatch Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input class="form-control" id="" type="text" placeholder="Delivery Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span>
								<select class="selectpicker">
									<option value="0">Select Load Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="col-md-6 form-control-fld padding-left-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text" placeholder="Qty">
								</div>
							</div>
							<div class="col-md-6 form-control-fld padding-right-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text" placeholder="Capacity">
								</div>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span>
								<select class="selectpicker">
									<option value="0">Select Vehicle Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<img src="../images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions *</span>
						</div>

						<div class="col-md-6 form-control-fld">
							<button class="btn theme-btn btn-block">Search</button>
						</div>
				</div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	  </div>

@endsection
