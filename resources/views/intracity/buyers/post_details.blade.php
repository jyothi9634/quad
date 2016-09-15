@extends('app') @section('content')
@include('partials.page_top_navigation')
{{--*/ $url_array = array(); /*--}} 
{{--*/ $previousURL = '' /*--}}
{{--*/ $url_array = explode ('/', '' ) /*--}} 
{{--*/ $previousURL = end ($url_array )/*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
<div class="main">

	<div class="container">
		@if (Session::has('message') && Session::get('message')!='')
		<div class="flash ">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
				Session::get('message') }}</p>

		</div>
		@endif <a onclick="return checkSession(3,'/intracity/buyer_post');"
			href="#">
			<button class="btn post-btn pull-right">Post & get Quote</button>
		</a>
		<div class="clearfix"></div>

		<span class="pull-left"><h1 class="page-title">Spot Transaction -
				@if($postDetails->transaction_id) {!! $postDetails->transaction_id
				!!} @else{!! '-' !!} @endif</h1></span> <span class="pull-right">
			@if($postDetails->lkp_post_status_id != 5) <a
			onclick="buyerpostcancel({{$postDetails->id}})"><i class="fa fa-trash red" title="Delete"></i></a> @else <span>Cancelled</span> @endif

				<a href="{{ url('buyerposts/') }}" class="back-link1">Back to Posts</a>
		</span>

		<!-- Search Block Starts Here -->

		<div class="search-block inner-block-bg">

			<div class="from-to-area">
				<span class="search-result"> <i class="fa fa-map-marker"></i> <span
					class="location-text">{!! $postDetails->fromLocation !!} to {!!
						$postDetails->toLocation !!}</span>
				</span>
			</div>
			<div>
				<p class="search-head">City</p>
				<span class="search-result"><i class="fa fa-map-marker"></i> {!!
					$postDetails->city_name !!}</span>
			</div>
			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Pickup Date</p>
					<span class="search-result"> <i class="fa fa-calendar-o"></i> {!!
						date("d/m/Y", strtotime($postDetails->pickup_date)) !!}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">Pickup Time</p>
					<span class="search-result"> <i class="fa fa-clock-o"></i> {!!
						date("h.i a", strtotime($postDetails->pickup_time)) !!}
					</span>
				</div>
			</div>
			<div>
				<p class="search-head">Load Type</p>
				<span class="search-result">{!! $postDetails->load_type !!}</span>
			</div>
			<div>
				<p class="search-head">Post Rate Type</p>
				<span class="search-result">{!! $postDetails->rate_name !!}</span>
			</div>
			<div>
				<p class="search-head">Vehicle Type</p>
				<span class="search-result">@if($postDetails->vehicle_type){!!
					$postDetails->vehicle_type !!} @endif</span>
			</div>

			<div class="empty-div"></div>
		</div>

		<!-- Search Block Ends Here -->



		<div class="col-md-12 padding-none">
			<div class="main-inner">


				<!-- Right Section Starts Here -->

				<div class="main-right">

					<div class="pull-left">
						<div class="info-links">
							<a href="#" class="active"><i class="fa fa-file-text-o"></i>
								Quotes<span class="badge">{!!$quotesCount!!}</span></a>
						</div>
					</div>




					<!-- Table Starts Here -->

					<div class="table-div">

						<!-- Table Head Starts Here -->

						<div class="table-heading inner-block-bg">
							<div class="col-md-5 padding-left-none">
								<input type="checkbox" /><span class="lbl padding-8"></span>
								Vehicle Number<i class="fa  fa-caret-down"></i>
							</div>
							<div class="col-md-5 padding-left-none">
								Price<i class="fa  fa-caret-down"></i>
							</div>
							<div class="col-md-2 padding-none"></div>
						</div>

						<!-- Table Head Ends Here -->

					<div class="table-data" id="intraaddbuyerpostcounteroffer">

							<!-- Table Row Starts Here -->

						
								@if($sellerQuotes) {{--*/ $i = 1 /*--}} @foreach($sellerQuotes
								as $sellerQuote) {{--*/ $buyerQuoteId=$sellerQuote->id /*--}}
									<div class="table-row inner-block-bg">
									<div class="col-md-5 padding-left-none">
									<input type="checkbox" /><span class="lbl padding-8"></span>
									{!! $sellerQuote->vehicle_number !!}
								</div>
								<div class="col-md-5 padding-left-none">{!!
									$sellerQuote->initial_quote_price !!}/-
								</div>
								@if(isset($sellerQuote->order_id))
								<span class="pull-right">Booked</span>

								@else @if($sellerQuote->lkp_post_status_id==OPEN && (isset($flag) && $flag!=1) &&
								(($sellerQuote->seller_acceptence == 1) ||
								(empty($sellerQuote->counter_quote_price) &&
								!empty($sellerQuote->initial_quote_price))))
								<span class="detailsslide-007 intra_add_buyer_addtocart_details btn red-btn pull-right"
											id="{{$buyerQuoteId}}">Book Now</span>
                                                                <div id="dialog_{{$buyerQuoteId}}" data-bqid="{{$buyerQuoteId}}" class="dialog displayNone" title="Confirmation Required">
                                                                        Please confirm that you are booking for:<br />
                                                                        From:{{$postDetails->fromLocation}},<br />
                                                                        To:{{$postDetails->toLocation}},<br />
                                                                        Date:{{date("d/m/Y",strtotime($postDetails->pickup_date))}},<br />
                                                                        Time:{{date("h.i a",strtotime($postDetails->pickup_time))}}.<br />
                                                                </div>
								@endif @endif 

							

							
							{!!
							Form::hidden('service_id',3,array('class'=>'','id'=>'service_id'))!!}
							{!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId,
							$sellerQuote->buyer_id, array('id' =>
							'buyer_post_buyer_id_'.$buyerQuoteId)) !!} {!!
							Form::hidden('buyer_post_vehicle_id_'.$buyerQuoteId,
							$sellerQuote->lkp_ict_vehicle_id, array('id' =>
							'buyer_post_vehicle_id_'.$buyerQuoteId)) !!} {!!
							Form::hidden('buyer_quote_item_id_'.$buyerQuoteId,
							$sellerQuote->buyer_quote_item_id, array('id' =>
							'buyer_quote_item_id_'.$buyerQuoteId)) !!} {!!
							Form::hidden('buyer_post_price_'.$buyerQuoteId,
							$sellerQuote->initial_quote_price, array('id' =>
							'buyer_post_price_'.$buyerQuoteId)) !!} {!!
							Form::hidden('pickup_date', $postDetails->pickup_date, array('id'
							=> 'pickup_date')) !!} {!! Form::hidden('pickup_time',
							$postDetails->pickup_time, array('id' => 'pickup_time')) !!}
							
							<div class="clearfix"></div>
							
							</div>
								
							{{--*/ $i++ /*--}} @endforeach @endif
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
</div>

​ @endsection
