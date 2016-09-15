@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationbuyercomponent', 'App\Components\Relocation\RelocationBuyerComponent')
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
<div class="main">
	<div class="container">

		<!-- Page top navigation Starts Here-->
		@include('partials.page_top_navigation')

					<div class="clearfix"></div>



				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Relocation - {{$buyer_post_details[0]->transaction_id}}</h1></span>
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
								{{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}
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
								<div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Category Type<span>
                                        <span class="data-value">
                                         {{$commonComponent->getVehicleCategorytypeById($buyer_post_details[0]->lkp_vehicle_category_type_id)}}
                                        </span>
                                    </div>

                                <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Category Type<span>
                                        <span class="data-value">
                                         {{$buyer_post_details[0]->vehicle_model}}
                                        </span>
                                    </div>
							   @endif
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Post Type</span>
									<span class="data-value">
									@if($buyer_post_details[0]->lkp_quote_access_id==1)
									Public
									@else
									Private
									@endif
									</span>
								</div>
								
                                 @if(isset($seller_quote_details) && !empty($seller_quote_details))
                                 @foreach($seller_quote_details as $key=>$seller_quote_detail)
								 @if($seller_quote_detail->id == $buyerQuoteSellerPriceId)
                               	 <div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Price</span>
									<span class="data-value">
									
									@if(isset($seller_quote_detail->total_price))
									{{--*/ $rprice = $seller_quote_detail->total_price /*--}}
									{{ $seller_quote_detail->total_price }}
									@endif
									</span>
								</div>
								
								
                                                           
                                                                <span style="display:none;" data-price="{!! $seller_quote_detail->total_price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                                                                </span>
                                                                {{--*/ $sellerpost  =   $commonComponent->getSellersQuotesFromId($seller_quote_detail->private_seller_quote_id) /*--}}
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
							</div>
							<div class="clearfix"></div>
						</div>
		      		</div>
					<!--toggle div ends-->

				</div>
                                

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