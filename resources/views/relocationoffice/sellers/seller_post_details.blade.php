@inject('common', 'App\Components\BuyerComponent')
@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationOffice\RelocationOfficeSellerComponent')

@extends('app')
@section('content')

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

	@if(!empty($_REQUEST) )

		{{--*/ $type = $_REQUEST['type'] /*--}}
	@else
		{{--*/ $type = 'enquiries' /*--}}
	@endif
	@if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
		{{--*/ $countMessages = count($allMessagesList['result']) /*--}}
	@else
		{{--*/ $countMessages = 0 /*--}}
	@endif
	{{--*/ $now_date = date('Y-m-d') /*--}}
	
@if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
    {{--*/ $countMessages = count($allMessagesList['result']) /*--}}
@else
    {{--*/ $countMessages = 0 /*--}}
@endif

@include('partials.page_top_navigation')

	<div class="main">

		<div class="container">
			@include('partials.content_top_navigation_links')
			<div class="clearfix"></div>

			<span class="pull-left"><h1 class="page-title">Spot Relocation Office- {!! $seller_post->transaction_id !!}</h1></span>
				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i>
					{!! $commoncomponent->getSellersViewcountFromTable($seller_post->id,'relocationoffice_seller_post_views') !!}</a>
					@if($seller_post->lkp_post_status_id != 5)
						@if($seller_post->is_private != 1)
							<a href="/relocation/updatesellerpost/{{$seller_post->id}}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
						<!-- <a  href="javascript:void(0)"  onclick="javascript:relocationsellerpostcancel({{ $seller_post->id }})" class="delete-icon"><i class="fa fa-trash red" title="Delete"></i></a> -->
						@endif
					@endif

					<a href="{{  $backToPostsUrl }}" class="back-link1">Back to Posts</a>
				</span>


			<div class="filter-expand-block">
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{!! $commoncomponent->getCityName($seller_post->from_location_id) !!}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Valid From</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{!! $commoncomponent->checkAndGetDate($seller_post->from_date) !!}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Valid To</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{!! $commoncomponent->checkAndGetDate($seller_post->to_date) !!}
							</span>
						</div>
					</div>
					
					<div>
						<p class="search-head">Payment</p>
						<span class="search-result">
							{{--*/  $payment_type = ''; /*--}}
							{!! $commoncomponent->getSellerPostPaymentMethod($seller_post->lkp_payment_mode_id) !!}
							@if($seller_post->accept_credit_netbanking == 1)
								{{--*/ $payment_type .= ' | Net Banking'; /*--}}
							@endif
							@if($seller_post->accept_credit_cheque == 1)
								{{--*/ $payment_type .= ' | Cheque / DD'; /*--}}
							@endif
							
							
							@if($seller_post->credit_period)
							{{--*/ $payment_type .= ' | ';/*--}}
								{{--*/ $payment_type .= $seller_post->credit_period;/*--}}
							 {{--*/ $payment_type .= ' ';/*--}}
								{{--*/ $payment_type .= $seller_post->credit_period_units;/*--}}
							@endif
							{{$payment_type}}
						</span>
					</div>
					<div>
						<p class="search-head">Tracking</p>
						<span class="search-result">							
                                    {{ $commoncomponent->getTrackingType($seller_post->tracking) }}
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
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">Post Type -									
                                             {{ $commoncomponent->getQuoteAccessById($seller_post->lkp_access_id) }}
								</span>
								@if($seller_post->lkp_access_id == 2 || $seller_post->lkp_access_id == 3)
									<span class="data-value">
										@foreach($privatebuyers as $pdetails)
											{{$pdetails->username}} |
										@endforeach
									</span>
								@endif
							</div>
							@if($seller_post->is_private != 1)
								<div class="table-div table-style1">
									<h2 class="filter-head1 margin-left-none">Pricing Details</h2>
							
									<!-- Table Head Starts Here -->
			
									<div class="table-heading inner-block-bg">
										<div class="col-md-2 padding-left-none">Minimum KMs<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Maximum KMs<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">Price<i class="fa fa-caret-down"></i></div>
									</div>
			
									<!-- Table Head Ends Here -->
			
									<div class="table-data">
											@foreach($seller_post_slabs as $slabs_row)
											<!-- Table Row Ends Here -->	
											<div class="table-row inner-block-bg">
												<div class="col-md-2 padding-left-none break-all">{{$slabs_row->slab_min_km}}</div>
												<div class="col-md-2 padding-left-none break-all">{{$slabs_row->slab_max_km}}</div>
												<div class="col-md-3 padding-left-none break-all">{{$slabs_row->transport_price}}</div>
											</div>
											@endforeach											
											<!-- Table Row Ends Here -->
									</div>
								</div>
							@endif	
								<div class="clearfix"></div>

								<div class="col-md-12 form-control-fld">
									<!-- <div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Door to Door Charges</span>
										<span class="data-value">1235.00</span>
									</div> -->

									<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">O & D Charges</span>
										<span class="data-value">{{$seller_post->rate_per_cft}} /-</span>
									</div>
								</div>
								
								<div class="col-md-12 form-control-fld">
						   		@if($seller_post->cancellation_charge_price!='' && $seller_post->cancellation_charge_text !='')
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Cancellation Charges
									</span>
									<span class="data-value">
									<?php 
									if($seller_post->cancellation_charge_price!='' && $seller_post->cancellation_charge_text !='')
									echo  $commoncomponent->getPriceType($seller_post->cancellation_charge_price);
									?>
									</span>
								</div>
								@endif
								@if($seller_post->docket_charge_price!='' && $seller_post->docket_charge_text !='')
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Other Charges
									</span>
									<span class="data-value">
									<?php 
									if($seller_post->docket_charge_price!='' && $seller_post->docket_charge_text !='')
									echo  $commoncomponent->getPriceType($seller_post->docket_charge_price);
									?>
									</span>
								</div>
								@endif
								@if($seller_post->other_charge1_text !='' && $seller_post->other_charge1_price!='')
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">
									<?php if($seller_post->other_charge1_text !='')
										echo  $seller_post->other_charge1_text; 
									?>
									</span>
									<span class="data-value">
									<?php 
									if($seller_post->other_charge1_text !='' && $seller_post->other_charge1_price!='')
									echo  $commoncomponent->getPriceType($seller_post->other_charge1_price);
									?>
									</span>
								</div>
								@endif
								@if($seller_post->other_charge2_text !='' && $seller_post->other_charge2_price!='')
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">
									<?php if($seller_post->other_charge2_text !='')
										echo  $seller_post->other_charge2_text; 
									?>
									</span>
									<span class="data-value">
									<?php 
									if($seller_post->other_charge2_text !='' && $seller_post->other_charge2_price!='')
									echo  $commoncomponent->getPriceType($seller_post->other_charge2_price);
									?>
									</span>
								</div>
								@endif
								@if($seller_post->other_charge3_text !='' && $seller_post->other_charge3_price!='')
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">
									<?php if($seller_post->other_charge3_text !='')
										echo  $seller_post->other_charge3_text; 
									?>
									</span>
									<span class="data-value">
									<?php 
									if($seller_post->other_charge3_text !='' && $seller_post->other_charge3_price!='')
									echo  $commoncomponent->getPriceType($seller_post->other_charge3_price);
									?>
									</span>
								</div>
								@endif
								
								
								
								</div>
								
								@if($seller_post->terms_conditions!='')
									<div class="col-md-12 form-control-fld">
										<span class="data-head">Terms &amp; Conditions</span>
										<span class="data-value">{{ $seller_post->terms_conditions }}</span>
									</div>
								@endif
							</div>

							<div class="clearfix"></div>
						</div>
		      		</div>
					<!--toggle div ends-->

			</div>

			<div class="col-md-12 padding-none">
				<div class="main-inner">


					<!-- Right Section Starts Here -->

					<div class="main-right">

						<div class="pull-left">
							<div class="info-links" id="seller_post_info_links">
								
								<a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="relocationoffice_spot-seller-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
								<a href="#" class="{{($type=="enquiries")?"active":""}} tabs-showdiv" data-showdiv="relocationoffice_spot-seller-enquiry" >
									<i class="fa fa-file-text-o"></i> Enquiries
									<span class="badge">{!! count($enquiries) !!}</span>
								</a>
								<a href="#" class="{{($type=="leads")?"active":""}} tabs-showdiv " data-showdiv="relocationoffice_spot-seller-leads"><i class="fa fa-thumbs-o-up" ></i> Leads<span class="badge">{!! $lead_count !!}</span></a>
								<a href="#" class="tabs-showdiv" data-showdiv="relocationoffice_spot-seller-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
								<a href="#" class="tabs-showdiv" data-showdiv="relocationoffice_spot-seller-documentation"><i class="fa fa-file-text-o"></i> Documentation</a>
								<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
								<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{!! $seller_post->id !!}">
							</div>
						</div>
						<div class="clearfix"></div>
						<!-- Table Starts Here -->
						<div class="table-div">
						{{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}}
						<div id="relocationoffice_spot-seller-messages" class="tabs-group" {{$msg_style}}>
                             
                            {!! $allMessagesList['grid'] !!}
                             
                        </div>
						
						<div class="table-data text-left tabs-group" id="relocationoffice_spot-seller-marketanalytics" style="display:none;">
							<div class="table-row inner-block-bg text-center">
								No records founds
							</div>
						</div>		

						<div class="table-data text-left tabs-group" id="relocationoffice_spot-seller-documentation" style="display:none;">
							<div class="table-row inner-block-bg  text-center">
								No records founds
							</div>
						</div>				
						
						<div class="table-data text-left tabs-group" id="relocationoffice_spot-seller-leads" style="display:none;">
							@include('relocationoffice.sellers.enquiries_leads', array(
								'enquiries' => $leads,
							)) 
						</div>	
								
						{{--*/ $enquiry_style   =($type=="enquiries")?"style=display:block":"style=display:none" /*--}}
						<div class="table-data text-left tabs-group" id="relocationoffice_spot-seller-enquiry" {{$enquiry_style}}>
							 @include('relocationoffice.sellers.enquiries_leads', array(
								'enquiries' => $enquiries,
							)) 
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

					<!-- Modal -->

@endsection