@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationbuyercomponent', 'App\Components\RelocationInt\RelocationIntBuyerComponent')
@inject('relOceanSellerCComponent', 'App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent')

		<!-- Page top navigation Starts Here-->
@if(!empty($_REQUEST) )
    
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
{{--*/ $fromCity = $commonComponent->getCityName($buyer_post_details[0]->from_location_id) /*--}}
{{--*/ $toCity = $commonComponent->getCityName($buyer_post_details[0]->to_location_id) /*--}}
{{--*/ $dispatchDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date) /*--}}
{{--*/ $deliveryDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date) /*--}}
<div class="main">
	<div class="container">
		
		<!-- Page top navigation Starts Here-->
		@include('partials.page_top_navigation')

					<div class="clearfix"></div>

				
		       
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Relocation International - {{$buyer_post_details[0]->transaction_id}}</h1></span>
<!-- 				<span class="pull-right"> -->
<!-- 					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i>{{$ptlbuyercomponent->updateBuyerQuoteDetailsViews($buyer_post_details[0]->id,'relocation_buyer_post_views')}}</a> -->
<!-- 					<a href="javascript:history.back()" class="back-link1">Back to Posts</a> -->
<!-- 				</span> -->
				

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
								@if($buyer_post_details[0]->delivery_date != "" && $buyer_post_details[0]->delivery_date != "0000-00-00")
									{{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}  
								@else
									NA
								@endif
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Type</p>
						<span class="search-result">
							@if($buyer_post_details[0]->lkp_international_type_id == INTERNATIONAL_TYPE_AIR)
	                        <span class="search-result">Air</span>
	                        @else
	                        <span class="search-result">Ocean</span>
	                        @endif	
						</span>
					</div>
					@if($buyer_post_details[0]->lkp_international_type_id == INTERNATIONAL_TYPE_AIR)
					<div>
						<p class="search-head">Weight</p>
						<span class="search-result">
							{{ $buyer_post_details[0]->total_cartons_weight }} KGs
						</span>
					</div>
					<div>
						<p class="search-head">No of Cartons</p>
						<span class="search-result">{{ $total_cartons }}
						
						</span>
					</div>
					@endif	
					@if($buyer_post_details[0]->lkp_international_type_id == INTERNATIONAL_TYPE_OCEAN)
					
					<div>
						<p class="search-head">Volume</p>
						<span class="search-result">
						   {{--*/ $totalCFT=$relOceanSellerCComponent->getVolumeCft($buyer_post_details[0]->id) /*--}} 
                           {{--*/ $volume=round($totalCFT/35.5, 2) /*--}}                                  
                           {{$volume}} CBM
						</span>
					</div>
					<div>
						<p class="search-head">No of Items</p>
						<span class="search-result">
							{{ count($buyer_post_inventory_details) }} 
						</span>
					</div>
					<div>
						<p class="search-head">Property Type</p>
						<span class="search-result">
							{{$commonComponent->getPropertyType($buyer_post_details[0]->lkp_property_type_id)}} 
						</span>
					</div>
					@endif				
					<div class="text-right filter-details">
							<!--div class="info-links">
								<a class="transaction-details-expand"><span class="show-icon">+</span>
									<span class="hide-icon">-</span> Details
								</a>
							</div-->
						</div>
				</div>

				

				

				<!-- Search Block Ends Here -->

				<!--toggle div starts-->
					<!--div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
					   	<div class="expand-block">
					   		<div class="col-md-12">
					   		
								<div class="col-md-2 padding-left-none data-fld">
								    <span class="data-head">Property Type</span>
									<span class="data-value"></span>
								</div>
							   <div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">CFT</span>
									<span class="data-value">
									</span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Load Type</span>
									<span class="data-value"> 
									
                                    </span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Inventory Details</span>
									<span class="data-value">
									
									</span>
								</div>
									
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Post Type</span>
									<span class="data-value">
									
									</span>
								</div>
                                                           <div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Price</span>
									<span class="data-value">
									@if(isset($seller_quote_details[0]->total_price))
									{{ $seller_quote_details[0]->total_price }}
									@endif 
									</span>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
		      		</div-->
		      		
					<!--toggle div ends-->

				</div>
                                @if(isset($seller_quote_details) && !empty($seller_quote_details))
                                    @foreach($seller_quote_details as $key=>$seller_quote_detail)
                                   
                                    @if($seller_quote_detail->id == $buyerQuoteSellerPriceId)
                                    <div class="search-block inner-block-bg">
                                        <div class="from-to-area">
                                            <p class="search-head">Vendor Name</p>
                                            <span class="search-result">
                                                {!! $seller_quote_detail->username !!}
                                            </span>
                                            <div class="red">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        </div>

                                        <div class="date-area">
                                            <p class="search-head">Transit Time</p>
                                            <span class="search-result">
                                                {{ $seller_quote_detail->transit_days }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="search-head">Price</p>
                                            <span class="search-result">
                                                <i class="fa fa-rupee"></i> 
                                                {{--*/ $rprice = $seller_quote_detail->total_price /*--}}
                                                {{ $seller_quote_detail->total_price }}
                                            @if(!empty($seller_quote_detail->total_price))
                                                /-
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                    <span style="display:none;" data-price="{!! $seller_quote_detail->total_price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                                </span>
                                {{--*/ $sellerpost  =   $commonComponent->getSellersQuotesFromId($seller_quote_detail->seller_post_id) /*--}}
				{{--*/ $exactDispatchDate = ($buyer_post_details[0]->dispatch_date == '0000-00-00') ? '' : $buyer_post_details[0]->dispatch_date /*--}}
                                {{--*/ $exactDeliveryDate = ($buyer_post_details[0]->delivery_date == '0000-00-00') ? '' : $buyer_post_details[0]->delivery_date /*--}}
                                <input type="hidden" name="priceval" id="priceval" value="{!! $seller_quote_detail->total_price !!}">
                                {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyer_post_details[0]->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteId, $seller_quote_detail->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $seller_quote_detail->buyer_quote_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('seller_post_item_id_'.$buyerQuoteId, $seller_quote_detail->seller_post_id, array('id' => 'seller_post_item_id_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId,
                                            $exactDispatchDate, array('id' =>'buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId,
                                            $exactDeliveryDate, array('id' =>'buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_counter_offer_seller_from_date_'.$buyerQuoteId,
                                            $sellerpost->from_date, array('id' =>'buyer_counter_offer_seller_from_date_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('buyer_counter_offer_seller_to_date_'.$buyerQuoteId,
                                            $sellerpost->to_date, array('id' =>'buyer_counter_offer_seller_to_date_'.$buyerQuoteId)) !!}
                                {!! Form::hidden('fdispatch-date_'.$buyerQuoteId,
                                            $exactDispatchDate, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}            

                                    @endif     
                                    @endforeach
                                @endif        
                                
				<div class="clearfix"></div>
                                <div class="col-md-12 padding-none">
                                    <div class="main-inner"> 
                                        <!-- Right Section Starts Here -->
                                        <div class="main-right">
                                            {{--*/ $buyerQuoteId = $buyerQuoteId /*--}}
                                            {!! Form::open(array('url' => '#', 'id' => 'addbuyerpostcounteroffer', 'name' => 'addbuyerpostcounteroffer')) !!}
                                                @include('partials.buyer_booknow')
                                            {!! Form::close() !!}
                                            <span class="buyer_post_details_url" data-url="{{ url('cart') }}"></span>
                                            <input type="hidden" name="buyer_name" id="buyer_name" value="{{Auth::User()->username}}">
                                        </div>
                                    </div>
                                </div>

			</div>
		</div>



	{{--*/ $commercial=1 /*--}}
		<input type="hidden" name="commerical_type" id="commerical_type" value="{{$commercial}}">
		@if($commercial==1)
		@include('partials.gsa_booknow')
		@endif
@include('partials.footer')
@endsection