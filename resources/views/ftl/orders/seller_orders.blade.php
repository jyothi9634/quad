@extends('app') @section('content')


<div class="main-container">
	<div class="container container-inner">
		<!-- Left Nav Starts Here -->
	@include('partials.seller_leftnav')
	<!-- Left Nav Ends Here -->
		<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

			<div class="block">
				<div class="tab-nav underline">
					@include('partials.page_top_navigation')
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12 padding-top">

					<div class="gray_bg">


						{!! Form::open(array('url' => 'sellerorderSearch', 'id'
						=>'seller-order-search', 'class'=>'form-inline' )) !!}

						<div class="col-md-3 col-sm-3 col-xs-12 padding-none">{!!
							Form::select('lkp_order_type_id',array('' => 'Select Order Type')
							+ $order_types,$order_type
							,['class'=>'selectpicker','id'=>'order_types']) !!}</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
							Form::select('service_id',array('' => 'Service Type (All)') +
							$services,$service_id
							,['class'=>'selectpicker','id'=>'service_offered']) !!}</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
							Form::select('status_id',array('' => 'Status (All)') +
							$status,$order_status,['class'=>'selectpicker','id'=>'post_status'])
							!!} </div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
								<div class="form-group">{!! Form::submit('Go', array( 'class'=>'btn 
							')) !!} {!! Form :: close() !!}</div>
							</div>
							 

						<div class="clearfix"></div>
					</div>

					{!! $filter->open !!}
					 {!! $filter->field('src') !!}
					<div class="gray_bg">
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none">{!!
							$filter->field('orders.from_city_id') !!}</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
							$filter->field('orders.to_city_id') !!}</div>

						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
							<div class="form-group">
							@if(isset($_GET['start_dispatch_date']))
                                                        {!! Form::text('start_dispatch_date', $_GET['start_dispatch_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From','readonly' => true]) !!}
                                                        @else
                                                        {!! Form::text('start_dispatch_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From','readonly' => true]) !!}
                                                        @endif
                                                        </div>
							</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
							<div class="form-group">
							@if(isset($_GET['end_dispatch_date']))
                                                        {!! Form::text('end_dispatch_date', $_GET['end_dispatch_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To','readonly' => true]) !!}
                                                        @else
                                                        {!! Form::text('end_dispatch_date','',['id' => 'end_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'To','readonly' => true]) !!}
                                                        @endif
                                                        </div>
							</div>
						<div class="clearfix"></div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none">{!!
							$filter->field('orders.buyer_consignor_name') !!}</div>

						<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
							$filter->field('orders.buyer_consignee_name') !!}</div>
						<div
							class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
							<div class=" form-group">
							<div class="input-group">
								{!! $filter->field('orders.order_no') !!} <span
									class="input-group-addon cursor-hover searchSubmit"> <i
									class="fa fa-search"></i>
								</span>
							</div>
							</div>

						</div>
						<div class="clearfix"></div>

					</div>
					{!! $filter->close !!} 
					<!-- DATA GRID  -->
					{!! $grid !!}


				</div>


			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 main-right">
			<span class="left-top-text">Help Desk</span>
			<div class="block">
				<h3 class="block-head">Services</h3>
				<h3 class="block-head">Community</h3>
				<h3 class="block-head">Effortless Transportation - Road PTL</h3>
				<div class="clearfix"></div>
			</div>
			<div class="block">
				<ul class="right-menu">
					<li><a href="#"><i class="fa fa-truck fa-flip-horizontal"></i>Load
							Planning & Optimization</a></li>
					<li><a href="#"><i class="fa fa-truck fa-flip-horizontal"></i>Schedule
							Multiple Loads</a></li>
					<li><a href="#"><i class="fa fa-truck fa-flip-horizontal"></i>Wide
							Choice of Vendors</a></li>
					<li><a href="#"><i class="fa fa-truck fa-flip-horizontal"></i>Varied
							type of trucks</a></li>
					<li><a href="#"><i class="fa fa-wifi"></i>Irresistible Price</a></li>
					<li><a href="#"><i class="fa fa-check"></i>Get Quote &amp Negotiate
							Online</a></li>
					<li><a href="#"><i class="fa fa-check"></i>Track &amp Trace
							Consignment</a></li>
					<li><a href="#"><i class="fa fa-truck"></i>24x7 Customer Support</a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
		</div>
		
		
		
		
	</div>
</div>

@endsection
