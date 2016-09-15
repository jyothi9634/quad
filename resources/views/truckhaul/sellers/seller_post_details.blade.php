@inject('common', 'App\Components\BuyerComponent')
@inject('commoncomponent', 'App\Components\CommonComponent')
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
@include('partials.page_top_navigation')


@if(isset($seller_post_items[0]->from_location_id) && !empty($seller_post_items[0]->from_location_id))
 {{--*/  $fromLocationId = $seller_post_items[0]->from_location_id /*--}}
@else
 {{--*/  $fromLocationId = ''/*--}}
@endif

@if(isset($seller_post_items[0]->to_location_id) && !empty($seller_post_items[0]->to_location_id))
 {{--*/  $toLocationId = $seller_post_items[0]->to_location_id /*--}}
@else
 {{--*/  $toLocationId = ''/*--}}
@endif



{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_seller_hual    =   $commoncomponent->getGsaDocuments(SELLER,$serviceId,$seller_post[0]->id,$fromLocationId,$toLocationId); /*--}}      
{{--*/ $docCount_hual = count($docs_seller_hual) /*--}}


@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('sellerlist/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

<div class="main">
	@if(Session::has('updatedseller')) <div class="alert alert-info"> {{Session::get('updatedseller')}} </div> @endif

	@if(Session::has('message') && Session::get('message')!='')
		<div class="flash alert-info notification">
			<p class="bg-success text-success col-sm-12 text-center ">{{Session::get('message')}}</p>
		</div>
	@endif
        

	<div class="container">
		@include('partials.content_top_navigation_links')
		<div class="clearfix"></div>

		<span class="pull-left"><h1 class="page-title">Spot Transactions - {!! $seller_post[0]->transaction_id !!}</h1></span>
		<span class="pull-right">
			<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $viewcount }} </a>
			@if($seller_post_items[0]->lkp_post_status_id ==2)
				@if($seller_post_items[0]->is_private != 1)
					<a href="/updateseller/{!! $seller_post_id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
				@endif
				<a href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="javascript:setcancelpostid('items',{{ $seller_post_items[0]->id }})" class="delete-icon"><i class="fa fa-trash red" title="Delete"></i></a>
			@endif
			<a href="{{$backToPostsUrl}}" class="back-link1">Back to Posts</a>
		</span>
		
		<div class="filter-expand-block">
			<div class="search-block inner-block-bg margin-bottom-less-1">
				<div class="from-to-area">
					<span class="search-result">
						<i class="fa fa-map-marker"></i>
						<span class="location-text">{!! $fromlocations[0]->city_name !!} to {!! $tolocations[0]->city_name !!}</span>
					</span>
				</div>
				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid From</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							{!! $commoncomponent->checkAndGetDate($seller_post[0]->from_date) !!}
						</span>
					</div>
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid To</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							{!! $commoncomponent->checkAndGetDate($seller_post[0]->to_date) !!}
						</span>
					</div>
				</div>
				<div>
					<p class="search-head">Load Type</p>
					<span class="search-result">{!! $loadtype !!}</span>
				</div>
				<div>
					<p class="search-head">Vehicle Number</p>
					<span class="search-result">{!! $seller_post_items[0]->vehicle_number !!}</span>
				</div>
				<div>
					<p class="search-head">Price</p>
					<span class="search-result">{!! $commoncomponent->getPriceType($seller_post_items[0]->price) !!}</span>
				</div>
				<div class="text-right filter-details">
					<div class="info-links">
						<a class="transaction-details-expand"><span class="show-icon">+</span>
							<span class="hide-icon">-</span> Details
						</a>
					</div>
				</div>

			</div>

			<div class="show-trans-details-div-expand trans-details-expand"> 
			   	<div class="expand-block">
			   		<div class="col-md-12">
						<div class="col-md-2 padding-left-none data-fld">
					   		<p class="search-head">Vehicle Type</p>
							<span class="search-result">{!! $vehicletype !!}</span>
						</div>

						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Transit Days</span>
							<span class="data-value">{!! $seller_post_items[0]->transitdays !!} {!! $seller_post_items[0]->units !!}</span>
						</div>
						
								@if($seller_post[0]->lkp_payment_mode_id == 1)
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Payment</span>
									<span class="data-value">
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
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Payment</span>
									<span class="data-value">
										{{--*/ $payment_type = 'Cash on delivery'; /*--}}
								@elseif($seller_post[0]->lkp_payment_mode_id == 3)
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Payment</span>
									<span class="data-value">
										{{--*/  $payment_type = 'Cash on pickup'; /*--}}
								@else
								<div class="col-md-3 padding-left-none data-fld">
									<span class="data-head">Payment</span>
									<span class="data-value">
										{{--*/  $payment_type = 'Credit'; /*--}}
										@if($seller_post[0]->accept_credit_netbanking == 1)
											{{--*/ $payment_type .= ' | Net Banking'; /*--}}
										@endif
										@if($seller_post[0]->accept_credit_cheque == 1)
											{{--*/ $payment_type .= ' | Cheque / DD'; /*--}}
										@endif
										
										
										{{--*/ $payment_type .= ' | ';/*--}}
										{{--*/ $payment_type .= $seller_post[0]->credit_period;/*--}}
										{{--*/ $payment_type .= ' ';/*--}}
										{{--*/ $payment_type .= $seller_post[0]->credit_period_units;/*--}}
									
								@endif

										{!! $payment_type !!}
									</span>
								</div>
							
						@if($seller_post[0]->cancellation_charge_price != "" && $seller_post[0]->cancellation_charge_price != "0.00")
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">Cancellation Charges</span>
								<span class="data-value">{{ $commoncomponent->getPriceType($seller_post[0]->cancellation_charge_price) }}</span>
							</div>
						@endif
						@if($seller_post[0]->docket_charge_price != "" && $seller_post[0]->docket_charge_price != "0.00")
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">Docket Charges</span>
								<span class="data-value">{{ $commoncomponent->getPriceType($seller_post[0]->docket_charge_price) }}</span>
							</div>
						@endif	
												
						@if($seller_post[0]->other_charge1_text !='' && !empty($seller_post[0]->other_charge1_price))
							
							<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{{$seller_post[0]->other_charge1_text}}</span>
							<span class="data-value">{{ $commoncomponent->getPriceType($seller_post[0]->other_charge1_price) }}</span>
						</div>
						@endif
						@if($seller_post[0]->other_charge2_text !='' && !empty($seller_post[0]->other_charge2_price))
							
							<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{{$seller_post[0]->other_charge2_text}}</span>
							<span class="data-value">{{ $commoncomponent->getPriceType($seller_post[0]->other_charge2_price) }} </span>
						</div>
						@endif
						@if($seller_post[0]->other_charge3_text !='' && !empty($seller_post[0]->other_charge3_price))
							
							<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">{{$seller_post[0]->other_charge3_text}}</span>
							<span class="data-value">{{ $commoncomponent->getPriceType($seller_post[0]->other_charge3_price) }}</span>
						</div>
						@endif

						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">Tracking</span>
							<span class="data-value">								
                                        {{ $commoncomponent->getTrackingType($seller_post[0]->tracking) }}
							</span>
						</div>
						<div class="col-md-1 padding-left-none data-fld">
							<span class="data-head">Documents</span>
							<span class="data-value">{!! $docCount_hual !!}</span>
						</div>
						<div class="clearfix"></div>
						@if($seller_post[0]->terms_conditions!='')
						<div class="col-md-12 padding-left-none data-fld">
							<span class="data-head">Terms &amp; Conditions</span>
							<span class="data-value">{{ $seller_post[0]->terms_conditions }}</span>
						</div>
						@endif
					</div>
					<div class="clearfix"></div>
				</div>
      		</div>
		</div>

		
		
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				
				
				<!-- Right Section Starts Here -->

				<div class="main-right">

					<div class="pull-left">
						<div class="info-links" id="seller_post_info_links">
							<a  href="#" class="{{($type=="messages")?"active":""}}" data-showdiv="ftl-seller-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
							<a href="#" class="{{($type=="enquiries")?"active":""}}" data-showdiv="ftl-seller-enquiry" >
								<i class="fa fa-file-text-o"></i> Enquiries
								<span class="badge">{!! count($buyerpublicquotedetails) !!}</span>
							</a>
							<a href="#" class="{{($type=="leads")?"active":""}}" data-showdiv="ftl-seller-leads"><i class="fa fa-thumbs-o-up" ></i> Leads<span class="badge">{!! $lead_count !!}</span></a>
							<a href="#" data-showdiv="ftl-seller-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics</a>
							<a href="#" data-showdiv="ftl-seller-documentation"><i class="fa fa-file-text-o"></i> Documentation <span class="badge">{!! $docCount_hual !!}</span>
							</a>
						</div>
					</div>
                   
					<div class="clearfix"></div>
					<div class="table-data text-left tabs-group" id="ftl-seller-marketanalytics" style="display:none;">
								
                                                <div class="table-row inner-block-bg text-center">
                                                    No records founds
                                                </div>
                                        </div>	
	
                                        
                                        
                                        {{--*/ $docu_style   =($type=="documentation")?"style=display:block":"style=display:none" /*--}} 
                                <div id="ftl-seller-documentation" class="table-data text-left tabs-group" {{$docu_style}}>
                                    <div class="table-data inner-block-bg">                                       
                                        
                                        @if($docCount_hual>0)                                     
                                        <div class="col-sm-12 padding-right-none">
                                            <h3>List of documents </h3> 
                                            <ul class="popup-list">                                               
                                                
                                                @foreach($docs_seller_hual as $doc)
                                                <li>{{$doc}}</li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                       @else
                                       No Documents Found
                                       @endif
                                       
                                    </div>
                                </div> 
                                        
                                        
                                        
                                        
                                        <div class="clearfix"></div>
                                                {{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
							<div class="table-data text-left tabs-group" id="ftl-seller-messages" {{$msg_style}}>
								
								
                                                                    {!! $allMessagesList['grid'] !!}
                                                            
							</div>	
							<div class="clearfix"></div>
							<!-- Enquiries -->
                                                        {{--*/ $enquiry_style   =($type=="enquiries")?"style=display:block":"style=display:none" /*--}}
							@if($seller_post[0]->lkp_post_status_id != 1)
								<div class="table-data text-left tabs-group" id="ftl-seller-enquiry" {{$enquiry_style}}>
	                            	<!-- Table Starts Here -->
	 								@include('partials.seller.submit_quote', array(
										'buyerpublicquotedetails' => $buyerpublicquotedetails,
										'buyersquotes' => $buyersquotes,
										'is_detail' => 1,
									))     
									<!-- Table Ends Here -->     
	                                                
	                            </div>
							@endif
							<!-- EOD of ENQUIRIES -->	


							<!-- Table Row Starts Here -->

							
							<!-- Leads -->		

							{{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}} 
							
							<div id="ftl-seller-leads" {{$leads_style}}>
                               <!-- Table Starts Here -->
						        @include('partials.seller.submit_quote', array(
									'buyerpublicquotedetails' => $buyerleadsquotedetails,
									'buyersquotes' => $buyersleads,
									'is_detail' => 1,
								))   
								<!-- Table Ends Here -->                              
							</div>
							


							<!-- End of leads -->
                                                        

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