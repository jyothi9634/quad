
@inject('commoncomponent', 'App\Components\CommonComponent')
{{--*/ $lease_term = '' /*--}}
{{--*/ $serviceId = Session::get('service_id') /*--}}
@if($serviceId == ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL || $serviceId == ROAD_TRUCK_LEASE)
	@if(isset($is_detail) && $is_detail==1)
	<div class="table-div">
		<!-- Table Head Starts Here -->
		@if($serviceId == ROAD_FTL )
		<div class="table-heading inner-block-bg">
	    	<div class="col-md-2 padding-left-none">Buyer Name</div>
			<div class="col-md-2 padding-left-none">Dispatch Date</div>
	        <div class="col-md-2 padding-left-none">Delivery Date</div>
	        <div class="col-md-3 padding-left-none">Load Type</div>
	        <div class="col-md-1 padding-left-none">Status</div>
	        <div class="col-md-2 padding-left-none"></div>
		</div>
		@elseif($serviceId == ROAD_TRUCK_LEASE)
		<div class="table-heading inner-block-bg">
			<div class="col-md-2 padding-left-none">Buyer Name</div>
            <div class="col-md-2 padding-left-none">From Date</div>
            <div class="col-md-2 padding-left-none">To date</div>
            <div class="col-md-3 padding-left-none">Vehicle Type</div>
            <div class="col-md-1 padding-left-none">Status</div>
			<div class="col-md-2 padding-left-none"></div>
        </div>
		@else
		<div class="table-heading inner-block-bg">
	    	<div class="col-md-3 padding-left-none">Buyer Name</div>
			<div class="col-md-3 padding-left-none">Dispatch Date</div>
	        <div class="col-md-3 padding-left-none">Load Type</div>
	        <div class="col-md-1 padding-left-none">Status</div>
	        <div class="col-md-2 padding-left-none"></div>
		</div>
		@endif
		<!-- Table Head Ends Here -->
		@if(count($buyerpublicquotedetails)==0)
			<div class="table-data">
										<div class="table-row inner-block-bg text-center">
											No records founds
										</div>
									</div>
									@endif
									{{--*/ $i = 0 /*--}}
								@foreach($buyerpublicquotedetails as $buyerpublicquotedetailsvalue)
									@if($serviceId == ROAD_TRUCK_LEASE )
										@if(isset($buyerpublicquotedetailsvalue->lkp_trucklease_lease_term_id) &&  	$buyerpublicquotedetailsvalue->lkp_trucklease_lease_term_id != "") 
											{{--*/ $lease_term = $commoncomponent->getAllLeaseName($buyerpublicquotedetailsvalue->lkp_trucklease_lease_term_id)  /*--}}
										@else
											{{--*/ $lease_term = '' /*--}}
										@endif
									@else
										{{--*/ $lease_term = '' /*--}}
									@endif
								<div class="table-row inner-block-bg">
								@if($serviceId != ROAD_TRUCK_HAUL )
									<div class="col-md-2 padding-left-none">
								@else
									<div class="col-md-3 padding-left-none">
								@endif
										<span class="lbl padding-8"></span>
										{!! $buyerpublicquotedetailsvalue->username !!}
										<div class="red">
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
										</div>
									</div>
									@if($serviceId != ROAD_TRUCK_HAUL )
									<div class="col-md-2 padding-left-none">
									@else
									<div class="col-md-3 padding-left-none">
									@endif
									@if($serviceId != ROAD_TRUCK_LEASE )
										{!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->dispatch_date) !!}
									@else
										{!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->from_date) !!}
									@endif
									</div>
									@if( $serviceId != ROAD_TRUCK_HAUL)
									<div class="col-md-2 padding-none">
										@if( $serviceId == ROAD_FTL)
										{{--*/ $ddate = $buyerpublicquotedetailsvalue->delivery_date /*--}}
										@else
										{{--*/ $ddate = $buyerpublicquotedetailsvalue->to_date /*--}}
										@endif
										@if($commoncomponent->checkAndGetDate($ddate) =='')
											N/A
										@else
											{!! $commoncomponent->checkAndGetDate($ddate) !!}
										@endif
									</div>
									@endif
									<div class="col-md-3 padding-none">
									@if( $serviceId != ROAD_TRUCK_LEASE)
									{!! $buyerpublicquotedetailsvalue->load_type !!}
									@else
									{!! $buyerpublicquotedetailsvalue->vehicle_type !!}
									@endif
									</div>
									<div class="col-md-1 padding-none">
									{{ $commoncomponent->getSellerPostStatuss($buyerpublicquotedetailsvalue->lkp_post_status_id) }}
									</div>
									<div class="col-md-2 padding-none">
									@if($buyerpublicquotedetailsvalue->lkp_post_status_id==2)
										@if($subscriptionstdate <= $now_date && $subscriptionenddate >= $now_date)
									
											@if(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price =='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000' )
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right submit-data  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Initial Quote Submitted">
												</div>
												
												@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
															$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
															isset($buyersquotes[$i][0]->counter_quote_price) &&
															$buyersquotes[$i][0]->counter_quote_price !='0.0000' && 
															isset($buyersquotes[$i][0]->final_quote_price) &&
															$buyersquotes[$i][0]->final_quote_price =='0.0000' )
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right submit-data  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Submit Final Quote">
												</div>
												@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
															$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
															isset($buyersquotes[$i][0]->counter_quote_price) &&
															$buyersquotes[$i][0]->counter_quote_price !='0.0000' && 
															isset($buyersquotes[$i][0]->final_quote_price) &&
															$buyersquotes[$i][0]->final_quote_price !='0.0000' )
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Final Quote Submitted">
												</div>
												@elseif(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price !=0
														&& (isset($buyersquotes[$i][0]->seller_acceptence) && $buyersquotes[$i][0]->seller_acceptence==1))
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Accepted Firm Offer">
												</div>
												@elseif(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price !=0
														&& (isset($buyersquotes[$i][0]->seller_acceptence) && $buyersquotes[$i][0]->seller_acceptence!=1))
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													 <input type="button" class="btn red-btn pull-right submit-data underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Accept Firm Offer" >
												</div>
												@elseif(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price !=0
														&& (!isset($buyersquotes[$i][0]->seller_acceptence)))
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right submit-data  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Accept Firm Offer">
												</div>
											@else
												<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
													<input type="button" class="btn red-btn pull-right submit-data   underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Submit Quote">
												</div>
											@endif
										@else
											<div class="col-md-12 col-sm-12 col-xs-12 text-right padding-none">
												<input type="button" class="btn red-btn pull-right submit-data  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Submit Quote">
											</div>
										@endif
									@endif
									@if($buyerpublicquotedetailsvalue->lkp_post_status_id==2)
										@if($subscriptionstdate <= $now_date && $subscriptionenddate >= $now_date)
											@if(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000' )
												<div class="col-md-12 col-sm-12 col-xs-12 text-right red padding-none underline_link">
													<input type="button" class="btn red-btn pull-right submit-data  underline_link seller_counter" data-buyernbuyerquoteid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" id="click-link" value="Accept Counter offer">
												</div>
											@endif
										@endif
									@endif
	
									</div>
	
									<div class="pull-right text-right">
										<div class="info-links">
											<span class="detailsslide  underline_link" data-buyersearchlistid="{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}"><span class="show_details">+</span><span class="hide_details">-</span> Details</span>
											<!--a class="show-data-link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a-->
											<a class="red underline_link new_message" data-buyer-transaction="{{$buyerpublicquotedetailsvalue->transaction_no}}" data-userid='{{ $buyerpublicquotedetailsvalue->buyer_id }}' data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $buyerpublicquotedetailsvalue->id }}" href="#"><i class="fa fa-envelope-o"></i></a>
										</div>
									</div>
									{!! Form::open(array('url' => 'sellersubmitquote', 'id' => 'addsellerpostquoteoffer', 'name' => 'addsellerpostquoteoffer')) !!}
									<div class="col-md-12 show-data-div quote_details_1_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" style="display:none" >
											<div class="col-md-12 tab-modal-head">
												<h3>
													<i class="fa fa-map-marker"></i> {!! $buyerpublicquotedetailsvalue->city_name !!} 
													@if($serviceId != ROAD_TRUCK_LEASE)
													to {!! $common->getCityNameFromId($buyerpublicquotedetailsvalue->to_city_id) !!}
													@endif
													<span class="close-icon">x</span>
												</h3>
											</div>
											<div class="col-md-8 data-div">
												@if($serviceId != ROAD_TRUCK_LEASE)
												<div class="col-md-3 padding-left-none data-fld">
													<span class="data-head">Load Type</span>
													<span class="data-value">{!! $buyerpublicquotedetailsvalue->load_type !!}</span>
												</div>
												@else
												<div class="col-md-4 padding-left-none data-fld">
													<span class="data-head">From Date</span>
													<span class="data-value">
													
													{!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->from_date) !!}
													
													</span>
												</div>
	
												<div class="col-md-4 padding-left-none data-fld">
													<span class="data-head">To Date</span>
													<span class="data-value">
													@if($commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->to_date) =='')
													N/A
													@else
													{!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->to_date) !!}
													@endif
													</span>
												</div>
											
												<div class="col-md-4 padding-left-none data-fld">
													<span class="data-head">Lease Term</span>
													<span class="data-value">{{ $commoncomponent->getAllLeaseName($buyerpublicquotedetailsvalue->lkp_trucklease_lease_term_id)}}</span>
												</div>
												@endif
												<div class="col-md-4 padding-left-none data-fld">
													<span class="data-head">Vehicle Type</span>
													<span class="data-value">{!! $buyerpublicquotedetailsvalue->vehicle_type !!}</span>
												</div>
												<div class="col-md-3 padding-left-none data-fld">
													<span class="data-head">Price Type</span>
													<span class="data-value">@if($buyerpublicquotedetailsvalue->lkp_quote_price_type_id==1)
												Competitive
											   @else
											    Firm
											   @endif</span>
												</div>
												@if($serviceId != ROAD_TRUCK_LEASE)
												<div class="col-md-3 padding-left-none data-fld">
													<span class="data-head">No of Loads</span>
													<span class="data-value">{!! $buyerpublicquotedetailsvalue->number_loads !!}</span>
												</div>
												@endif
												<div class="clearfix"></div>
												@if($serviceId != ROAD_TRUCK_LEASE)
                                                                  <div class="col-md-3 padding-left-none data-fld">
                                                                        @if( $serviceId == ROAD_TRUCK_HAUL)
                                                                       <span class="data-head">Reporting Date</span>
                                                                       @else
                                                                       <span class="data-head">Dispatch Date</span>
                                                                       @endif
                                                                       <span class="data-value">{!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->dispatch_date) !!}</span>
                                                                  </div>
                                                                  @if( $serviceId != ROAD_TRUCK_HAUL)
                                                                  <div class="col-md-3 padding-left-none data-fld">
                                                                       <span class="data-head">Delivery Date</span>
                                                                       <span class="data-value">													
                                                                            @if($commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->delivery_date) =='')
                                                                            N/A
                                                                            @else
                                                                            {!! $commoncomponent->checkAndGetDate($buyerpublicquotedetailsvalue->delivery_date) !!}
                                                                            @endif													
                                                                       </span>
                                                                  </div>
                                                            @endif
												
												<div class="col-md-3 padding-left-none data-fld">
													<span class="data-head">Quantity</span>
													<span class="data-value">{!! $buyerpublicquotedetailsvalue->quantity !!} {!! $buyerpublicquotedetailsvalue->units !!}</span>
												</div>
												@endif
											</div>
											<div class="col-md-4">
											@if(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price !=0
												&& (isset($buyersquotes[$i][0]->seller_acceptence) && $buyersquotes[$i][0]->seller_acceptence!=1) )
												<span class="data-head">Submit Quote</span>
												<span class="data-value big-value">0.00 /-</span>
											@elseif(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price !=0
												&& (isset($buyersquotes[$i][0]->seller_acceptence) && $buyersquotes[$i][0]->seller_acceptence==1) )
												<span class="data-head">Final Quote Submitted</span>
												<span class="data-value big-value">{{$commoncomponent->moneyFormat($buyerpublicquotedetailsvalue->price) }} /-</span>	
											@else
												@if(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price =='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price =='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000' )
													<span class="data-head">Submit Quote</span>
													<span class="data-value big-value">0.00 /-</span>
												@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price =='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000' )
													<span class="data-head">Initial Quote Submitted</span>
													<span class="data-value big-value">{{$commoncomponent->moneyFormat($buyersquotes[$i][0]->initial_quote_price)}} /-</span>
												@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000' )
													<span class="data-head">Counter Quote</span>
													<span class="data-value big-value">{{$commoncomponent->moneyFormat($buyersquotes[$i][0]->counter_quote_price)}} /-</span>
												@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price !='0.0000' )
													<span class="data-head">Final Quote Submitted</span>
													<span class="data-value big-value">{{$commoncomponent->moneyFormat($buyersquotes[$i][0]->final_quote_price)}} /-</span>
												@else
													@if($buyerpublicquotedetailsvalue->lkp_quote_price_type_id==1)
													<span class="data-head">Submit Quote</span>
													<span class="data-value big-value">0.00 /-</span>
													@else
													<span class="data-head">Firm Offer</span>
													<span class="data-value big-value">{{ $commoncomponent->moneyFormat( $buyerpublicquotedetailsvalue->price) }} /-</span>
													@endif
												
												@endif
											@endif
											</div>
									</div>
	
									
									<div class="col-md-12 padding-none submit-data-div quote_details_2_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" style="display:none">
	
											<div class="col-md-12 padding-none margin-top margin-bottom">
	
										
											
											@if(isset($buyerpublicquotedetailsvalue->price) && $buyerpublicquotedetailsvalue->price!='0.0000' && $buyerpublicquotedetailsvalue->price!=0)
											
											
											<div class="col-md-3 padding-left-none">
												<div class="data-head padding-top-8 margin-bottom">Firm Offer : Rs {{ $commoncomponent->moneyFormat($buyerpublicquotedetailsvalue->price) }} /-</div>
												@if($serviceId != ROAD_TRUCK_LEASE)
													@if(isset($buyersquotes[$i][0]->final_quote_price) &&  $buyersquotes[$i][0]->final_quote_price!='')
													<div class="data-head padding-top-8 margin-bottom">Transit Days : {{ $buyersquotes[$i][0]->final_transit_days }}</div>
													@else
													 <input type="text" name="accept_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   id="accept_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   class="form-control form-control1 numericvalidation " value="" placeholder="Transit Days *" maxlength="3" />
													@endif
												@else
													<input type="hidden" name="accept_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   id="accept_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   class="form-control form-control1 numericvalidation " value="1" placeholder="Transit Days *" maxlength="3" />
												@endif
												<input type="hidden" name="accept_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                id="accept_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" class="form-control form-control1 numberVal " value="{{ $buyerpublicquotedetailsvalue->price }}" >
													
												<input type='hidden' name='buyer_id' id='buyer_id' value="{{ $buyerpublicquotedetailsvalue->buyer_id }}">
												<input type='hidden' name='buyer_quote_item_id' id='buyer_quote_item_id' value="{{ $buyerpublicquotedetailsvalue->id }}">
												<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{{ Session::get('seller_post_item') }}">
												<input type='hidden' name='transactionid' id='transactionid' value="{{ $seller_post[0]->transaction_id }}">
													
													
												 @if ($errors->has('initial_quote'))<p style="color:red;">{!! $errors->first('initial_quote')!!}</p>@endif
												
											</div>
											
											<div class="col-md-3 padding-none text-right pull-right">
											@if(!isset($buyersquotes[$i][0]->final_quote_price))
											<input type="button" id="acccept_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn accept_quote_submit" value="Accept">							
											@endif
											</div>
										@else
											
											
											
											<div class="col-md-3 padding-left-none">
	
													
													
													@if(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000')
		                                               <div class="data-head padding-top-8 margin-bottom">Quote : Rs {{  $commoncomponent->moneyFormat($buyersquotes[$i][0]->initial_quote_price) }} /-</div>
		                                               @if($serviceId != ROAD_TRUCK_LEASE)
		                                               	<div class="data-head padding-top-8 margin-bottom">Transit Days : {{  $buyersquotes[$i][0]->initial_transit_days }} </div>
		                                               @endif
													@else
	
														<input type="text" name="initial_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                   data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                   id="initial_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" data-servicetype="{{$serviceId}}" data-leasetype="{{$lease_term}}"
		                                                   class="form-control  form-control1   margin-bottom clsFTLQuote" value="" placeholder="Quote *" />
			                                            @if($serviceId != ROAD_TRUCK_LEASE)       
			                                            <input type="text" name="initial_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   id="initial_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" data-servicetype="{{$serviceId}}" 
			                                                   class="form-control form-control1 clsFTLTransitDays" value="" placeholder="Transit Days *" />
			                                            @else
			                                            <input type="hidden" name="initial_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   id="initial_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
			                                                   class="form-control numericvalidation form-control1 numberVal " value="1" placeholder="Transit Days *" maxlength="2" />
			                                            @endif
													@endif
													<input type='hidden' name='buyer_id' id='buyer_id' value="{{ $buyerpublicquotedetailsvalue->buyer_id }}">
													<input type='hidden' name='buyer_quote_item_id' id='buyer_quote_item_id' value="{{ $buyerpublicquotedetailsvalue->id }}">
													<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{{ Session::get('seller_post_item') }}">
													
													 @if ($errors->has('initial_quote'))<p style="color:red;">{!! $errors->first('initial_quote')!!}</p>@endif
	
											</div>
											
													
													
												
											
											@if(isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000')
	
		
											<div class="col-md-3 padding-left-none">
												<div >
	
		                                               <div class="data-head  padding-top-8 margin-bottom">Counter Offer : Rs {{  $commoncomponent->moneyFormat($buyersquotes[$i][0]->counter_quote_price) }} /-</div>
		                                               @if($serviceId != ROAD_TRUCK_LEASE)
		                                               <div class="white-space data-head padding-top-8 margin-bottom">Transit Days : {{  $buyersquotes[$i][0]->initial_transit_days }}</div>
		                                               @endif
		                                               <input type="hidden" name="counter_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
		                                                id="counter_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" class="form-control" value="{{ $buyersquotes[$i][0]->counter_quote_price }}" >
													
													
												</div>
											</div>
											@endif
											
											@if(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000' &&
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price !='0.0000')
												<div class="col-md-3 padding-left-none hide-final">
													<div class=" ">										
														<div class="white-space data-head padding-top-8 margin-bottom">Final Quote : Rs {{ $commoncomponent->moneyFormat($buyersquotes[$i][0]->final_quote_price) }} /-</div>
														@if($serviceId != ROAD_TRUCK_LEASE)
														<div class="white-space data-head padding-top-8 margin-bottom">Transit Days : {{ $buyersquotes[$i][0]->final_transit_days }}</div>
														@endif
	                                          		</div>
	                                           </div>
											@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														$buyersquotes[$i][0]->initial_quote_price !='0.0000' && 
														isset($buyersquotes[$i][0]->counter_quote_price) &&
														$buyersquotes[$i][0]->counter_quote_price !='0.0000' &&
														isset($buyersquotes[$i][0]->final_quote_price) &&
														$buyersquotes[$i][0]->final_quote_price =='0.0000')
														
													<div class="col-md-3 padding-left-none hide-final">
														<div class=" ">													
															<input type="text" name="final_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                id="final_quote_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" data-servicetype="{{$serviceId}}" data-leasetype="{{$lease_term}}" class="margin-bottom form-control form-control1 clsFTLFinalQuote" value="" placeholder="Final Quote *" />
				                                            @if($serviceId != ROAD_TRUCK_LEASE)
				                                            <input type="text" name="final_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                id="final_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" data-servicetype="{{$serviceId}}" class="form-control form-control1 clsFTLTransitDays" value="" placeholder="Transit Days *" />
															@else
															<input type="hidden" name="final_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                data-buyerid="{{ $buyerpublicquotedetailsvalue->buyer_id }}" dat-buyerqouteit="{{ $buyerpublicquotedetailsvalue->id }}" 
				                                                id="final_transit_days_{{ $buyerpublicquotedetailsvalue->buyer_id }}_{{ $buyerpublicquotedetailsvalue->id }}" class="form-control form-control1 numberVal " value="1" placeholder="Transit Days *" maxlength="2" />
															@endif
			
															
														</div>
													</div>
	
													
												@endif
											
											
												<div class="col-md-3 padding-none hide-submit text-right pull-right">
												
												@if($subscriptionstdate <= $now_date && $subscriptionenddate >= $now_date)
													@if($buyerpublicquotedetailsvalue->lkp_quote_access_id == 2)
														@if(isset($buyersquotes[$i][0]->initial_quote_price) && 
															$buyersquotes[$i][0]->initial_quote_price=='0.0000')
															<input type="button" id="initail_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn initial_quote_submit" value="Submit">
														@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														     $buyersquotes[$i][0]->initial_quote_price!='0.0000' &&
															isset($buyersquotes[$i][0]->counter_quote_price) &&
															 $buyersquotes[$i][0]->counter_quote_price!='0.0000' &&
															isset($buyersquotes[$i][0]->final_quote_price) &&
															 $buyersquotes[$i][0]->final_quote_price=='0.0000' )
															 <input type="button" id="final_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn final_quote_submit" value="Submit">
														@endif
													@else
														@if(!isset($buyersquotes[$i][0]->initial_quote_price))
															<input type="button" id="initail_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn initial_quote_submit" value="Submit">
															
														@elseif(isset($buyersquotes[$i][0]->initial_quote_price) &&
														     $buyersquotes[$i][0]->initial_quote_price!='0.0000' &&
															isset($buyersquotes[$i][0]->counter_quote_price) &&
															 $buyersquotes[$i][0]->counter_quote_price!='0.0000' &&
															isset($buyersquotes[$i][0]->final_quote_price) &&
															 $buyersquotes[$i][0]->final_quote_price=='0.0000' )
															<input type="button" id="final_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn final_quote_submit" value="Submit">
														@endif
													@endif
												@else
												Your Subcription is completed.Please renew.
												@endif
											</div>
											
												<div class="col-md-3 padding-none show-submit text-right pull-right">
												
												@if($subscriptionstdate <= $now_date && $subscriptionenddate >= $now_date)
													
													<input type="button" id="counter_quote_submit_{{$buyerpublicquotedetailsvalue->buyer_id}}_{{$buyerpublicquotedetailsvalue->id}}" class="btn add-btn counter_quote_submit" value="Accept">
																
												@else
												Your Subcription is completed.Please renew.
												@endif
											</div>
											
										@endif
	
											</div>
										
									</div>
									{!! Form::close() !!}
								</div>
	
								{{--*/ $i++  /*--}}
								<!-- Table Row Ends Here -->
		@endforeach
	</div>
	@else
	
		@if($subs_st_date <= $now_date && $subs_end_date >= $now_date)
			@if($getbqi[0]->lkp_quote_price_type_id == '2')
				@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000' && isset($getFirmQuotePrice[0]->seller_acceptence) && $getFirmQuotePrice[0]->seller_acceptence!=0)
					<div class="col-md-2 padding-none">
								   		   <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Accepted Firm offer">
	
								   		   </div>
				@elseif($buyer_post_status_id==2)
					<div class="col-md-2 padding-none">
								   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Accept Firm offer">
								   		   </div>
				@endif	
			@elseif($buyer_post_status_id==2 &&  isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000' )
				<div class="col-md-2 padding-none">
				   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Submit Final Quote">												   		  
				   		    <input type="button" class="sellesearchdetails_2 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Accept Counter Offer">
								   		   </div>
			
			@elseif(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price!='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000' )
					<div class="col-md-2 padding-none">
				   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Final Quote Submitted">
								</div>
			@elseif(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price=='0.0000' )
					<div class="col-md-2 padding-none">
				   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Initial Quote Submitted">
							</div>
			@elseif($buyer_post_status_id==2)
				<div class="col-md-2 padding-none">
				   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Submit Quote"> 
							</div>
			@endif
			
		@elseif($buyer_post_status_id==2)
			<div class="col-md-2 padding-none">
				   		    <input type="button" class="sellesearchdetails_1 detailsslide-3 btn red-btn pull-right submit-data" data-buyersearchlistid={{$buyer_id}}_{{$buyer_quote_id}} value="Submit Quote">
								   		   </div>
		@endif
	
	
	
		<div class="pull-right text-right">
			<div class="info-links">
				<span class="detailsslide underline_link" data-buyersearchlistid="{{$buyer_id}}_{{$buyer_quote_id}}">
					<span class="show_details" style="display: inline;">+</span>
					<span class="hide_details" style="display: none;">-</span>Details
				</span> 
	            <a href="#" data-userid="{{$buyer_id}}" data-buyer-transaction="{{$transaction_id}}" class="new_message" data-buyerquoteitemidforseller="{{$buyer_quote_id}}"><i class="fa fa-envelope-o"></i></a>
			</div>
		</div>
	
		<div class="col-md-12 show-data-div quote_details_1_{{$buyer_id}}_{{$buyer_quote_id}}">
			<div class="col-md-12 tab-modal-head">
				<h3>
					<i class="fa fa-map-marker"></i> {{$fromcity_buyer}} 
					@if($serviceId != ROAD_TRUCK_LEASE )
					to {{$tocity_buyer}}
					@endif
					<span class="close-icon">x</span>
				</h3>
			</div>
			<div class="col-md-8 data-div">
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">
					@if($serviceId != ROAD_TRUCK_LEASE )
					Dispatch Date
					@else
					From Date
					@endif
					</span>
					<span class="data-value">{{$commoncomponent->checkAndGetDate($dispatch_date_buyer)}}</span>
				</div>
				@if($serviceId != ROAD_TRUCK_HAUL )
					@if($commoncomponent->checkAndGetDate($delivery_date_buyer) == '')
						<div class="col-md-4 padding-left-none data-fld">
							<span class="data-head">
							@if($serviceId != ROAD_TRUCK_LEASE )
							Delivery Date
							@else
							To Date
							@endif</span>
							<span class="data-value">NA</span>
						</div>
					@else
										
						<div class="col-md-4 padding-left-none data-fld">
							<span class="data-head">
							@if($serviceId != ROAD_TRUCK_LEASE )
							Delivery Date
							@else
							To Date
							@endif
							<span class="data-value">{{$commoncomponent->checkAndGetDate($delivery_date_buyer)}}</span>
						</div>
					@endif
				@endif						
										
				<input type="hidden" name="from_city" id="from_city" value="{{$fromcity_buyer}}" >
				<input type="hidden" name="to_city" id="to_city"  value="{{$tocity_buyer}}" >
				<input type="hidden" name="from_date" id="from_date" value="{{$dispatch_date_buyer}}" >
				@if($serviceId != ROAD_TRUCK_HAUL )
				<input type="hidden" name="to_date" id="to_date"  value="{{$delivery_date_buyer}}" >
				@endif
				<input type="hidden" name="search" id="search"  value="1" >
				@if($serviceId == ROAD_TRUCK_LEASE )
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Lease Term</span>
					<span class="data-value">{{$load_type_buyer}}</span>
				</div>	
				@endif						
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Vehicle Type</span>
					<span class="data-value">{{$vechile_type_buyer}}</span>
				</div>
				@if($serviceId == ROAD_TRUCK_LEASE )
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Price Type</span>
					<span class="data-value">{{$price_buyer}}</span>
				</div>
				@else
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Load Type</span>
					<span class="data-value">{{$load_type_buyer}}</span>
				</div>
				@endif
				@if($serviceId != ROAD_TRUCK_LEASE )
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Quantity</span>
					<span class="data-value">{{$qty}} {{$units}}</span>
				</div>
														
				<div class="col-md-4 padding-left-none data-fld">
					<span class="data-head">Loads</span>
					<span class="data-value">{{$loads}}</span>
				</div>
				@endif
			</div>
			{{--*/ $quotePrice = '' /*--}}
			{{--*/ $quotePrice = $commoncomponent->moneyFormat($getbqi[0]->price) /*--}}
			@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000' && isset($getFirmQuotePrice[0]->seller_acceptence) && $getFirmQuotePrice[0]->seller_acceptence!=0)
							
			<div class="col-md-4">
				<span class="data-head">Accepted Firm Price</span>
				<span class="data-value big-value">{{$commoncomponent->moneyFormat($getFirmQuotePrice[0]->firm_price)}} /-</span>
			</div>
			@elseif (isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price=='0.0000' )
		
					<div class="col-md-4">
						<span class="data-head">Initial Quote Submitted</span>
						<span class="data-value big-value">{{$commoncomponent->moneyFormat($getInitialQuotePrice[0]->initial_quote_price)}} /-</span>
					</div>
			@elseif (isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000')
						
					<div class="col-md-4">
				<span class="data-head">Counter Quote</span>
				<span class="data-value big-value">{{$commoncomponent->moneyFormat($getCounterQuotePrice[0]->counter_quote_price)}} /-</span>
			</div>
			
			@elseif (isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price!='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
					isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000')
						
			<div class="col-md-4">
				<span class="data-head">Final Quote Submitted</span>
				<span class="data-value big-value">{{$commoncomponent->moneyFormat($getFinalQuotePrice[0]->final_quote_price)}} /-</span>
			</div>
			@else	
				@if($getbqi[0]->lkp_quote_price_type_id == 2)
					<div class="col-md-4">
						<span class="data-head">Firm Offer</span>
						<span class="data-value big-value">{{$commoncomponent->moneyFormat($getbqi[0]->price)}} /-</span>
					</div>
				@else
					<div class="col-md-4">
						<span class="data-head">Submit Quote</span>
						<span class="data-value big-value">0.00 /-</span>
					</div>
				
				@endif
			@endif
		</div>
		@if($serviceId == ROAD_TRUCK_LEASE)
				@if(isset($lease_term_id) &&  $lease_term_id != "") 
						{{--*/ $lease_term = $commoncomponent->getAllLeaseName($lease_term_id)  /*--}}
				@else
						{{--*/ $lease_term = '' /*--}}
				@endif
		@else
			{{--*/ $lease_term = '' /*--}}
		@endif
		<div style="display:none;" class="col-md-12 submit-data-div margin-bottom submit-data-div1  padding-none seller_quotedetails_{{$buyer_id}}_{{$buyer_quote_id}} padding-top">
																
												@if($getbqi[0]->lkp_quote_price_type_id == '2') 
	
													<div class="col-md-4 col-sm-4 col-xs-6 padding-none">
													<div class="form-group">
													
													@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000' && $getFirmQuotePrice[0]->seller_acceptence ==1 )
														<div class="white-space padding-left-none data-head"> Firm Offer : Rs {{$commoncomponent->moneyFormat($getbqi[0]->price)}} /-</div>
	                                               		@if($serviceId != ROAD_TRUCK_LEASE)
	                                               		<div class="white-space data-head padding-top-8"> Transit Days : {{$getFirmQuotePrice[0]->final_transit_days}}
														</div>
														@endif
														
													
														@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000' && isset($getFirmQuotePrice[0]->seller_acceptence) && $getFirmQuotePrice[0]->seller_acceptence ==0 )
															<div class="col-md-4 data-fld padding-none text-right pull-right">
															<input type="button" value="Accept" id="accept_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="accept_quote_submit btn add-btn" >
															</div>
														@endif
														
													
													@else
														<div class="white-space data-head"> Firm Offer : Rs  {{$commoncomponent->moneyFormat($getbqi[0]->price)}} /-</div>
	                                               		<div class="margin-top">
	                                               		@if($serviceId != ROAD_TRUCK_LEASE)
		                                            	<input type="text" placeholder="Transit Days *"
										           		name="accept_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
										           		data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
										           		id="accept_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
										           		class="form-control form-control1 numberVal " value="" >
										           		@else
		                                            	<input type="hidden" placeholder="Transit Days *"
										           		name="accept_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
										           		data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
										           		id="accept_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
										           		class="form-control form-control1 numberVal " value="1" >
										           		
										           		@endif
	                                               		</div>
	                                               	@endif
													</div>
												</div>
												@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000' && $getFirmQuotePrice[0]->seller_acceptence ==1 )
												@else
												<div class="col-md-4 data-fld padding-none text-right pull-right">
													<input type="button" id="accept_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="accept_quote_submit btn add-btn" value="Accept">
												</div>
												@endif	
												<input type="hidden"
									           		name="accept_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
									           		data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
									           		id="accept_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
									           		class="form-control form-control1 numberVal " value={{$getbqi[0]->price}} >
													
												@else
	
	
													@if(isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000')
														<div class="clearfix"></div>
	                                                <div class="col-md-3 padding-none">
														<div class="padding-left-none padding-top-8" ><span class="data-head">Quote : Rs {{$commoncomponent->moneyFormat($getInitialQuotePrice[0]->initial_quote_price)}} /-</span></div>
	                                           			@if($serviceId != ROAD_TRUCK_LEASE)
	                                           			<div class="padding-left-none padding-top-8" ><span class="data-head">Transit Days : {{$getInitialQuotePrice[0]->initial_transit_days}}</span></div>
	                                           			@endif
	                                           		</div>
													@else
														
																<div class="margin-top">
											
																
																<div class="col-md-3 padding-left-none">
													
																<input type="text" placeholder="Quote *"
									           			name="initial_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
									           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
									           			id="initial_quote_{{$buyer_id}}_{{$buyer_quote_id}}" data-servicetype="{{$serviceId}}"  data-leasetype="{{$lease_term}}"
									           			class="form-control form-control1  margin-top margin-bottom clsFTLQuote" value="" >
									           					@if($serviceId != ROAD_TRUCK_LEASE)
									           					<input type="text" placeholder="Transit Days *"
									           			name="initial_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
									           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
									           			id="initial_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}" data-servicetype="{{$serviceId}}"
									           			class="form-control form-control1 clsFTLTransitDays " value="">
									           			@else
									           			<input type="hidden" placeholder="Transit Days *"
									           			name="initial_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
									           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
									           			id="initial_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
									           			class="form-control form-control1 numberVal " value="1" maxlength="2">
									           			@endif
									           			</div>	</div>
									           		 @endif
													 @if(isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000')
														
															<div class="col-md-3 padding-none ">
																<div>
													
					                                               	<div class=" padding-top-8"> <span class="data-head">Counter Offer : Rs {{$commoncomponent->moneyFormat($getCounterQuotePrice[0]->counter_quote_price)}} /-</span></div>
					                                               @if($serviceId != ROAD_TRUCK_LEASE)
					                                                <div class=" padding-top-8"> <span class="data-head">Transit Days: {{$getCounterQuotePrice[0]->initial_transit_days}}</span></div>
					                                                @endif
					                                                
					                                               			<input type="hidden"
														           			name="counter_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
														           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
														           			id="counter_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
														           			class="form-control form-control1 numberVal " value="{{$getCounterQuotePrice[0]->counter_quote_price}}" >
													        </div>
														           					</div>
														@endif
													
												
													<div class="col-md-3 padding-left-none hide-final_{{$buyer_id}}_{{$buyer_quote_id}}">
														<div >
													
															@if(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price!='0.0000')
															
			                                               		<div class=" padding-top-8 padding-right-none"> <span class="data-head">Final Quote : Rs {{$commoncomponent->moneyFormat($getFinalQuotePrice[0]->final_quote_price)}} /-</span></div>
			                                               		@if($serviceId != ROAD_TRUCK_LEASE)
			                                               		<div class=" padding-top-8 padding-right-none"> <span class="data-head">Transit Days : {{$getFinalQuotePrice[0]->final_transit_days}}</span></div>
			                                               		@endif
			                                              	@elseif(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000'
																	&& isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000')
																	<div class=""><input type="text"
											           			name="final_quote_{{$buyer_id}}_{{$buyer_quote_id}}"
											           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
											           			id="final_quote_{{$buyer_id}}_{{$buyer_quote_id}}" data-servicetype="{{$serviceId}}"  data-leasetype="{{$lease_term}}" 
											           			class="form-control form-control1  margin-bottom clsFTLFinalQuote" value=""  placeholder="Final Quote *"></div>
											           			<div class="">
											           			@if($serviceId != ROAD_TRUCK_LEASE)
											           			<input type="text"
											           			name="final_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
											           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
											           			id="final_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}" data-servicetype="{{$serviceId}}"
											           			class="form-control form-control1 clsFTLTransitDays" value=""  placeholder="Transit Days *">
											           			@else
											           			<input type="hidden"
											           			name="final_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
											           			data-buyerid="{{$buyer_id}}" dat-buyerqouteit="{{$buyer_quote_id}}"
											           			id="final_transit_days_{{$buyer_id}}_{{$buyer_quote_id}}"
											           			class="form-control form-control1 numberVal " value="1"  placeholder="Transit Days *" maxlength="2">
											           			@endif
											           			</div>
											           		@endif
													
													 
														</div>
													</div>
													
													
													
												@endif
												<!--Tracking -->
												@if($serviceId != ROAD_TRUCK_LEASE)
												@if($tracking=='')
												
												<div class="clearfix"></div>
													<div class="col-md-3 padding-left-none track-margin">
														<div class="normal-select">
                                                                            {{--*/ $trackingOptionsHtml = \App\Components\CommonComponent::getTrackingTypeOptionsHtml() /*--}}
															<select class="selectpicker"  id="tracking_{{$buyer_id}}_{{$buyer_quote_id}}" name="tracking_{{$buyer_id}}_{{$buyer_quote_id}}">
																<option value="">Tracking</option>
																{!! $trackingOptionsHtml !!}
															</select>
														</div>
													</div>
												@else
												<div class="clearfix"></div>
													<div class="col-md-6 padding-none"><span class="data-head">Tracking : {{$tracking}}</span></div>
													<input type="hidden" name="tracking" id="tracking" value="{{$getSellerpost[0]->tracking}}">
												@endif
												@endif
												<!--Payment -->
												@if($payment_type=='')
												<div class="clearfix"></div>
												<div class="col-md-12 padding-none"><h2 class="filter-head1">Payment Terms</h2></div>
												<div class="col-md-3 padding-left-none track-margin margin-bottom">
													<div class="normal-select">
													@if($serviceId == ROAD_FTL)
														<select class="selectpicker ptl_payment payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" id="payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" name="paymentterms_{{$buyer_id}}_{{$buyer_quote_id}}">
															<option value="1">Advance</option>
															<option value="2">Cash on Delivery</option>
															<option value="3">Cash on Pickup</option>
															<option value="4">Credit</option>
														</select>
													@elseif($serviceId == ROAD_TRUCK_LEASE)
														<select class="selectpicker ptl_payment payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" id="payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" name="paymentterms_{{$buyer_id}}_{{$buyer_quote_id}}">
															<option value="1">Advance</option>
															<option value="4">Credit</option>
														</select>
													@else
														<select class="selectpicker ptl_payment payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" id="payment_options_{{$buyer_id}}_{{$buyer_quote_id}}" name="paymentterms_{{$buyer_id}}_{{$buyer_quote_id}}">
															<option value="1">Advance</option>
														</select>
													@endif
													</div>
												</div>
										
												<div class="col-md-12 padding-none" id ="show_advanced_period_{{$buyer_id}}_{{$buyer_quote_id}}">
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" id="accept_payment_ptl[]" value="1"><span class="lbl padding-8">NEFT/RTGS</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="2"><span class="lbl padding-8">Credit Card</span>
													</div>
													<div class="checkbox_inline">
														<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="3"><span class="lbl padding-8">Debit Card</span>
													</div>
												</div>
										
										
												<div class="col-md-12 form-control-fld padding-left-none" style ="display: none;" id = "show_credit_period_{{$buyer_id}}_{{$buyer_quote_id}}">
									<div class="col-md-3 form-control-fld padding-left-none">
				
									<div class="col-md-7 padding-none">
										<div class="input-prepend">
											<input class="form-control form-control1 clsFTLCreditPeriod  credit_period_ptl_{{$buyer_id}}_{{$buyer_quote_id}}" type="text" name="credit_period_ptl_{{$buyer_id}}_{{$buyer_quote_id}}" id="credit_period_ptl" value="" placeholder="Credit Period" data-servicetype="{{$serviceId}}"  data-leasetype="{{$lease_term}}"><span class="lbl padding-8">Credit Card</span>
										</div>
									</div>
									<div class="col-md-5 padding-none">
										<div class="input-prepend">
											<span class="add-on unit-days manage">
												<div class="normal-select">
													<select class="selectpicker bs-select-hidden credit_period_units_{{$buyer_id}}_{{$buyer_quote_id}}"  id="credit_period_units" name="credit_period_units_{{$buyer_id}}_{{$buyer_quote_id}}">
														<option value="Days">Days</option>
														<option value="Weeks">Weeks</option>
													</select>
							
												</div>
											</span>
										</div>
									</div>
				
				
									</div>
									<div class="col-md-12 padding-none">
										<div class="checkbox_inline" >
										<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="1"><span class="lbl padding-8">Net Banking</span>
										
										</div>
										<div class="checkbox_inline">
										<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="2"><span class="lbl padding-8">Cheque / DD</span>
										</div>
	
									</div>
								</div>
								@else
									<div class="clearfix"></div>
									<div class="col-md-12 padding-none "><span class="data-head">Payment : {{$payment_type}}</span></div>
											  <input type="hidden" name="payment_options" id="payment_options" value="{{$payment_type}}">
											  <input type="hidden" name="credit_peroid" id="credit_peroid" value=" ">
											  <input type="hidden" name="credit_period_units" id="credit_period_units" value=" ">
								@endif
								
					<div class="col-md-3 padding-right-none pull-right hide-submit_{{$buyer_id}}_{{$buyer_quote_id}}" style="display:none;" >
							
						@if($subs_st_date <= $now_date && $subs_end_date >= $now_date)
							@if($accessid ==2)
									
								@if(isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price==0 && $getbqi[0]->lkp_quote_price_type_id == '1')
									<input type="button" id="initail_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="initial_quote_submit btn add-btn  pull-right" value="Submit">
								@elseif(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
										isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000' )
									<input type="button" id="final_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="final_quote_submit btn add-btn pull-right" value="Submit">
								@endif
							@else
						
									
								@if(!isset($getInitialQuotePrice) && $getbqi[0]->lkp_quote_price_type_id == '1')
									<input type="button" id="initail_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="initial_quote_submit btn add-btn  pull-right " value="Submit">
								@elseif(isset($getFinalQuotePrice) && $getFinalQuotePrice[0]->final_quote_price=='0.0000' && isset($getInitialQuotePrice) && $getInitialQuotePrice[0]->initial_quote_price!='0.0000' &&
										isset($getCounterQuotePrice) && $getCounterQuotePrice[0]->counter_quote_price!='0.0000' )
									<input type="button" id="final_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" class="final_quote_submit btn add-btn  pull-right" value="Submit" />
								@endif
							@endif
						
						@else
						
							
								<div class="col-md-4 data-fld padding-none text-right">
								Your Subcription is completed.Please renew.</div>
						@endif
				
					</div>
							
					<div class="col-md-6 padding-none show-submit_{{$buyer_id}}_{{$buyer_quote_id}} " style="display:none;">
						@if($subs_st_date <= $now_date && $subs_end_date >= $now_date)
							
								@if($accessid ==2)
							
									<input id="counter_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" type="button" class="btn add-btn counter_quote_submit pull-right" value=" Accept ">
								@else
									<input id="counter_quote_submit_{{$buyer_id}}_{{$buyer_quote_id}}" type="button" class="btn add-btn counter_quote_submit pull-right" value=" Accept ">
								@endif
							
							
						@else
							
								'Your Susbcription is completed.Please renew.
						@endif
					</div>
				</div>
			</div></form>
	<!-- 		@if(isset($getFirmQuotePrice) && $getFirmQuotePrice[0]->firm_price!='0.0000') -->
	<!-- 		</div></div> -->
	<!-- 		@endif -->
	@endif
@endif