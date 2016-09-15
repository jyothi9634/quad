@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationOffice\RelocationOfficeSellerComponent')
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
{{--*/ $dispatchDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date) /*--}}
{{--*/ $deliveryDate=$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date) /*--}}

<div class="main">
	<div class="container">
		
		<!-- Page top navigation Starts Here-->
		@include('partials.page_top_navigation')

					<div class="clearfix"></div>

				
		       
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Relocation Office Move - {{$buyer_post_details[0]->transaction_id}}</h1></span>
			
				<div class="filter-expand-block">
				<!-- Search Block Starts Here -->
				<div class="search-block inner-block-bg margin-bottom-less-1">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
							{{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}</span>
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
						<p class="search-head">Approximate Distance</p>
						<span class="search-result">{{$buyer_post_details[0]->distance}} KM</span>
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
						{{--*/ $rprice = $seller_quote_detail->total_price /*--}}
							<i class="fa fa-rupee"></i> {{$seller_quote_detail->total_price}} /-
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