@extends('app') @section('content')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
        {{--*/ $id = $data->id /*--}}
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
                                        <span class="location-text"> {!! $toCity !!}</span>
                                </span>
                        </div>
                        <div class="date-area">
                                <div class="col-md-6 padding-none">
                                        <p class="search-head">Date</p>
                                        <span class="search-result">
                                                <i class="fa fa-calendar-o"></i>
<!--								15 Jul 2015-->{!! Session::get('session_dispatch_date_buyer') !!}
                                        </span>
                                </div>
                                
                        </div>

                        <div>
                                <p class="search-head">Service Type</p>
                                <span class="search-result">{{ $commonComponent->getAllGMServiceTypesById(Session::get('session_service_type_relocation')) }}</span>
                        </div>
                        <div>
                                <p class="search-head">Numbers</p>
                                <span class="search-result">{{ Session::get('session_measurement_relocation') }}</span>
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
                                <p class="search-head">Price</p>
                                <span class="search-result">
                                    <i class="fa fa-rupee"></i> 
                                    {!! $allInput['buyersearch_booknow_seller_price_'.$id] !!}
                                @if(!empty($seller_post[0]->price))
                                    /-
                                @endif
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('buyersearch_booknow_buyer_id_'.$id, Auth::User()->id, array('id' => 'buyersearch_booknow_buyer_id_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_id_'.$id, $seller_post[0]->seller_id, array('id' => 'buyersearch_booknow_seller_id_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$id, $allInput['buyersearch_booknow_seller_price_'.$id], array('id' => 'buyersearch_booknow_seller_price_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_from_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'buyersearch_booknow_from_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_to_date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDeliveryDate), array('id' =>'buyersearch_booknow_to_date_'.$id)) !!}
                                {!! Form::hidden('buyersearch_booknow_dispatch_date_'.$id,
                                            $commonComponent->convertDateForDatabase(Session::get('session_dispatch_date_buyer')), array('id' =>'buyersearch_booknow_dispatch_date_'.$id,'class'=>'flexyDispatch')) !!}
                                
                                {!! Form::hidden('fdispatch-date_'.$id,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'fdispatch-date_'.$id)) !!}              
                            </div>
                        </div>
                    
                @endif
				<div class="clearfix"></div>
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            {{--*/ $buyerQuoteId = $id /*--}}
                            {{--*/ $booknow_flag = 1 /*--}}
                            {!! Form::open(array('url' => '#', 'id' => 'ftl-buyer-search-booknow', 'name' => 'ftl-buyer-leads-booknow')) !!}
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