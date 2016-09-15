@extends('app') @section('content')

{{-- Default Variable Start --}}
	{{--*/ $fromCity = ''; /*--}}
	{{--*/ $toCity = ''; /*--}}
	{{--*/ $dispatchDate = ''; /*--}}
	{{--*/ $deliveryDate = ''; /*--}}
	{{--*/ $exactDeliveryDate = '' /*--}}
	{{--*/ $exactDispatchDate = '' /*--}}

	{{--*/ $serviceId = Session::get('service_id') /*--}}
{{-- Default Variable End --}}

{{-- Inject required Components Start --}}
	@inject('commonComponent', 'App\Components\CommonComponent')
	@inject('relocationbuyercomponent', 'App\Components\Relocation\RelocationBuyerComponent')
	@inject('sellercomponent', 'App\Components\RelocationOffice\RelocationOfficeSellerComponent')
	@if($serviceId == RELOCATION_INTERNATIONAL)
		@inject('relocationbuyercomponent', 'App\Components\RelocationInt\RelocationIntBuyerComponent')
		@inject('relOceanSellerCComponent', 'App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent')
	@endif
{{-- Inject required Components END --}}

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

@if($serviceId==RELOCATION_PET_MOVE)
	{{--*/ $buyer_post_details[0] = $buyer_post_details; /*--}}
@endif

@if(isset($buyer_post_details[0]->from_location_id))
	{{--*/ $fromCity = $commonComponent->getCityName($buyer_post_details[0]->from_location_id) /*--}}
@else
	{{--*/ $fromCity = $commonComponent->getCityName($buyer_post_details[0]->location_id) /*--}}
@endif

@if(isset($buyer_post_details[0]->to_location_id))
	{{--*/ $toCity = $commonComponent->getCityName($buyer_post_details[0]->to_location_id) /*--}}
@endif

{{--*/ $dispatchDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date) /*--}}

@if(isset($buyer_post_details[0]->delivery_date))
	{{--*/ $deliveryDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date) /*--}}
@endif
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
								{{$fromCity}} @if($toCity) to {{$toCity}} @endif
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
								{{$dispatchDate}}
							</span>
						</div>
						@if($deliveryDate)
							<div class="col-md-6 padding-none">
								<p class="search-head">Delivery Date</p>
								<span class="search-result">
									<i class="fa fa-calendar-o"></i>
									{{$deliveryDate}}
								</span>
							</div>
						@endif
					</div>
					@if(isset($buyer_post_details[0]->origin_elevator))
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
					@endif
					@if(isset($buyer_post_details[0]->destination_elevator))
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
					@endif
					@if(isset($buyer_post_details[0]->origin_storage))
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
					@endif
					@if(isset($buyer_post_details[0]->origin_destination))
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
					@endif
					@if(isset($buyer_post_details[0]->distance))
						<div>
							<p class="search-head">Approximate Distance</p>
							<span class="search-result">
								{{$buyer_post_details[0]->distance}} KM
							</span>
						</div>

						<div>
							<p class="search-head">Post Type</p>
								<span class="search-result">
									<i class="fa fa-calendar-o"></i>
									@if($buyer_post_details[0]->lkp_quote_access_id==1)
										Public
									@else
										Private
									@endif 
								</span>
						
						</div>						
					@endif
	                @if(isset($buyer_post_details[0]->pet_type))    
	                    <div>
	                        <p class="search-head">Pet Type</p>
	                        <span class="search-result">
		                        {{$buyer_post_details[0]->pet_type}}  
	                        </span>
	                    </div>
					@endif

					@if($serviceId==RELOCATION_PET_MOVE)
	                    <div>
                            <p class="search-head">Breed</p>
                            <span class="search-result">
	                            @if($buyer_post_details[0]->breed_type!='' && $buyer_post_details[0]->breed_type!='0')
		                            {{$buyer_post_details[0]->breed_type}}  
	                            @else
	    	                        NA
	                            @endif
                            </span>
	                    </div>
					@endif
					@if(isset($buyer_post_details[0]->cage_type))
	                    <div>
                            <p class="search-head">Cage Type</p>
                            <span class="search-result">
	                            {{$buyer_post_details[0]->cage_type}}  
                            </span>
	                    </div>
                    @endif
					@if(isset($buyer_post_details[0]->cage_weight))
	                    <div>
	                        <p class="search-head">Cage Weight</p>
	                        <span class="search-result">
		                        {{$buyer_post_details[0]->cage_weight}}  KGs
	                        </span>
	                    </div>
					@endif
					@if(isset($buyer_post_details[0]->lkp_international_type_id))
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
					@endif
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
					   		@if($serviceId!=RELOCATION_OFFICE_MOVE && $serviceId!=RELOCATION_GLOBAL_MOBILITY)	
						   		@if(isset($buyer_post_details[0]->lkp_post_ratecard_type_id) && $buyer_post_details[0]->lkp_post_ratecard_type_id==1)
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
									@if(isset($buyer_post_details[0]->lkp_vehicle_category_id))
										<div class="col-md-4 padding-left-none data-fld">
	                                        <span class="data-head">Vehicle Category<span>
	                                        <span class="data-value">
	                                         {{$commonComponent->getVehicleCategoryById($buyer_post_details[0]->lkp_vehicle_category_id)}}
	                                        </span>
	                                    </div>
									@endif
									@if(isset($buyer_post_details[0]->vehicle_model))
		                                <div class="col-md-4 padding-left-none data-fld">
	                                        <span class="data-head">Vehicle Model<span>
	                                        <span class="data-value">
	                                         {{$buyer_post_details[0]->vehicle_model}}
	                                        </span>
	                                    </div>
									@endif
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
                            @elseif($serviceId==RELOCATION_OFFICE_MOVE)
								<!-- Table Starts Here -->
								<div class="table-div table-style1 inventory-block-officemove">
									<div class="table-div table-style1 inventory-table padding-none">
										<!-- Table Head Starts Here -->
										<div class="table-heading inner-block-bg">
											<div class="col-md-8 padding-left-none">&nbsp;</div>
											<div class="col-md-4 padding-left-none">No of Items</div>
										</div>
										<!-- Table Head Ends Here -->
											 <div class="table-data">    
	                                            {{--*/ $office_buyer_post_inventory_particulars = $sellercomponent->getBuyerInventaryParticulars($buyer_post_details[0]->id)  /*--}}
	                                            <!-- Table Row Starts Here -->
	                                            @foreach($office_buyer_post_inventory_particulars as $buyer_particulars)
	                                            <div class="table-row inner-block-bg">
	                                                <div class="col-md-8 padding-left-none">{{$buyer_particulars->office_particular_type}}</div>
	                                                <div class="col-md-4 padding-left-none">{{$buyer_particulars->number_of_items}}</div>
	                                            </div>
	                                            @endforeach
	                                            <!-- Table Row Ends Here -->
	                                        </div>
									</div>	
								</div>
                            @elseif($serviceId==RELOCATION_GLOBAL_MOBILITY)
								@include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $buyer_post_details[0]->id])
							@endif	

							</div>
							<div class="clearfix"></div>
						</div>
		      		</div>
					<!--toggle div ends-->

				</div>
                     
				@if(isset($seller_quote_details) && !empty($seller_quote_details))
                    @foreach($seller_quote_details as $key=>$seller_quote_detail)
                        @if($seller_quote_detail->id == $buyerQuoteSellerPriceId)
								<div class="search-block inner-block-bg">
									<div class="from-to-area">
										<p class="search-head">Vendor Name</p>
										<span class="search-result">
											{{$seller_quote_detail->username}}
											{{--*/ $seller_id = $seller_quote_detail->seller_id /*--}}
										</span>
										<div class="red">
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
										</div>
									</div>
									@if(isset($seller_quote_detail->transit_days) && ($serviceId==RELOCATION_PET_MOVE || $serviceId==RELOCATION_INTERNATIONAL))
                                    <div>
                                        <span class="search-head">Transit Time</span>
                                        <span class="search-result">
	                                        {{$seller_quote_detail->transit_days}}  
	                                        @if($seller_quote_detail->transit_days!="")
	                                          Days
	                                        @endif
                                        </span>
                                    </div>
									@endif
									<div>
										<p class="search-head">Price</p>
										<span class="search-result">
										@if($serviceId==RELOCATION_GLOBAL_MOBILITY)	
		                                    {{--*/ $rprice = $commonComponent->getTotalBuyerServicesSellerQuotePrice($buyer_post_details[0]->id,$seller_quote_detail->seller_post_id) /*--}}
										@else	
											{{--*/ $rprice = $seller_quote_detail->total_price; /*--}}
										@endif
											<i class="fa fa-rupee"></i> {{ $commonComponent->number_format($rprice) }}
										</span>
									</div>
									<span style="display:none;" data-price="{!! $rprice !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">
				                                        </span>
                                        {{--*/ $sellerpost  =   $commonComponent->getSellersQuotesFromId($seller_quote_detail->private_seller_quote_id) /*--}}
                                        {{--*/ $exactDispatchDate = ($buyer_post_details[0]->dispatch_date == '0000-00-00') ? '' : $buyer_post_details[0]->dispatch_date /*--}}
										@if(isset($buyer_post_details[0]->delivery_date))
                                        	{{--*/ $exactDeliveryDate = ($buyer_post_details[0]->delivery_date == '0000-00-00') ? '' : $buyer_post_details[0]->delivery_date /*--}}
                                        @endif
                                        <input type="hidden" name="priceval" id="priceval" value="{!! $rprice !!}">
                                        {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyer_post_details[0]->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
                                        {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteId, $seller_quote_detail->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteId)) !!}
										@if(isset($seller_quote_detail->buyer_quote_id))	
                                        	{!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $seller_quote_detail->buyer_quote_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
										@else
						                    {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $seller_quote_detail->buyer_post_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
										@endif
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
								</div>
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