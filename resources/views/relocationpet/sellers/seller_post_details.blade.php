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

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

@include('partials.page_top_navigation')

	<div class="main">

		<div class="container">
			@include('partials.content_top_navigation_links')
			<div class="clearfix"></div>

			<span class="pull-left"><h1 class="page-title">Relocation Pet- {!! $seller_post->transaction_id !!}</h1></span>
				<span class="pull-right">
                                    {{--*/ $viewcount=0 /*--}}
                                    @foreach($seller_post_items as $seller_post_item)
                                    {{--*/    $viewcount += $commoncomponent->getSellersViewcountFromTable($seller_post_item->id,'relocationpet_seller_post_views') /*--}}
                                    @endforeach
                                    
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {!! $viewcount !!}</a>
					@if($seller_post->lkp_post_status_id != 5 && $seller_post->lkp_post_status_id != 3)
						@if($seller_post_items[0]->is_private != 1)
							<a href="/relocation/updatesellerpost/{!! $seller_post->id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
						@endif
                                                <a onclick="setcancelpostid('posts',{!! $seller_post->id !!})" data-toggle="modal" data-target="#cancelsellerpostmodal" href="javascript:void(0)" class="delete-icon">
<!--						<a  href="javascript:void(0)"  onclick="javascript:relocationsellerpostcancel({{ $seller_post->id }})" class="delete-icon">--><i class="fa fa-trash red" title="Delete"></i></a>
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
						
						{{--*/ $payment_type .= ' | '; /*--}}
						{{--*/ $payment_type .= $seller_post->credit_period; /*--}}
						{{--*/ $payment_type .= ' '; /*--}}
						{{--*/ $payment_type .= $seller_post->credit_period_units ; /*--}}

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
							<div class="clearfix"></div>
							
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Cancellation Charges</span>
									<span class="data-value">{!! $seller_post->cancellation_charge_price !!} /-</span>
								</div>

								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Docket Charges</span>
									<span class="data-value">{!! $seller_post->docket_charge_price !!} /-</span>
								</div>
								@if(isset($seller_post->other_charge1_price) && $seller_post->other_charge1_price!=0.00)
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">{!! $seller_post->other_charge1_text !!}</span>
									<span class="data-value">{!! $seller_post->other_charge1_price !!} /-</span>
								</div>
								@endif
								@if(isset($seller_post->other_charge2_price) && $seller_post->other_charge2_price!=0.00)
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">{!! $seller_post->other_charge2_text !!}</span>
									<span class="data-value">{!! $seller_post->other_charge2_price !!} /-</span>
								</div>
								@endif
								@if(isset($seller_post->other_charge2_price) && $seller_post->other_charge2_price!=0.00)
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">{!! $seller_post->other_charge2_text !!}</span>
									<span class="data-value">{!! $seller_post->other_charge2_price !!} /-</span>
								</div>
								@endif				
							
							<div class="clearfix"></div>

							<div class="col-md-12 padding-left-none data-fld">
								<span class="data-head">Terms &amp; Conditions</span>
								<span class="data-value">{!! $seller_post->terms_conditions !!}</span>
							</div>

						</div>
						{{--*/ $householdItems = 0; /*--}}
						{{--*/ $vehicleItems = 0; /*--}}
						@foreach($seller_post_items as $key=>$seller_post_edit_action_line)
								{{--*/ $householdItems++ /*--}}
						@endforeach

						<div class="col-md-12">
							@if($householdItems > 0)
								<div class="table-div table-style1 margin-top">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
                                                                                <div class="col-md-2 padding-left-none">From Location</div>
                                                                                <div class="col-md-1 padding-left-none">To Location</div>
                                                                                <div class="col-md-1 padding-left-none">Pet Type</div>
                                                                                <div class="col-md-1 padding-left-none">Cage Type</div>
                                                                                <div class="col-md-2 padding-left-none">O & D Charges</div>
                                                                                <div class="col-md-2 padding-left-none">Freight</div>
                                                                                <div class="col-md-1 padding-left-none">Transit Days</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data">
										<!-- Table Row Starts Here -->
                                                                            @foreach($seller_post_items as $seller_post_edit_action_line)

                                                                            <div class="table-row inner-block-bg">
                                                                                <div class="col-md-2 padding-left-none">{{$commoncomponent->getCityName($seller_post->from_location_id)}}</div>
                                                                                <div class="col-md-1 padding-left-none">{{$commoncomponent->getCityName($seller_post->to_location_id)}}</div>
                                                                                <div class="col-md-1 padding-left-none">{{$commoncomponent->getPetType($seller_post_edit_action_line->lkp_pet_type_id)}}</div>
                                                                                <div class="col-md-1 padding-left-none"> {{$commoncomponent->getCageType($seller_post_edit_action_line->lkp_cage_type_id)}} </div>
                                                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->od_charges}} /-</div>
                                                                                <div class="col-md-2 padding-none">{{$seller_post_edit_action_line->rate_per_cft}} /- per KG</div>
                                                                                <div class="col-md-1 padding-left-none">{{$seller_post_edit_action_line->transitdays}} {{$seller_post_edit_action_line->units}}</div>
                                                                            </div>
                                                                            @endforeach
										<!-- Table Row Ends Here -->
									</div>
									<!-- Table Ends Here -->
								</div>
							@endif
							<!-- Table Ends Here -->
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
                                                @include('relocationpet.sellers.enquiries_leads', array(
                                                        'enquiries' => $leads,
                                                ))
                                        </div>	

                                        {{--*/ $enquiry_style   =($type=="enquiries")?"style=display:block":"style=display:none" /*--}}
                                        <div class="table-data text-left tabs-group" id="relocation_spot-seller-enquiry" {{$enquiry_style}}>
                                                @include('relocationpet.sellers.enquiries_leads', array(
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