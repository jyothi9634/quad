@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationbuyercomponent', 'App\Components\Relocation\RelocationBuyerComponent')
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

				<span class="pull-left"><h1 class="page-title">Spot Relocation - {{$buyer_post_details[0]->transaction_id}}</h1></span>
				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i>&nbsp;{{$ptlbuyercomponent->updateBuyerQuoteDetailsViews($buyer_post_details[0]->id,'relocation_buyer_post_views')}}</a>
					@if($buyer_post_details[0]->lkp_quote_access_id==2)
					<a href="/editrelocationbuyerquote/{{$buyer_post_details[0]->id}}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
					@endif

					<a href='#' class="delete-icon" onclick='buyerpostcancel({{$buyer_post_details[0]->id}})'><i class="fa fa-trash red" title="Delete"></i></a>
					<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>

				</span>
				

				<div class="filter-expand-block">
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
							{{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}} to {{$commonComponent->getCityName($buyer_post_details[0]->to_location_id)}}
							</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date)}} 
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
                                                                @if($buyer_post_details[0]->delivery_date == '0000-00-00' || $buyer_post_details[0]->delivery_date == '')
                                                                NA
                                                                @else
                                                                {{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}  
                                                                @endif								
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Elevetor Origin</p>
						<span class="search-result">
						@if($buyer_post_details[0]->origin_elevator==1)
						Yes
						@else
						No
						@endif
						</span>
					</div>
					<div>
						<p class="search-head">Elevetor Destination</p>
						<span class="search-result">
						@if($buyer_post_details[0]->destination_elevator==1)
						Yes
						@else
						No
						@endif
						</span>
					</div>
					<div>
						<p class="search-head">Storage Origin</p>
						<span class="search-result">
						@if($buyer_post_details[0]->origin_storage==1)
						Yes
						@else
						No
						@endif
						</span>
					</div>
					<div>
						<p class="search-head">Storage Destination</p>
						<span class="search-result">
						@if($buyer_post_details[0]->origin_destination==1)
						Yes
						@else
						No
						@endif
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
					<div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
					   	<div class="expand-block">
					   		<div class="col-md-12">
					   		@if($buyer_post_details[0]->lkp_post_ratecard_type_id==1)
								<div class="col-md-2 padding-left-none data-fld">
								    <span class="data-head">Property Type</span>
									<span class="data-value">{{$commonComponent->getPropertyType($buyer_post_details[0]->lkp_property_type_id)}}</span>
								</div>
							   <div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">CFT</span>
									<span class="data-value">
									{{--*/ $volume_total = $commonComponent->getVolumeCft($buyer_post_details[0]->id)+$commonComponent->getCratingVolumeCft($buyer_post_details[0]->id) /*--}}
									{{$commonComponent->number_format($volume_total,false)}}
									</span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Load Type</span>
									<span class="data-value"> 
									@if($buyer_post_details[0]->lkp_load_category_id==1) 
                                         Full Load
                                        @else
                                        Part Load
                                        @endif   
                                    </span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Inventory Details</span>
									<span class="data-value">
									@if(count($buyer_post_inventory_details)>0)
									Yes
									@else
									No
									@endif
									</span>
								</div>
								@else
								<div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Category<span>
                                        <span class="data-value">
                                         {{$commonComponent->getVehicleCategoryById($buyer_post_details[0]->lkp_vehicle_category_id)}}  
                                        </span>
                                    </div> 
                                    
								<div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Category Type<span>
                                        <span class="data-value">
                                        @if($buyer_post_details[0]->lkp_vehicle_category_id==1)
                                         {{$commonComponent->getVehicleCategorytypeById($buyer_post_details[0]->lkp_vehicle_category_type_id)}}
                                        @else
                                        NA
                                        @endif   
                                        </span>
                                    </div> 
                                    
                                <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">VehicleModel<span>
                                        <span class="data-value">
                                         {{$buyer_post_details[0]->vehicle_model}}  
                                        </span>
                                    </div> 
							   @endif	
								<div class="col-md-3 padding-left-none data-fld">
									<span class="data-head">Post Type</span>
									<span class="data-value">
									@if($buyer_post_details[0]->lkp_quote_access_id==1)
									Public
									@else
									Private
									@endif 
									</span>
								</div>
							</div>
							
							@if($buyer_post_details[0]->lkp_post_ratecard_type_id==1)
							{{--*/ $particularsDataCount=$commonComponent->getBuyerInventoryParticularsDataInfo($buyer_post_details[0]->id) /*--}}
							@if($particularsDataCount>0)
							<div class="col-md-12">
							{{--*/ $roomTypes=array();/*--}}
							{{--*/ $roomTypes=$commonComponent->getBuyerInventoryRoomsbyId($buyer_post_details[0]->id) /*--}}
							
							@foreach($roomTypes as $roomType)
							<div class="col-md-12 padding-left-none data-fld">
							<span class="data-head">Room Type: {{$roomType->inventory_room_type}}</span>
							</div>
							<div class="clearfix"></div>
							<div class="table-div">
							<div class="table-heading inner-block-bg">
								<div class="col-md-3 padding-left-none">
									<span class="lbl padding-8">Particular
								</div>
								<div class="col-md-3 padding-left-none">Number of Items</div>
								<div class="col-md-3 padding-left-none">Crating</div>
								<div class="col-md-3 padding-left-none"></div>
							</div>
							{{--*/ $particularsData=array();/*--}}
							{{--*/ $particularsData=$commonComponent->getBuyerInventoryParticularsbyId($buyer_post_details[0]->id,$roomType->lkp_inventory_room_id) /*--}}
							<div class="table-data">
								@foreach($particularsData as $particularData)
								
								<div class="table-row inner-block-bg">
								<div class="col-md-3 padding-left-none">{{$particularData->room_particular_type}}</div>
								<div class="col-md-3 padding-left-none">{{$particularData->number_of_items}}</div>
								<div class="col-md-3 padding-left-none">
								@if($particularData->crating_required==1)
								Yes
								@else
								No
								@endif
								</div>
								</div>
								@endforeach
							</div>
							</div>
							@endforeach
							
							</div>
							@endif
							
							@if($buyer_post_details[0]->lkp_quote_access_id==2)
							<div class="col-md-12">
							<span class="data-head">Sellers</span>
							<span class="data-value">
							{{--*/ $privateSellers=array();/*--}}
							{{--*/ $privateSellers=$commonComponent->getSellerNames($buyer_post_details[0]->id) /*--}} 
							
							@foreach($privateSellers as $privateSeller)
							
							{{$privateSeller->username}}<br>
							
							@endforeach
							
							
							</span>
							
							</div>
							@endif
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
									<a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
									<a href="#" class="{{($type=="quotes")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes<span class='badge'>{{$relocationbuyercomponent->getQuotesCount($buyer_post_details[0]->id)}}</span></a>
<!--									<a href="#" class="{{($type=="leads")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-leads"><i class="fa fa-thumbs-o-up"></i> Leads</a>-->
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
							</div>
							<input type="hidden" name="buyer_details_id" id="buyer_details_id" value="{{$buyer_post_details[0]->id}}">
							
							<!-- Table Starts Here -->

							<div class="table-div">
								{{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
                                                                <div id="ftl-buyer-messages" class="tabs-group" {{$msg_style}}>
                                                                    
                                                                        {!! $allMessagesList['grid'] !!}
                                                                    
                                                                </div>
                                                                {{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}} 
                                                                <div id="ftl-buyer-leads" class="tabs-group" {{$leads_style}}>
                                                                    	<div class="table-row inner-block-bg text-center">
                                                                        No data available
                                                                        </div>
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
									<?php //print_r($seller_quote_details);exit;?>
									<!-- Table Row Starts Here -->
									@foreach($seller_quote_details as $seller_quote_detail)
                                     {{--*/ $sp_item_id = $seller_quote_detail->seller_post_id /*--}}
									<div class="table-row inner-block-bg">
										<div class="col-md-3 padding-left-none">
											<input type="checkbox" class="checksellres" name="check_{{$seller_quote_detail->id}}" id="check_{{$seller_quote_detail->seller_id}}" value="{{$seller_quote_detail->seller_id}}"><span class="lbl padding-8"></span>
											{{$seller_quote_detail->username}}
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
												<a class="show-data-link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
												<a href="#" class="underline_link new_message" data-transaction_no="{{$seller_quote_detail->transaction_no}}" data-userid="{{$seller_quote_detail->seller_id}}" data-id="{{$seller_quote_detail->buyer_quote_id}}" data-buyerquoteitemid="{{ $seller_quote_detail->seller_post_id }}"><i class="fa fa-envelope-o"></i></a>
											</div>
										</div>

										<div class="col-md-12 show-data-div padding-top">
											<div class="col-md-3 padding-left-none">
												<span class="data-value">O & D Charges (per CFT) : {{$seller_quote_detail->rate_per_cft}}/-</span>
											</div>
									
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Transport Charges : {{$seller_quote_detail->transport_charges}}/-</span>
											</div>

											<div class="clearfix"></div>

											<div class="col-md-3 padding-left-none">
												<span class="data-head"><u>Additional Charges</u></span>
											</div>

											<div class="clearfix"></div>

											<div class="col-md-3 padding-left-none">
												<span class="data-value">Crating Charges (per CFT) : {{$seller_quote_detail->transport_charges}}/-</span>
											</div>
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Storage Charges (CFT/Day) : {{$seller_quote_detail->storage_charges}}/-</span>
											</div>
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Escort Charges (per Day) : {{$seller_quote_detail->escort_charges}}/-</span>
											</div>
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Handyman Charges (per Hour): {{$seller_quote_detail->handyman_charges}}/-</span>
											</div>

											<div class="clearfix"></div>
											
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Property Search (Rs) : {{$seller_quote_detail->property_search}}/-</span>
											</div>

											<div class="col-md-3 padding-left-none">
												<span class="data-value">Brokerage (Rs) : {{$seller_quote_detail->brokerage_charges}}/-</span>
											</div>
											<div class="col-md-3 padding-left-none">
												<span class="data-value">Total Price (Rs) : {{$seller_quote_detail->total_price}}/-</span>
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