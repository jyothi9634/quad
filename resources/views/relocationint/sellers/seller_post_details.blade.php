@inject('common', 'App\Components\BuyerComponent')
@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\Relocation\RelocationSellerComponent')

@extends('app')
@section('content')
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

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

	<div class="main">

		<div class="container">
			@include('partials.content_top_navigation_links')
			<div class="clearfix"></div>

			<span class="pull-left"><h1 class="page-title">Spot Relocation - {!! $seller_post->transaction_id !!}</h1></span>
				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {!! $commoncomponent->getSellersViewcountFromTable($seller_post->id,'relocationint_seller_post_views') !!}</a>
					@if($seller_post->lkp_post_status_id != 5)
						@if($seller_post->is_private != 1)
							<a href="/relocation/updatesellerpost/{!! $seller_post->id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
						@endif
						<a  href="javascript:void(0)"  onclick="javascript:relocationsellerpostcancel({{ $seller_post->id }})" class="delete-icon"><i class="fa fa-trash red" title="Delete"></i></a>
					@endif

					<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
				</span>


			<div class="filter-expand-block">
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{!! $commoncomponent->getCityName($seller_post->from_location_id) !!} to {!! $commoncomponent->getCityName($seller_post->to_location_id) !!}</span>
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

					@if($seller_post->lkp_payment_mode_id == 1)
						{{--*/ $payment_type = 'Advance'; /*--}}
						@if($seller_post->accept_payment_netbanking == 1)
							{{--*/ $payment_type .= ' | Net Banking'; /*--}}
						@endif
						@if($seller_post->accept_payment_credit == 1)
							{{--*/ $payment_type .= ' | Credit Card'; /*--}}
						@endif
						@if($seller_post->accept_payment_debit == 1)
							{{--*/ $payment_type .= ' | Debit Card'; /*--}}
						@endif
					@elseif($seller_post->lkp_payment_mode_id == 2)
						{{--*/ $payment_type = 'Cash on delivery'; /*--}}
					@elseif($seller_post->lkp_payment_mode_id == 3)
						{{--*/  $payment_type = 'Cash on pickup'; /*--}}
					@else
						{{--*/  $payment_type = 'Credit'; /*--}}
						@if($seller_post->accept_credit_netbanking == 1)
							{{--*/ $payment_type .= ' | Net Banking'; /*--}}
						@endif
						@if($seller_post->accept_credit_cheque == 1)
							{{--*/ $payment_type .= ' | Cheque / DD'; /*--}}
						@endif
						
						@if($seller_post->credit_period)
							{{--*/ $payment_type .= ' | '; /*--}}
							{{--*/ $payment_type .= $seller_post->credit_period; /*--}}
							{{--*/ $payment_type .= ' '; /*--}}
							{{--*/ $payment_type .= $seller_post->credit_period_units ; /*--}}
						@endif
					@endif

					<div>
						<p class="search-head">Payment</p>
						<span class="search-result">{!! $payment_type !!}</span>
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
						@if($seller_post->lkp_international_type_id==1) {{-- Spot Air Post Details --}}
						   	<div class="expand-block">
								<div class="col-md-12">

									
									<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Storage Charges</span>
										<span class="data-value">
											@if($seller_post->storage_charges)
												{!! $seller_post->storage_charges !!}
											@else
												0.00
											@endif
											/-
										</span>
									</div>

									<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Cancellation Charges</span>
										<span class="data-value">
											@if($seller_post->cancellation_charge_price)
												{!! $seller_post->cancellation_charge_price !!}
											@else
												0.00
											@endif
											/-
										</span>

									</div>

									<div class="col-md-2 padding-left-none data-fld">
										<span class="data-head">Other Charges</span>
										<span class="data-value">
											@if($seller_post->other_charge_price)
												{!! $seller_post->other_charge_price !!}
											@else
												0.00
											@endif
											/-
										</span>

									</div>

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

								</div>

								<div class="clearfix"></div>
								@if(count($seller_post_slabs) > 0)
								<div class="table-div table-style1 margin-top">
										
										<!-- Table Head Starts Here -->

										<div class="table-heading inner-block-bg">
											<div class="col-md-3 padding-left-none">Weight Bracket (KGs)</div>
											<div class="col-md-3 padding-left-none">Transit Days</div>
											<div class="col-md-3 padding-left-none">Freight Charges (per KG)</div>
											<div class="col-md-3 padding-none">O & D Charges (Rate per CFT)</div>
										</div>

										<!-- Table Head Ends Here -->

										<div class="table-data">
											

											@foreach($seller_post_slabs as $slab)
												<!-- Table Row Starts Here -->

												<div class="table-row inner-block-bg">
													<div class="col-md-3 padding-left-none">{{$slab->min_slab_weight}}-{{$slab->max_slab_weight}}</div>
													<div class="col-md-3 padding-left-none">{{$seller_post->transitdays}} Days</div>
													<div class="col-md-3 padding-left-none">{{$slab->freight_charges}} /-</div>
													<div class="col-md-3 padding-left-none">{{$slab->od_charges}} /-</div>
												</div>

												<!-- Table Row Ends Here -->
												
											@endforeach

										</div>
									</div>
									@endif


							</div>
						@elseif($seller_post-> 	lkp_international_type_id==2) {{-- Spot Ocean Post Details --}}
					   	<div class="expand-block">
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
							<div class="col-md-12">
								@if(count($seller_post_items) > 0)
								<div class="table-div table-style1 margin-top">
									
										<!-- Table Head Starts Here -->
										<div class="table-heading inner-block-bg">
											<div class="col-md-3 padding-left-none">Shipment Type</div>
											<div class="col-md-2 padding-left-none">Volume</div>
											<div class="col-md-3 padding-left-none">O & D Charges</div>
											<div class="col-md-2 padding-left-none">Freight</div>
											<div class="col-md-2 padding-left-none">Transit Days</div>
										</div>
										<!-- Table Head Ends Here -->
										@foreach($seller_post_items as $item)
											<div class="table-data">
												<!-- Table Row Starts Here -->
												<div class="table-row inner-block-bg">
													<div class="col-md-3 padding-left-none">{{$item->shipment_type}}</div>
													<div class="col-md-2 padding-left-none">{{$item->volume}}</div>
													<div class="col-md-3 padding-left-none">{{$item->od_charges}} /- (Rs per CBM)</div>
													<div class="col-md-2 padding-none">{{$item->freight_charges}} /-
														@if($item->shipment_type=='LCL')
														(Rs per CBM)
														@else
														(Rs per Flat)
														@endif
													</div>
													<div class="col-md-2 padding-left-none">{{$item->transitdays}} Days</div>
												</div>
												<!-- Table Row Ends Here -->
											</div>
										@endforeach
									<!-- Table Ends Here -->
									</div>
								@endif

									<div class="col-md-2 form-control-fld data-fld">
										<span class="data-head">Crating Charges (per CFT)</span>
										<span class="data-value">
											@if($seller_post->crating_charges)
												{!! $seller_post->crating_charges !!}
											@else
												0.00
											@endif
											/-											
										</span>
									</div>

									<div class="clearfix"></div>

									<div class="col-md-12 form-control-fld data-fld">
										<span class="data-head"><u>Additional Charges</u></span>
									</div>

									<div class="col-md-2 form-control-fld data-fld">
										<span class="data-head">Cancellation Charges</span>
										<span class="data-value">
											@if($seller_post->cancellation_charge_price)
												{!! $seller_post->cancellation_charge_price !!}
											@else
												0.00
											@endif
											/-											
										</span>
									</div>

									<div class="col-md-2 form-control-fld data-fld">
										<span class="data-head">Other Charges</span>
										<span class="data-value">
											@if($seller_post->other_charge_price)
												{!! $seller_post->other_charge_price !!}
											@else
												0.00
											@endif
											/-											
										</span>
									</div>


							</div>

							<div class="clearfix"></div>
						</div>
						@endif
		      		</div>
					<!--toggle div ends-->
			</div>

			<div class="col-md-12 padding-none">
				<div class="main-inner">


					<!-- Right Section Starts Here -->

					<div class="main-right">

						<div class="pull-left">
							<div class="info-links" id="seller_post_info_links">
								
								<a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="relocation_spot-seller-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
								<a href="#" class="{{($type=="enquiries")?"active":""}} tabs-showdiv" data-showdiv="relocation_spot-seller-enquiry" >
									<i class="fa fa-file-text-o"></i> Enquiries
									<span class="badge">{!! count($enquiries) !!}</span>
								</a>
								<a href="#" class="{{($type=="leads")?"active":""}} tabs-showdiv " data-showdiv="relocation_spot-seller-leads"><i class="fa fa-thumbs-o-up" ></i> Leads<span class="badge">{!! $lead_count !!}</span></a>
								<a href="#" class="tabs-showdiv" data-showdiv="relocation_spot-seller-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
								<a href="#" class="tabs-showdiv" data-showdiv="relocation_spot-seller-documentation"><i class="fa fa-file-text-o"></i> Documentation</a>
								<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
								<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{!! $seller_post->id !!}">
							</div>
						</div>
						<div class="clearfix"></div>
						<!-- Table Starts Here -->
						<div class="table-div">
						{{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}}
						<div id="relocation_spot-seller-messages" class="tabs-group" {{$msg_style}}>
                             
                            {!! $allMessagesList['grid'] !!}
                             
                        </div>
						
						<div class="table-data text-left tabs-group" id="relocation_spot-seller-marketanalytics" style="display:none;">
							<div class="table-row inner-block-bg text-center">
								No records founds
							</div>
						</div>		

						<div class="table-data text-left tabs-group" id="relocation_spot-seller-documentation" style="display:none;">
							<div class="table-row inner-block-bg  text-center">
								No records founds
							</div>
						</div>				
						
						<div class="table-data text-left tabs-group" id="relocation_spot-seller-leads" style="display:none;">
							@include('relocationint.sellers.enquiries_leads', array(
								'enquiries' => $leads,
								'lkp_international_type_id'=>$seller_post->lkp_international_type_id
							))
						</div>	
								
						{{--*/ $enquiry_style   =($type=="enquiries")?"style=display:block":"style=display:none" /*--}}
						<div class="table-data text-left tabs-group" id="relocation_spot-seller-enquiry" {{$enquiry_style}}>
							@include('relocationint.sellers.enquiries_leads', array(
								'enquiries' => $enquiries,
								'lkp_international_type_id'=>$seller_post->lkp_international_type_id
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