
@extends('app')

@section('content')
@inject('commoncomponent', 'App\Components\CommonComponent')
<div class="container container-inner">
	<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
	<!-- Left Nav Ends Here -->
	<!-- Page Center Content Starts Here -->
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
			{!! Form::open(['url' => 'sellerposts/'.$postId,'id'=>'seller_posts_search']) !!}
			<div class="col-md-12 col-sm-12 col-xs-12 padding-top">


				<div class="gray_bg">

						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! Form::select('status', (['' => 'Status'] + $posts_status_list), $statusSelected , ['class' => 'selectpicker','id' => 'posts_status']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! Form::submit(' GO ', ['class' => 'btn','name' => 'go','id' => 'go_seller_search']) !!}
					</div>
					</div>

					<div class="clearfix"></div>
				</div>
				{!! Form::close() !!}
				<div class="gray_bg">

					{!! $filter->open !!}
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
						{!! $filter->field('spi.from_location_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! $filter->field('spi.to_location_id') !!}

					</div>
					<div class="clearfix"></div>


					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
						{!! $filter->field('spi.lkp_vehicle_type_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						{!! $filter->field('spi.lkp_load_type_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						<div class="form-group">{!! $filter->field('sp.from_date') !!}</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
						<div class="form-group">{!! $filter->field('sp.to_date') !!}</div>
					</div>



					{!! $filter->close !!}
				</div>
				<div class="clearfix"></div>
				<div class="col-md-9 col-sm-8 col-xs-12 padding-none">
					<h5>Spot Transaction</h5>
					<h5>
						<div class="col-md-6 col-sm-6 col-xs-12 padding-none">Full Truck Load Post ID </div>
						<div class="col-md-6 col-sm-6 col-xs-12 padding-none">{!! $seller_post[0]->transaction_id !!}</div>
					</h5>
					<div class="col-md-6 col-sm-6 col-xs-12 padding-none">
						<p>Valid From</p>
						<p>Valid to</p>

					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 padding-none">


						<p>{!! date('d/m/Y', strtotime($seller_post[0]->from_date)) !!}</p>
						<p>{!! date('d/m/Y', strtotime($seller_post[0]->to_date)) !!}<span class="pull-right spot_transaction_details hidden-xs">Details <span class="show_details">+</span><span class="hide_details" style="display: none;">-</span></span>
						</p>

					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 padding-none spot_transaction_details_view">
						<div class="col-md-6 col-sm-6 col-xs-12 padding-none">

							<p>Payment</p>
							<p>Tracking</p>
							<p>Documents</p>

						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 padding-none">

							<p>{!! $payment[0]->payment_mode !!} <i class="fa fa-credit-card"></i> Online</p>
							<p>								
                                   {{ $commoncomponent->getTrackingType($seller_post[0]->tracking) }}
                                   </p>
							<p><i class="fa fa-file-text"></i>&nbsp;<sup>0</sup></p>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>


				<input type="hidden"  value="{{$postId}}" class="blindHidden" id="seller-post-id">
				<div class="table-top col-md-12 col-sm-12 col-xs-12 padding-none"><input type="checkbox">
					<a href="#">Select All</a> <a id="cancel-seller-post" class="cursor-hover">Cancel</a>
					<span class="pull-right">
						View 1-50
					</span>
				</div>

			</div>
			{!! $grid !!}


			</div>

	<!-- Page Center Content Ends Here -->
	<!-- Right Starts Here -->
		@include('partials.right')
	<!-- Right Ends Here -->
</div>
@endsection

