@extends('app') 
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationpetcomponent', 'App\Components\RelocationPet\RelocationPetBuyerComponent')

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
				
				<!-- Page top navigation Starts Here-->
				@include('partials.page_top_navigation')
				<div class="clearfix"></div>
				<a href="{{'/relocation/creatbuyerrpost'}}"><span class="btn post-btn pull-right"> Post &amp; Get Quote</span></a>
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Relocation Pet - {{$buyer_post_details->transaction_id}}</h1></span>

				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $ptlbuyercomponent->updateBuyerQuoteDetailsViews($buyer_post_details->id,'relocationpet_buyer_post_views') }}</a>

					@if($buyer_post_details->lkp_post_status_id != CANCELLED)
						
						@if($buyer_post_details->lkp_quote_access_id == 2)
							<a href="/editrelocationbuyerquote/{{$buyer_post_details->id}}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
						@endif

					<!--buyerpostcancel-->
					<a href='#' class="delete-icon" onclick='setcancelbuyerpostid({{$buyer_post_details->id}})' data-toggle="modal" data-target="#cancelbuyerpostmodal"><i class="fa fa-trash red" title="Delete"></i></a>
					@endif

					<a href="{{$backToPostsUrl}}"  class="back-link1">Back to Posts</a>
				</span>
				

				<div class="filter-expand-block">
				<!-- Search Block Starts Here -->
				
				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $buyer_post_details->from_location_name }} to {{ $buyer_post_details->to_location_name }}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $buyer_post_details->dispatch_date }}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $buyer_post_details->delivery_date }}

							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Pet Type</p>
						<span class="search-result">{{ $buyer_post_details->pet_type }}</span>
					</div>
					<div>
						<p class="search-head">Breed</p>
                                                @if($buyer_post_details->breed_type!='')
						<span class="search-result">{{ $buyer_post_details->breed_type }}</span>
                                                @else
                                                NA
                                                @endif
					</div>
					<div>
						<p class="search-head">Cage Type</p>
						<span class="search-result">{{ $buyer_post_details->cage_type }}</span>
					</div>
					<div>
						<p class="search-head">Cage Weight</p>
						<span class="search-result">{{ $buyer_post_details->cage_weight }} KGs</span>
					</div>
					<div class="empty-div"></div>
					
					@if($buyer_post_details->lkp_quote_access_id == 2)
					<div class="text-right filter-details">
						<div class="info-links">
							<a class="transaction-details-expand"><span class="show-icon">+</span>
								<span class="hide-icon">-</span> Details
							</a>
						</div>
					</div>
					@endif

				</div>

					<!-- Search Block Ends Here -->

					@if($buyer_post_details->lkp_quote_access_id == 2)
					<!--toggle div starts-->
					<div class="show-trans-details-div-expand trans-details-expand"> 
					   	<div class="expand-block">
					   		<div class="col-md-12">
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Post Type - {{ $buyer_post_details->quote_access }}</span>
									<!-- <span class="data-value">Buyer1 | Buyer2 | Buyer3 | Buyer4</span> -->

									{{--*/ $privateSellers=$commonComponent->getSellerNames($buyer_post_details->id, 'relocationpet_buyer_selected_sellers') /*--}} 
									
									<span class="data-value">
									@foreach($privateSellers as $privateSeller)
									{{ucfirst($privateSeller->username)}}<br>
									@endforeach
									</span>

								</div>
							</div>
							<div class="clearfix"></div>
						</div>
		      		</div>
					<!--toggle div ends-->
					@endif

				</div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

							<div class="pull-left">
								<div class="info-links">
									<a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
									<a href="#" class="{{($type=="quotes")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes<span class='badge'>{{$relocationpetcomponent->getBuyerPetmoveQuoteCount($buyer_post_details->id, RELOCATION_PET_MOVE)}}</span></a>
									
									<a href="#"><i class="fa fa-line-chart"></i> Market Analytics</a>
									<a href="#"><i class="fa fa-file-text-o"></i> Documentation</a>
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
									<select class="selectpicker" id="buyer_postrelocation_counter_offer_comparision_types">
										<option value="">Compare</option>
										<option value="1" {{$transitselected}}>Transit Time</option>
										<option value="2" {{$priceselected}}>Price</option>
									</select>
								</div>
								<input type="hidden" name="buyer_details_id" id="buyer_details_id" value="{{$buyer_post_details->id}}">
							</div>
							<!-- Table Starts Here -->

							<div class="table-div">
								{{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
                                 <div id="ftl-buyer-messages" class="tabs-group" {{$msg_style}}>
                                   {!! $allMessagesList['grid'] !!}
                                 </div>
                                                                
								<!-- Table Head Starts Here -->
								{{--*/ $quotes_style   =($type=="quotes")?"style=display:block":"style=display:none" /*--}}                                
                                <div id="ftl-buyer-quotes" class="tabs-group" {{$quotes_style}}>	
								<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none">
										<input type="checkbox" name="relocation_seller_list" id="relocation_seller_list"><span class="lbl padding-8"></span>
										Seller Name<i class="fa  fa-caret-down"></i>
									</div>
									@if(!empty($compareid))
									<div class="col-md-2 padding-left-none">Transit Days <i class="fa  fa-caret-down"></i></div>
									<div class="col-md-2 padding-left-none">Quote<i class="fa  fa-caret-down"></i></div>
									@else
									<div class="col-md-3 padding-left-none">Transit Days <i class="fa  fa-caret-down"></i></div>
									<div class="col-md-3 padding-left-none">Quote<i class="fa  fa-caret-down"></i></div>
									@endif
									
									@if(!empty($compareid))
                                    <div class="col-md-2 padding-none text-left">
                                     Rank
                                     </div>
                                    @endif
									<div class="col-md-3 padding-left-none"></div>
								</div>

								<!-- Table Head Ends Here -->

								<div class="table-data">
									<?php //echo "<pre>"; print_r($seller_quote_details);exit;?>
									<!-- Table Row Starts Here -->
									@foreach($seller_quote_details as $seller_quote_detail)
                                        {{--*/ $sp_item_id = $seller_quote_detail->seller_post_id /*--}}
                                        {{--*/ $sp_item_id = $seller_quote_detail->seller_post_id /*--}}
									<div class="table-row inner-block-bg">
											<div class="col-md-3 padding-left-none">
												<input type="checkbox" class="checksellres" name="check_{{$seller_quote_detail->seller_id}}" id="check_{{$seller_quote_detail->seller_id}}" value="{{$seller_quote_detail->seller_id}}"><span class="lbl padding-8"></span>
												{{ ucfirst($seller_quote_detail->username) }}
												<div class="red rating-margin">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
											</div>

										@if(!empty($compareid))
										<div class="col-md-2 padding-left-none">{{$seller_quote_detail->transit_days}}</div>
										<div class="col-md-2 padding-none">{{$seller_quote_detail->total_price}} /-</div>
										@else
										<div class="col-md-3 padding-left-none">{{$seller_quote_detail->transit_days}}</div>
										<div class="col-md-3 padding-none">{{$seller_quote_detail->total_price}} /-</div>
										@endif
										
										 @if(!empty($compareid))
                                          <div class="col-md-2 padding-none text-left">
                                           {!! $seller_quote_detail->rank !!}
                                           </div>
                                          @endif
	 									<!--button class="btn red-btn pull-right">Book Now</button-->
                                        @if($commonComponent->CheckCartItem($seller_quote_detail->buyer_quote_id)==1)
                                        <div class="col-md-3 padding-none ">
                                        <input type="button" class="btn red-btn pull-right buyer_book_now" data-url="{{ $url.$seller_quote_detail->buyer_quote_id.'/'.$seller_quote_detail->id }}"
                                         id = "buyer_book_now_{{ $seller_quote_detail->buyer_quote_id }}" data-buyerpostofferid="{{ $seller_quote_detail->buyer_quote_id }}" value="Book Now" />
                                        </div>
                                        @elseif($commonComponent->CheckCart($seller_quote_detail->buyer_quote_id,$sp_item_id)==1)
                                        <div class="col-md-3 padding-none">
                                        <button class="btn red-btn pull-right buyer_submit_counter_offer">Booked</button>
                                        </div>
                                        @endif	

											<div class="clearfix"></div>

											<div class="pull-right text-right">
												<div class="info-links">
													<a class="viewcount_show-data-link view_count_update" data-quoteId="{{$seller_quote_detail->private_seller_quote_id}}"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
													<a href="#" class="underline_link new_message" data-transaction_no="{{$seller_quote_detail->transaction_no}}" data-userid="{{$seller_quote_detail->seller_id}}" data-id="{{$seller_quote_detail->buyer_quote_id}}" data-buyerquoteitemid="{{ $seller_quote_detail->seller_post_id }}"><i class="fa fa-envelope-o"></i></a>
												</div>
											</div>

											<div class="col-md-12 show-data-div padding-top" style="display: none;">
												<div class="col-md-3 padding-left-none">
													<span class="data-value">O &amp; D Charges Flat : {{$seller_quote_detail->doortodoor_charges}} /-</span>
												</div>
										
												<div class="col-md-3 padding-left-none">
													<span class="data-value">Freight (Rate per KG) - {{$seller_quote_detail->rate_per_cft}} /-</span>
												</div>

												<div class="col-md-3 padding-left-none">
													<span class="data-value">Transit Days : {{$seller_quote_detail->transit_days}} Days</span>
												</div>

												<div class="col-md-3 padding-left-none">
													<span class="data-value">&nbsp;</span>
												</div>
								 			</div>

										</div>
									
									<!-- Table Row Ends Here -->
								@endforeach

									

									
                                                                </div>

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
@endsection