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

{{--*/ $fromCity=$commonComponent->getCityName($buyer_post_details->from_location_id) /*--}}
{{--*/ $toCity=$commonComponent->getCityName($buyer_post_details->to_location_id) /*--}}
{{--*/ $dispatchDate=$buyer_post_details->dispatch_date /*--}}
{{--*/ $deliveryDate=$buyer_post_details->delivery_date /*--}}
<div class="main">
	<div class="container">
		<!-- Page top navigation Starts Here-->
            @include('partials.page_top_navigation')
<?php //print_r($buyer_post_details); ?>
            <div class="clearfix"></div>
            <span class="pull-left"><h1 class="page-title">Spot Relocation - {{$buyer_post_details->transaction_id}}</h1></span>
            <div class="filter-expand-block">
            <!-- Search Block Starts Here -->

            <div class="search-block inner-block-bg margin-bottom-less-1">
                    <div class="from-to-area">
                            <span class="search-result">
                                    <i class="fa fa-map-marker"></i>
                                    <span class="location-text">
                                    {{$commonComponent->getCityName($buyer_post_details->from_location_id)}} to {{$commonComponent->getCityName($buyer_post_details->to_location_id)}}
                                    </span>
                            </span>
                    </div>
                    <div class="date-area">
                            <div class="col-md-6 padding-none">
                                    <p class="search-head">Dispatch Date</p>
                                    <span class="search-result">
                                            <i class="fa fa-calendar-o"></i>
                                            
                                            {{$buyer_post_details->dispatch_date}}
                                    </span>
                            </div>
                            <div class="col-md-6 padding-none">
                                    <p class="search-head">Delivery Date</p>
                                    <span class="search-result">
                                            <i class="fa fa-calendar-o"></i>
                                             
                                            {{$buyer_post_details->delivery_date}}
                                    </span>
                            </div>
                    </div>
                    <div>
                            <p class="search-head">Pet Type</p>
                            <span class="search-result">
                            {{$buyer_post_details->pet_type}}  
                            </span>
                    </div>
                    <div>
                            <p class="search-head">Breed</p>
                            <span class="search-result">
                            @if($buyer_post_details->breed_type!='' && $buyer_post_details->breed_type!='0')
                            {{$buyer_post_details->breed_type}}  
                            @else
                            NA
                            @endif
                            </span>
                    </div>
                    <div>
                            <p class="search-head">Cage Type</p>
                            <span class="search-result">
                            {{$buyer_post_details->cage_type}}  
                            </span>
                    </div>
                    <div>
                            <p class="search-head">Cage Weight</p>
                            <span class="search-result">
                            {{$buyer_post_details->cage_weight}}  KGs
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
                                
                            @if(isset($seller_quote_details) && !empty($seller_quote_details))
                            @foreach($seller_quote_details as $key=>$seller_quote_detail)
                                   
                            @if($seller_quote_detail->id == $buyerQuoteSellerPriceId)    
                            <div class="col-md-12">

                                    <div class="col-md-3 padding-left-none data-fld">
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


                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Cage Type<span>
                                        <span class="data-value">
                                         {{$commonComponent->getCageType($seller_quote_detail->lkp_cage_type_id)}}  
                                        </span>
                                    </div> 

                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Transit Time<span>
                                        <span class="data-value">
                                         {{$seller_quote_detail->transit_days}}  
                                        @if($seller_quote_detail->transit_days!="")
                                          Days
                                        @endif
                                        </span>
                                    </div>
                                   <div class="col-md-3 padding-left-none data-fld">
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
								{{--*/ $exactDispatchDate = ($buyer_post_details->dispatch_date == '00/00/0000') ? '' : $buyer_post_details->dispatch_date /*--}}
                                {{--*/ $exactDeliveryDate = ($buyer_post_details->delivery_date == '00/00/0000') ? '' : $buyer_post_details->delivery_date /*--}}
                                {{--*/ $Dispatch_Date = ($buyer_post_details->dispatch_date == '00/00/0000') ? '' :$commonComponent->convertDateForDatabase($buyer_post_details->dispatch_date) /*--}}
                                
                                <input type="hidden" name="priceval" id="priceval" value="{!! $seller_quote_detail->total_price !!}">
                                {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyer_post_details->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
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
                                            $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}            

                                </div>
                            @endif
                            @endforeach
                                @endif 
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