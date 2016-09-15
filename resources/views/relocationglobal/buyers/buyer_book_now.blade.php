@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlbuyercomponent', 'App\Components\Ptl\PtlBuyerGetQuoteBooknowComponent')
@inject('relocationbuyercomponent', 'App\Components\RelocationGlobal\RelocationGlobalBuyerComponent')
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
{{--*/ $dispatchDate = $commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date) /*--}}
<div class="main">
	<div class="container">
		
		<!-- Page top navigation Starts Here-->
		@include('partials.page_top_navigation')
    <div class="clearfix"></div>
    <span class="pull-left"><h1 class="page-title">Spot Relocation - {{$buyer_post_details[0]->transaction_id}}</h1></span>

				

                    <div class="filter-expand-block">
                    <!-- Search Block Starts Here -->

                    <div class="search-block inner-block-bg margin-bottom-less-1">
                            <div class="from-to-area">
                                    <span class="search-result">
                                            <i class="fa fa-map-marker"></i>
                                            <span class="location-text">
                                            {{$commonComponent->getCityName($buyer_post_details[0]->location_id)}}
                                            </span>
                                    </span>
                            </div>
                            <div class="date-area">
                                    <div class="col-md-6 padding-none">
                                            <p class="search-head">Date</p>
                                            <span class="search-result">
                                                    <i class="fa fa-calendar-o"></i>
                                                    {{$dispatchDate}}
                                            </span>
                                    </div>
                                    
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
                                @include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $buyer_post_details[0]->id])                            
                                @if(isset($seller_quote_details) && !empty($seller_quote_details))
                                    @foreach($seller_quote_details as $key=>$seller_quote_detail)
                                   
                                    @if($seller_quote_detail->id == $buyerQuoteSellerPriceId)
                                <div class="col-md-12">
                                    
                                    <div class="col-md-2 padding-left-none data-fld">
                                        <span class="data-head">Vendor Name</span>
                                        <span class="data-value">
                                        @if(isset($seller_quote_detail->username))
                                        {{$seller_quote_detail->username}}
                                        @endif 
                                        </span>
                                    </div>
                                    {{--*/ $price=0 /*--}}
                                    
                                     {{--*/ $price = $commonComponent->getTotalBuyerServicesSellerQuotePrice($buyer_post_details[0]->id,$seller_quote_detail->seller_post_id) /*--}}

                                    <div class="col-md-2 padding-left-none data-fld">
                                        <span class="data-head">Price</span>
                                        <span class="data-value">
                                        @if(isset($price))
                                        {{ $price }}
                                        @endif 
                                        </span>
                                    </div>
                                   <span style="display:none;" data-price="{!! $price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
                    </span>
                    {{--*/ $sellerpost  =   $commonComponent->getSellersQuotesFromId($seller_quote_detail->private_seller_quote_id) /*--}}
                    {{--*/ $exactDispatchDate = ($buyer_post_details[0]->dispatch_date == '0000-00-00') ? '' : $buyer_post_details[0]->dispatch_date /*--}}
                    <input type="hidden" name="priceval" id="priceval" value="{!! $price !!}">
                    {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyer_post_details[0]->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
                    {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteId, $seller_quote_detail->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteId)) !!}
                    {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $seller_quote_detail->buyer_post_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
                    @if($seller_quote_detail->seller_post_id!=0)
                    {!! Form::hidden('seller_post_item_id_'.$buyerQuoteId, $seller_quote_detail->seller_post_id, array('id' => 'seller_post_item_id_'.$buyerQuoteId)) !!}
                    @else
                    {!! Form::hidden('seller_post_item_id_'.$buyerQuoteId, $seller_quote_detail->private_seller_quote_id, array('id' => 'seller_post_item_id_'.$buyerQuoteId)) !!}
                    @endif
                    {!! Form::hidden('buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId,
                                $exactDispatchDate, array('id' =>'buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId)) !!}
                    {!! Form::hidden('buyer_counter_offer_seller_from_date_'.$buyerQuoteId,
                                $sellerpost->from_date, array('id' =>'buyer_counter_offer_seller_from_date_'.$buyerQuoteId)) !!}
                    {!! Form::hidden('buyer_counter_offer_seller_to_date_'.$buyerQuoteId,
                                $sellerpost->to_date, array('id' =>'buyer_counter_offer_seller_to_date_'.$buyerQuoteId)) !!}
                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteId,
                                $exactDispatchDate, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}              
                                     
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
                                <input type="hidden" name="service_id" id="service_id" value="{{Session::get('service_id')}}">
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