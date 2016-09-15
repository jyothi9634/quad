@extends('app') @section('content')

{{-- Default Variable Start --}}
    {{--*/ $id = '' /*--}}
    {{--*/ $postid = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $postStatusName = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
    {{--*/ $rprice = '' /*--}}
    {{--*/ $fromCity = '' /*--}}
    {{--*/ $toCity = '' /*--}}
    {{--*/ $toCityid = '' /*--}}    
    {{--*/ $deliveryDate = '' /*--}}    
    {{--*/ $dispatchDate = '' /*--}}
    {{--*/ $seller_post_ID = '' /*--}}

    {{--*/ $serviceId = Session::get('service_id') /*--}}
{{-- Default Variable End --}}

{{-- Inject required Components Start --}}
    @inject('buyerCommonComponent', 'App\Components\BuyerComponent')
    @inject('commonComponent', 'App\Components\CommonComponent')
{{-- Inject required Components End --}}

@if(isset($seller_post) && !empty($seller_post))
    @foreach ($seller_post as $data)
        {{--*/ $id = $data->id /*--}}
        @if(isset($data->postid))
            {{--*/ $postid = $data->postid /*--}}
        @endif

        @if($postid)
            {{--*/ $seller_post_ID = $postid /*--}}
        @else
            {{--*/ $seller_post_ID = $id /*--}}
        @endif    

        {{--*/ $transactionId = $data->transaction_id /*--}}
        
        {{--*/ $postStatusName = $data->lkp_post_status_id /*--}}
        @if(isset($data->cost))
            {{--*/ $rprice = $data->cost /*--}}
        @endif
        {{--*/ $Dispatch_Date = ($data->to_date == '0000-00-00') ? '' :$data->to_date; /*--}}

        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach
@endif

@if(isset($fromLocation) && !empty($fromLocation))
    {{--*/ $fromCity = $fromLocation /*--}}
@endif
@if(isset($toLocation) && !empty($toLocation))
    {{--*/ $toCity = $toLocation /*--}}
@endif
@if(isset($toLocationid) && !empty($toLocationid))
    {{--*/ $toCityid = $toLocationid /*--}}
@endif
@if(isset($deliveryDate) && !empty($deliveryDate))
    {{--*/ $deliveryDate = $deliveryDate /*--}}
@endif
@if(isset($dispatchDate) && !empty($dispatchDate))
    {{--*/ $dispatchDate = $dispatchDate /*--}}
@endif


<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">

            <div class="container">
				
				<div class="clearfix"></div>

				{{-- Commented for Post / view count / Delete / Back to posts
                    <span class="pull-right">
                        <a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
                        
                        <a href="{{ url('buyerposts/') }}" class="back-link1">Back to Posts</a>
                    </span>
                --}}

				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
                                @if($fromCity)
                                    {!! $fromCity !!} 
                                @endif
                                @if($toCity) 
                                    @if($fromCity)
                                        to 
                                    @endif
                                    {!! $toCity !!} 
                                @endif
                            </span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">
                                @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
                                    Dispatch Date
                                @else
                                    Date    
                                @endif    
                                </p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
                                {!! Session::get('searchMod.dispatch_date_buyer') !!}
							</span>
						</div>
                        
                        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY) 
    						<div class="col-md-6 padding-none">
    							<p class="search-head">Delivery Date</p>
    							<span class="search-result">
    								<i class="fa fa-calendar-o"></i>
                                        @if(Session::get('searchMod.delivery_date_buyer') == "0000-00-00" || Session::get('searchMod.delivery_date_buyer') == "" )
                                            N / A
                                        @else
                                            {!! Session::get('searchMod.delivery_date_buyer') !!}
                                        @endif
    							</span>
    						</div>
                        @endif
					</div>
					
                    @if($serviceId==RELOCATION_DOMESTIC)
    					@if(Session::get('searchMod.household_items')!='' && Session::get('searchMod.household_items') == 1)
        					<div>
        						<p class="search-head">Property Type</p>
        						<span class="search-result">{{ $commonComponent->getPropertyType(Session::get('searchMod.property_type')) }}</span>
        					</div>
        					<div>
        						<p class="search-head">CFT</p>
        						@if(Session::has('searchMod.total_hidden_volume') && Session::get('searchMod.total_hidden_volume')!="")
            						<span class="search-result">{{Session::get('searchMod.total_hidden_volume')}}</span>
        						@else
        	   	       				<span class="search-result">{{Session::get('searchMod.volume')}}</span>
        						@endif
        					</div>
        					<div>
        						<p class="search-head">Load Type</p>
        						<span class="search-result">
        						@if(Session::get('searchMod.load_type')!='')
            						@if(Session::get('searchMod.load_type')==1)
                						Full Load
            						@else
                 						Part Load
            						@endif
        						@else
            						-
        						@endif
        						</span>
        					</div>
    					@else
        					<div>
        						<p class="search-head">Vehicle Type</p>
        						<span class="search-result">{{ $commonComponent->getVehicleCategoryById(Session::get('searchMod.vehicle_category')) }}</span>
        					</div>
        					<div>
        						<p class="search-head">Vehicle Model</p>
        						<span class="search-result">
                                    @if(Session::get('searchMod.vehicle_model'))
                                        {{ Session::get('searchMod.vehicle_model') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
        					</div>
        					<div>
        						<p class="search-head">Category Type</p>
        						<span class="search-result">
                                @if(Session::get('searchMod.vehicle_category_type'))
                                    {{ $commonComponent->getVehicleCategorytypeById(Session::get('searchMod.vehicle_category_type')) }}
                                @else
                                    N/A
                                @endif
        						</span>
        					</div>
    					@endif
                    @elseif($serviceId==RELOCATION_PET_MOVE)    
                        
                        <div>
                            <p class="search-head">Pet Type</p>
                            <span class="search-result">
                                {{ $commonComponent->getPetType(Session::get('searchMod.pet_type_reslocation')) }}
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Breed</p>
                            <span class="search-result">
                               @if(Session::get('searchMod.breed_type_reslocation')!=0 && Session::get('searchMod.breed_type_reslocation')!='') 
                                   {{ $commonComponent->getBreedType(Session::get('searchMod.breed_type_reslocation')) }}
                               @else
                                   NA
                               @endif
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Cage Type</p>
                            <span class="search-result">
                                {{ $commonComponent->getCageType(Session::get('searchMod.cage_type_reslocation')) }}
                            </span>
                        </div>
                       
                    @elseif($serviceId==RELOCATION_OFFICE_MOVE) 
                        <div>
                            <p class="search-head">CFT</p>
                            <span class="search-result">{{ Session::get('searchMod.volume_buyer') }}</span>
                        </div>
                    @elseif($serviceId==RELOCATION_GLOBAL_MOBILITY) 
                        <div>
                            <p class="search-head">Service Type</p>
                            <span class="search-result">{{ $commonComponent->getAllGMServiceTypesById(Session::get('searchMod.service_type_relocation')) }}</span>
                        </div>
                        <div>
                            <p class="search-head">Numbers</p>
                            <span class="search-result">{{ Session::get('searchMod.measurement_relocation') }}</span>
                        </div>
                    @elseif($serviceId==RELOCATION_INTERNATIONAL)
                        <div>
                            <p class="search-head">Type</p>
                            @if(Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_AIR)
                            <span class="search-result">Air</span>
                            @else
                            <span class="search-result">Ocean</span>
                            @endif                      
                        </div>
                        @if(Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_AIR)
                            <div>
                                <p class="search-head">Weight</p>
                                <span class="search-result">{{ Session::get('session_weight_buyer') }} KGs</span>
                            </div>
                            <div>
                                <p class="search-head">No of Cartons</p>
                                <span class="search-result">{{ Session::get('session_cartons_count_buyer') }}</span>
                            </div>
                            <div>
                                <p class="search-head">Volume</p>
                                <span class="search-result">{{ Session::get('session_volume_buyer') }} CFT</span>
                            </div>
                        @endif
                        @if(Session::get('session_service_type_buyer') == INTERNATIONAL_TYPE_OCEAN)
                        
                        <div>
                            <p class="search-head">Volume</p>
                            <span class="search-result">
                               {{ Session::get('session_ocean_search_volume') }} CBM
                            </span>
                        </div>
                        <div>
                            <p class="search-head">No of Items</p>
                            <span class="search-result">
                                {{ Session::get('session_ocean_search_no_of_items') }}
                            </span>
                        </div>
                        <div>
                            <p class="search-head">Property Type</p>
                            <span class="search-result">
                                {{$commonComponent->getPropertyType(Session::get('session_property_type_buyer'))}} 
                            </span>
                        </div>
                        @endif
                    @endif										
					<div class="text-right filter-details">
						<!--<a href="#">+ Details</a>-->
					</div>
				</div>
                <!-- Search Block Ends Here -->
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
                            @if(isset($seller_post[0]->transitdays)) 
                                <div>
                                    <p class="search-head">Transit Time</p>
                                    <span class="search-result">
                                        {!! $seller_post[0]->transitdays !!} {!! $seller_post[0]->units !!}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <p class="search-head">Price</p>
                                <span class="search-result">
                                    <i class="fa fa-rupee"></i> 
                                    {{--*/ $rprice = $allInput['buyersearch_booknow_seller_price_'.$seller_post_ID] /*--}}
                                    {!! $commonComponent->number_format($rprice,true) !!}
                                </span>
                            </div>
                            <div>
                                {!! Form::hidden('buyersearch_booknow_buyer_id_'.$seller_post_ID, Auth::User()->id, array('id' => 'buyersearch_booknow_buyer_id_'.$seller_post_ID)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_id_'.$seller_post_ID, $seller_post[0]->seller_id, array('id' => 'buyersearch_booknow_seller_id_'.$seller_post_ID)) !!}
                                {!! Form::hidden('buyersearch_booknow_seller_price_'.$seller_post_ID, $allInput['buyersearch_booknow_seller_price_'.$seller_post_ID], array('id' => 'buyersearch_booknow_seller_price_'.$seller_post_ID)) !!}
                                {!! Form::hidden('buyersearch_booknow_from_date_'.$seller_post_ID,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'buyersearch_booknow_from_date_'.$seller_post_ID)) !!}
                                {!! Form::hidden('buyersearch_booknow_to_date_'.$seller_post_ID,
                                            $commonComponent->convertDateForDatabase($exactDeliveryDate), array('id' =>'buyersearch_booknow_to_date_'.$seller_post_ID)) !!}
                                {!! Form::hidden('buyersearch_booknow_dispatch_date_'.$seller_post_ID,
                                            $commonComponent->convertDateForDatabase(Session::get('searchMod.dispatch_date_buyer')), array('id' =>'buyersearch_booknow_dispatch_date_'.$seller_post_ID,'class'=>'flexyDispatch')) !!}
                                {!! Form::hidden('buyersearch_booknow_delivery_date_'.$seller_post_ID,
                                            $commonComponent->convertDateForDatabase(Session::get('searchMod.delivery_date_buyer')), array('id' =>'buyersearch_booknow_delivery_date_'.$seller_post_ID)) !!}
                                {!! Form::hidden('fdispatch-date_'.$seller_post_ID,
                                            $commonComponent->convertDateForDatabase($exactDispatchDate), array('id' =>'fdispatch-date_'.$seller_post_ID)) !!}              
                            </div>
                        </div>
                    
                @endif
				<div class="clearfix"></div>
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            {{--*/ $buyerQuoteId = $seller_post_ID /*--}}
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
		{{--*/ $commercial=Session::get('session_is_commercial_date_buyer') /*--}}
		<input type="hidden" name="commerical_type" id="commerical_type" value="1">
		
	@include('partials.gsa_booknow')
	@include('partials.footer')

@endsection