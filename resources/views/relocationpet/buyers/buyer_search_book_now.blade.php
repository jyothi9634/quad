@extends('app') @section('content')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $postid = $data->postid /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $postStatusName = $data->lkp_post_status_id /*--}}
        <?php  $Dispatch_Date = ($data->to_date == '0000-00-00') ? '' :$data->to_date; ?>
        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $postStatusName = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
@endif
@if(isset($fromLocation) && !empty($fromLocation))
    {{--*/ $fromCity = $fromLocation /*--}}
@else
    {{--*/ $fromCity = '' /*--}}
@endif
@if(isset($toLocation) && !empty($toLocation))
    {{--*/ $toCity = $toLocation /*--}}
@else
    {{--*/ $toCity = '' /*--}}
@endif
@if(isset($toLocationid) && !empty($toLocationid))
    {{--*/ $toCityid = $toLocationid /*--}}
@else
    {{--*/ $toCityid = '' /*--}}
@endif
@if(isset($deliveryDate) && !empty($deliveryDate))
    {{--*/ $deliveryDate = $deliveryDate /*--}}
@else
    {{--*/ $deliveryDate = '' /*--}}
@endif
@if(isset($dispatchDate) && !empty($dispatchDate))
    {{--*/ $dispatchDate = $dispatchDate /*--}}
@else
    {{--*/ $dispatchDate = '' /*--}}
@endif
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">
            <div class="container">

                <div class="clearfix"></div>
                <div class="search-block inner-block-bg">
                        <div class="from-to-area">
                                <span class="search-result">
                                        <i class="fa fa-map-marker"></i>
                                        <span class="location-text">{!! $fromCity !!} to {!! $toCity !!}</span>
                                </span>
                        </div>
                        <div class="date-area">
                                <div class="col-md-6 padding-none">
                                        <p class="search-head">Dispatch Date</p>
                                        <span class="search-result">
                                                <i class="fa fa-calendar-o"></i>
<!--								15 Jul 2015-->{!! Session::get('session_dispatch_date_buyer') !!}
                                        </span>
                                </div>
                                <div class="col-md-6 padding-none">
                                        <p class="search-head">Delivery Date</p>
                                        <span class="search-result">
                                                <i class="fa fa-calendar-o"></i>
                @if(Session::get('session_delivery_date_buyer') == "0000-00-00" || Session::get('session_delivery_date_buyer') == "" )
                    N / A
                @else
                  {!! Session::get('session_delivery_date_buyer') !!}
                @endif


                                        </span>
                                </div>
                        </div>
                        {{--*/ $searchrequest=Session::get('relocbuyerrequest'); /*--}}
                        <?php //print_r(Session::get('relocbuyerrequest')); ?>
                        <div>
                            <p class="search-head">Pet Type</p>
                            <span class="search-result">
                                {{ $commonComponent->getPetType($searchrequest['selPettype']) }}
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Breed</p>
                            <span class="search-result">
                               @if($searchrequest['selBreedtype']!=0 && $searchrequest['selBreedtype']!='') 
                                    {{ $commonComponent->getBreedType($searchrequest['selBreedtype']) }}
                               @else
                               NA
                               @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Cage Type</p>
                            <span class="search-result">
                            {{ $commonComponent->getCageType($searchrequest['selCageType']) }}
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Cage Weight</p>
                            <span class="search-result">
                            {{ $commonComponent->getCageWeight($searchrequest['selCageType']) }} KGs
                            </span>
                        </div>
                        <div class="text-right filter-details">
                                <!--<a href="#">+ Details</a>-->
                        </div>
                </div>
                <!-- Search Block Ends Here -->
                <?php //echo "<pre>";print_r($seller_post);exit;?>
                @if(isset($seller_post) && !empty($seller_post))
                    
                    {{--*/ $buyerQuoteForLeadId = $seller_post[0]->id /*--}}
                        <div class="search-block inner-block-bg">
                            <div class="from-to-area">
                                <p class="search-head">Vendor Name</p>
                                <span class="search-result">
                                    {!! $seller_post[0]->username !!}
                                </span>
                                <div class="red">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                            </div>
                            <div>
                                <p class="search-head">Cage Type</p>
                                <span class="search-result">
                                    {{$commonComponent->getCageType($seller_post[0]->lkp_cage_type_id)}}  
                                    
                                </span>
                            </div> 
                            <div>
                                <p class="search-head">Transit Time</p>
                                <span class="search-result">
                                    {!! $seller_post[0]->transitdays !!} {!! $seller_post[0]->units !!}
                                </span>
                            </div>
                            <div>
                                <p class="search-head">Price</p>
                                <span class="search-result">
                                    <i class="fa fa-rupee"></i> 
                                    {!! $commonComponent->moneyFormat($allInput['buyersearch_booknow_seller_price_'.$postid],true) !!}
                                @if(!empty($seller_post[0]->rate_per_cft))
                                    /-
                                @endif
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('buyersearch_booknow_buyer_id_'.$postid, Auth::User()->id, array('id' => 'buyersearch_booknow_buyer_id_'.$postid)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_id_'.$postid, $seller_post[0]->seller_id, array('id' => 'buyersearch_booknow_seller_id_'.$postid)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$postid, $allInput['buyersearch_booknow_seller_price_'.$postid], array('id' => 'buyersearch_booknow_seller_price_'.$postid)) !!}
                                {!! Form::hidden('buyersearch_booknow_from_date_'.$postid,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'buyersearch_booknow_from_date_'.$postid)) !!}
                                {!! Form::hidden('buyersearch_booknow_to_date_'.$postid,
                                            $commonComponent->convertDateForDatabase($exactDeliveryDate), array('id' =>'buyersearch_booknow_to_date_'.$postid)) !!}
                                {!! Form::hidden('buyersearch_booknow_dispatch_date_'.$postid,
                                            $commonComponent->convertDateForDatabase(Session::get('session_dispatch_date_buyer')), array('id' =>'buyersearch_booknow_dispatch_date_'.$postid,'class'=>'flexyDispatch')) !!}
                                {!! Form::hidden('buyersearch_booknow_delivery_date_'.$postid,
                                            $commonComponent->convertDateForDatabase(Session::get('session_delivery_date_buyer')), array('id' =>'buyersearch_booknow_delivery_date_'.$postid)) !!}
                                {!! Form::hidden('fdispatch-date_'.$postid,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'fdispatch-date_'.$postid)) !!}              
                            </div>
                        </div>
                    
                @endif
				<div class="clearfix"></div>
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            {{--*/ $buyerQuoteId = $postid /*--}}
                            {{--*/ $booknow_flag = 1 /*--}}
                            {!! Form::open(array('url' => '#', 'id' => 'ftl-buyer-search-booknow', 'name' => 'ftl-buyer-leads-booknow')) !!}
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