@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationofficebuyercomponent', 'App\Components\RelocationOffice\RelocationOfficeBuyerComponent')
		<!-- Page top navigation Starts Here-->
@if(!empty($_REQUEST) && isset($_REQUEST['type']) )
    
    {{--*/ $type = $_REQUEST['type'] /*--}}
@else
    {{--*/ $type = 'quotes' /*--}}
    @endif
    @if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
    {{--*/ $countMessages = count($allMessagesList['result']) /*--}}
@else
    {{--*/ $countMessages = 0 /*--}}
@endif
{{--*/ $url = url().'/buyerbooknow/' /*--}}


@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@elseif(!str_contains("buyerposts",URL::previous()))
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

<div class="main">
	<div class="container">
		@if(Session::has('sumsg'))
                <div class="flash">
                        <p class="text-success col-sm-12 text-center flash-txt alert-success">
                                {{ Session::get('sumsg') }}
                        </p>
                </div>
		@endif
		@if(Session::has('succmsg'))
                <div class="flash">
                        <p class="text-success col-sm-12 text-center flash-txt alert-success">
                                {{ Session::get('succmsg') }}
                        </p>
                </div>
		@endif
		@if(Session::has('success')) 
                <div class="flash">
                    <p class="text-success col-sm-12 text-center flash-txt alert-success">
                        {{ Session::get('success') }}
                    </p>
                </div>
                @endif
		<!--button class="btn post-btn pull-right">Post & get Quote</button-->

		<!-- Page top navigation Starts Here-->
		@include('partials.page_top_navigation')

					<div class="clearfix"></div>
		        <span class="btn post-btn pull-right"><a href="{{'/relocation/creatbuyerrpost'}}"> Post &amp; Get Quote</a></span>
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Relocation Office - {{$buyer_post_details[0]->transaction_id}}</h1></span>
				<span class="pull-right">
					<a href="javascript:void(0)" class="view-icon red"><i class="fa fa-eye" title="Views"></i>&nbsp;{{$ptlbuyercomponent->updateBuyerQuoteDetailsViews($buyer_post_details[0]->id,'relocationoffice_buyer_post_views')}}</a>
					@if($buyer_post_details[0]->lkp_quote_access_id==2)
					<a href="/editrelocationbuyerquote/{{$buyer_post_details[0]->id}}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
					@endif
					@if($buyer_post_details[0]->lkp_post_status_id == 2)
						<a href="javascript:void(0)" class="delete-icon" onclick='buyerpostcancel({{$buyer_post_details[0]->id}})'><i class="fa fa-trash red" title="Delete"></i></a>
					@endif
					<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
				</span>
				

				<div class="filter-expand-block">
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
								{{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}
							</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Pickup Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date)}} 
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}  
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Post Type</p>
						<span class="search-result">
							@if($buyer_post_details[0]->lkp_quote_access_id==1)
							Public
							@else
							Private
							@endif 
						</span>
					</div>
					<div>
						<p class="search-head">Distance</p>
						<span class="search-result">{{$buyer_post_details[0]->distance}} KM</span>
					</div>
					<div>
						<p class="search-head">Total Volume</p>
	                    {{--*/ $volume_total = $commonComponent->getOfficeBuyerVolume($buyer_post_details[0]->id); /*--}}
						    {{$volume_total}} CFT
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
					<div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
					   	<div class="expand-block">
					   		<div class="col-md-6">
								<div>
									<div class="advanced-search-details" style="display: block;">
										<!-- Table Starts Here -->
										<div class="table-div table-style1 inventory-block" style="display: block;">
											<div class="table-div table-style1 inventory-table padding-none">
												
												<!-- Table Head Starts Here -->

												<div class="table-heading inner-block-bg">
													<div class="col-md-8 padding-left-none">&nbsp;</div>
													<div class="col-md-4 padding-left-none text-center">No of Items</div>
												
												</div>

												<!-- Table Head Ends Here -->

												<div class="table-data">
													@if(count($buyer_post_inventory_details))
														@foreach($buyer_post_inventory_details as $inv)
															<!-- Table Row Starts Here -->
															<div class="table-row inner-block-bg">
																<div class="col-md-8 padding-left-none">{{$inv->office_particular_identifier}}</div>
																<div class="col-md-4 padding-left-none text-center">
																	{{$inv->number_of_items}}
																</div>
																
															</div>
															<!-- Table Row Ends Here -->
														@endforeach	
													@endif
												</div>
											</div>	
										</div>	
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							@if($buyer_post_details[0]->lkp_quote_access_id==2)
								<div class="col-md-1"></div>	
								<div class="col-md-3">
									<span class="data-head">Sellers</span>
									<span class="data-value">
									{{--*/ $privateSellers=array();/*--}}
									{{--*/ $privateSellers=$relocationofficebuyercomponent->getSellerNames($buyer_post_details[0]->id) /*--}} 
										@foreach($privateSellers as $privateSeller)
											{{ucwords($privateSeller->username)}}<br>
										@endforeach
									</span>
								</div>
							@endif					   		
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
								<div class="info-links">
									<a href="javascript:void(0)" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
									<a href="javascript:void(0)" class="{{($type=="quotes")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes<span class='badge'>{{$relocationofficebuyercomponent->getQuotesCount($buyer_post_details[0]->id)}}</span></a>
									{{-- <a href="javascript:void(0)" class="{{($type=="leads")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-leads"><i class="fa fa-thumbs-o-up"></i> Leads</a> --}}
									<a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Market Analytics</a>
									<a href="javascript:void(0)"><i class="fa fa-file-text-o"></i> Documentation</a>
								</div>
							</div>

							<div class="col-md-2 pull-right padding-right-none compare-fld">
								<div class="normal-select">
								
								
								{{--*/ $transitselected=""; /*--}}
								{{--*/ $priceselected=""; /*--}}
								
								@if($compareid==1)
								{{--*/ $transitselected="selected"; /*--}}
								{{--*/ $priceselected=""; /*--}}
								@endif
								@if($compareid==2)
								{{--*/ $priceselected="selected"; /*--}}
								{{--*/ $transitselected=""; /*--}}
								@endif
								<select class="selectpicker" onchange="comapareTypeChange(this.value)" id="buyer_postrelocation_counter_offer_comparision_types">
										<option value="">Compare</option>
										<option value="2" {{$priceselected}}>Price</option>
									</select>
								</div>
							</div>
							<input type="hidden" name="buyer_details_id" id="buyer_details_id" value="{{$buyer_post_details[0]->id}}">
							
							<!-- Table Starts Here -->

							<div class="table-div">
								{{-- Messages Grid Starts here --}}	
									{{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
	                                <div id="ftl-buyer-messages" class="tabs-group" {{$msg_style}}>
	                                    
	                                        {!! $allMessagesList['grid'] !!}
	                                    
	                                </div>
                                {{-- Messages Grid Ends here --}}	
								{{-- Leads Grid Starts here --}}		
	                                {{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}} 
	                                <div id="ftl-buyer-leads" class="tabs-group" {{$leads_style}}>
	                                    	<div class="table-row inner-block-bg text-center">
	                                        No data available
	                                        </div>
	                                </div>
                                {{-- Leads Grid Ends here --}}	
                                {{-- Quotes Grid Starts here --}}	
								<!-- Table Head Starts Here -->
								{{--*/ $quotes_style   =($type=="quotes")?"style=display:block":"style=display:none" /*--}}                                
                                <div id="ftl-buyer-quotes" class="tabs-group" {{$quotes_style}}>	
								<div class="table-heading inner-block-bg">
								@if(!empty($compareid))
								<div class="col-md-3 padding-left-none">
										<input type="checkbox" name="relocation_seller_list" id="relocation_seller_list"><span class="lbl padding-8"></span>
										Seller Name<i class="fa  fa-caret-down"></i>
									</div>
								<div class="col-md-3 padding-left-none">Quote<i class="fa  fa-caret-down"></i></div>
                                @else
                                <div class="col-md-4 padding-left-none">
										<input type="checkbox" name="relocation_seller_list" id="relocation_seller_list"><span class="lbl padding-8"></span>
										Seller Name<i class="fa  fa-caret-down"></i>
									</div>
								<div class="col-md-4 padding-left-none">Quote<i class="fa  fa-caret-down"></i></div>
                                @endif
								@if(!empty($compareid))
                                <div class="col-md-2 padding-none text-left">
                                      Rank
                                     </div>
                                @endif
                               
								<div class="col-md-4 padding-left-none"></div>
								
								</div>

								<!-- Table Head Ends Here -->

								<div class="table-data">
									<?php //print_r($seller_quote_details);exit;?>
									<!-- Table Row Starts Here -->
									@foreach($seller_quote_details as $seller_quote_detail)
                                     {{--*/ $sp_item_id = $seller_quote_detail->seller_post_id /*--}}
									<div class="table-row inner-block-bg">
									@if(!empty($compareid))
										<div class="col-md-3 padding-left-none">
											<input type="checkbox" class="checksellres" name="check_{{$seller_quote_detail->seller_id}}" id="check_{{$seller_quote_detail->seller_id}}" value="{{$seller_quote_detail->seller_id}}"><span class="lbl padding-8"></span>
											{{ucwords($seller_quote_detail->username)}}
											<div class="red rating-margin">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-3 padding-none">{{$seller_quote_detail->total_price}} /-</div>
										@else
										<div class="col-md-4 padding-left-none">
											<input type="checkbox" class="checksellres" name="check_{{$seller_quote_detail->seller_id}}" id="check_{{$seller_quote_detail->seller_id}}" value="{{$seller_quote_detail->seller_id}}"><span class="lbl padding-8"></span>
											{{ucwords($seller_quote_detail->username)}}
											<div class="red rating-margin">
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</div>
										</div>
										<div class="col-md-4 padding-none">{{$seller_quote_detail->total_price}} /-</div>
										@endif
										@if(!empty($compareid))
	                                     <div class="col-md-2 padding-none text-left">
	                                      {!! $seller_quote_detail->rank !!}
	                                     </div>
	                                    @endif
	                                        <!--button class="btn red-btn pull-right">Book Now</button-->
	                                     
                                           @if($commonComponent->CheckCartItem($seller_quote_detail->buyer_quote_id)==1)
                                            <div class="col-md-4 padding-none ">
                                                <input type="button" class="btn red-btn pull-right buyer_book_now" data-url="{{ $url.$seller_quote_detail->buyer_quote_id.'/'.$seller_quote_detail->id }}"
                                             id = "buyer_book_now_{{ $seller_quote_detail->buyer_quote_id }}" data-buyerpostofferid="{{ $seller_quote_detail->buyer_quote_id }}" value="Book Now" />
                                            </div>
                                           @elseif($commonComponent->CheckCart($seller_quote_detail->buyer_quote_id,$sp_item_id)==1)
                                           	<div class="col-md-4 padding-none">
                                                  <button class="btn red-btn pull-right buyer_submit_counter_offer">Booked</button>
                                              </div>
                                           @endif
                                       
										<div class="clearfix"></div>
										<div class="pull-right text-right">
											<div class="info-links">
												<a class="viewcount_show-data-link" data-quoteId='{{$seller_quote_detail->seller_post_id}}' id='{{$seller_quote_detail->seller_post_id}}'><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
												<a href="javascript:void(0)" class="underline_link new_message" data-transaction_no="{{$seller_quote_detail->transaction_no}}" data-userid="{{$seller_quote_detail->seller_id}}" data-id="{{$seller_quote_detail->buyer_quote_id}}" data-buyerquoteitemid="{{ $seller_quote_detail->seller_post_id }}"><i class="fa fa-envelope-o"></i></a>
											</div>
										</div>

										<div class="col-md-12 show-data-div padding-top">
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Total Price (Rs) : {{$seller_quote_detail->total_price}}/-</span>
											</div>

											<div class="clearfix"></div>

											<div class="col-md-3 padding-left-none">
												<span class="data-head"><u>Additional Charges</u></span>
											</div>
											<div class="clearfix"></div>
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Cancellation Charges (Rs) : {{$seller_quote_detail->cancellation_charges}}/-</span>
											</div>
								 		</div>
									</div>
									
									<!-- Table Row Ends Here -->
								@endforeach

									

									
                                                                </div>

								</div>
								{{-- Quotes Grid Ends here --}}	
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
@endsection