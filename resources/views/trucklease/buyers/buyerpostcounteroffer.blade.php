@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')

@if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
    {{--*/ $countQuotes = count($arrayBuyerQuoteSellersQuotesPrices) /*--}}
@else
    {{--*/ $countQuotes = 0 /*--}}
@endif
@if(!empty($_REQUEST) && isset($_REQUEST['type']))
    
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
{{--*/ $urlForLeads = url().'/buyerbooknowforleads/' /*--}}
@if(isset($arrayBuyerCounterOffer) && !empty($arrayBuyerCounterOffer))
    @foreach ($arrayBuyerCounterOffer as $data)
        {{--*/ $id = $data->id /*--}}
        {{--*/ $transactionId = $data->transaction_id /*--}}
        {{--*/ $buyer_quote_id = $data->buyer_quote_id /*--}}
        {{--*/ $vehicleType = $data->vehicle_type /*--}}
        {{--*/ $priceType = $data->price_type /*--}}
        {{--*/ $quoteAccessType = $data->quote_access /*--}}
        {{--*/ $isCancelled = $data->is_cancelled /*--}}
        {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
    <?php  $Dispatch_Date = ($data->from_date == '0000-00-00') ? '' :$data->from_date; ?>

        {{--*/ $exactDispatchDate = ($data->from_date == '0000-00-00') ? '' : $data->from_date /*--}}
        {{--*/ $exactDeliveryDate = ($data->to_date == '0000-00-00') ? '' : $data->to_date /*--}}
    @endforeach
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $transactionId = '' /*--}}
    {{--*/ $buyer_quote_id = '' /*--}}
    {{--*/ $vehicleType = '' /*--}}
    {{--*/ $priceType = '' /*--}}
    {{--*/ $isCancelled = '' /*--}}
    {{--*/ $postStatus = '' /*--}}
    {{--*/ $exactDispatchDate = '' /*--}}
    {{--*/ $exactDeliveryDate = '' /*--}}
@endif
@if(isset($fromLocation) && !empty($fromLocation))
    {{--*/ $fromCity = $fromLocation /*--}}
@else
    {{--*/ $fromCity = '' /*--}}
@endif

@if(isset($deliveryDate) && !empty($deliveryDate))
    {{--*/ $deliveryDate = $deliveryDate /*--}}
@else
    {{--*/ $deliveryDate = 'NA' /*--}}
@endif
@if(isset($dispatchDate) && !empty($dispatchDate))
    {{--*/ $dispatchDate = $dispatchDate /*--}}
@else
    {{--*/ $dispatchDate = 'NA' /*--}}
@endif

@if(isset($arrayBuyerCounterOffer[0]->from_city_id) && !empty($arrayBuyerCounterOffer[0]->from_city_id))
 {{--*/  $fromLocationId = $arrayBuyerCounterOffer[0]->from_city_id /*--}}
@else
 {{--*/  $fromLocationId = ''/*--}}
@endif

@if(isset($arrayBuyerCounterOffer[0]->to_city_id) && !empty($arrayBuyerCounterOffer[0]->to_city_id))
 {{--*/  $toLocationId = $arrayBuyerCounterOffer[0]->to_city_id /*--}}
@else
 {{--*/  $toLocationId = ''/*--}}
@endif

{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$id,$fromLocationId,$toLocationId); /*--}}  
{{--*/ $docCount = count($docs_buyer) /*--}}
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@elseif(!str_contains("buyerposts",URL::previous()))
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
<!-- Left Nav Ends Here -->
    <!-- Inner Menu starts Here -->
		<div class="main">
			<div class="container">
                 @if (Session::has('cancelsuccessmessage'))
                    <div class="flash alert-info">
                        <p class="text-success col-sm-12 text-center flash-txt-counterofer">{{
                    Session::get('cancelsuccessmessage') }}</p>
                    </div>
                @endif
                
				<!-- Content top navigation Starts Here-->
                @include('partials.content_top_navigation_links')
                <!-- Content top navigation ends Here-->
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Spot Transaction - {!! $transactionId !!}</h1></span>
				<span class="pull-right">
					<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $countview }}</a>
                    @if($postStatus == '2')
                        @if(isset($quoteAccessType) && $quoteAccessType=='Private' )
                            <a href="{{ url('editbuyerquote/'. $buyer_quote_id .'/'. $id) }}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
                        @endif
                        <a href="#" class="delete-icon" data-target='#cancelbuyerpostmodal' data-toggle='modal' onclick='setcancelbuyerpostid({!! $id !!})'><i class="fa fa-trash red" title="Delete"></i></a>
                    @endif
					<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
				</span>

				<!-- Search Block Starts Here -->
                <div class="filter-expand-block">
                    <div class="search-block inner-block-bg margin-bottom-less-1">
                        <div class="from-to-area">
                            <span class="search-result">
                                <i class="fa fa-map-marker"></i>
                                <span class="location-text">{!! $fromCity !!}</span>
                            </span>
                        </div>
                        <div class="date-area">
                            <div class="col-md-6 padding-none">
                                <p class="search-head">From Date</p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
                                    {!! $dispatchDate !!}
                                </span>
                            </div>
                            <div class="col-md-6 padding-none">
                                <p class="search-head">To Date</p>
                                <span class="search-result">
                                    <i class="fa fa-calendar-o"></i>
									{!! $deliveryDate !!}
                                </span>
                            </div>
                        </div>
                       
                        <div>
                            <p class="search-head">Post Status</p>
                            <span class="search-result">{!! $poststatus !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Vehicle Type</p>
                            <span class="search-result">{!! $vehicleType !!}</span>
                        </div>
                        <div>
                            <p class="search-head">Lease Term </p>
                            <span class="search-result">{!! $leaseterm !!}</span>
                        </div>
                        <?php //print_r($privateSellerNames);die();?>
                        
                            @if(isset($privateSellerNames) && !empty($privateSellerNames[0]->username))
                                <div class="text-right filter-details">
                                    <div class="info-links">
                                        <a class="transaction-details-expand"><span class="show-icon">+</span>
                                            <span class="hide-icon">-</span> Details
                                        </a>
                                    </div>
                                </div>
                            @else
                                 <div class="text-right filter-details">
                                    <div class="info-links">
                                        <a class="transaction-details-expand"><span class="show-icon">+</span>
                                            <span class="hide-icon">-</span> Details
                                        </a>
                                    </div>
                                </div>
                            @endif
                    </div>
                    <div class="col-md-12 show-data-div"></div>
                    <!-- Search Block Ends Here -->
                    <!--toggle div starts-->
<!--                 <div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> --> 
<!--                         <div class="expand-block"> -->
<!--                             <div class="col-md-12"> -->
<!--                                 <div class="col-md-2 padding-left-none data-fld"> -->
<!--                                     <span class="data-head">Post private</span> -->
<!--                                     @if(isset($privateSellerNames) && !empty($privateSellerNames)) -->
<!--                                         @foreach($privateSellerNames as $key=>$privateSellerName) -->
<!--                                             <span class="data-value">{!! $privateSellerName->username !!}</span><br/> -->
<!--                                         @endforeach -->
<!--                                     @endif -->
<!--                                 </div> -->
<!--                             </div> -->
<!--                             <div class="clearfix"></div> -->
<!--                         </div> -->
<!--                     </div> -->
                    <div class="show-trans-details-div-expand trans-details-expand" style="display: none;"> 
					   	<div class="expand-block">
					   		<div class="col-md-12">
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Driver</span>
									<span class="data-value">
									@if($driver_availability==1)
									With Driver
									@else
									Without Driver
									@endif
									</span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Fuel</span>
									<span class="data-value">
									@if($fuel==0)
									Not Inculded
									@else
									Included
									@endif
									</span>
								</div>

								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Price Type</span>
									<span class="data-value">
									@if($lkp_quote_price_type_id==1)
									Competitive
									@else
									Firm
									@endif</span>
								</div>

								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Price</span>
									<span class="data-value">{{$price}} /-</span>
								</div>
								<div class="col-md-2 padding-left-none data-fld">
									<span class="data-head">Post</span>
									<span class="data-value">{{$arrayBuyerCounterOffer[0]->quote_access}}</span>
								</div>
								
								<div class="col-md-4 padding-left-none data-fld">
									<span class="data-head">Vehicle Make & Model & Year</span>
									<span class="data-value">{{$vehicle_make_model_year}}</span>
								</div>
								@if(isset($privateSellerNames) && !empty($privateSellerNames) && isset($privateSellerNames[0]->username))
								<div class="col-md-2 padding-left-none data-fld">
                                    <span class="data-head">Post private</span>
                                    
                                        @foreach($privateSellerNames as $key=>$privateSellerName)
                                            <span class="data-value">{!! $privateSellerName->username !!}</span><br/>
                                        @endforeach
                                </div>
                                @endif

							</div>
							<div class="clearfix"></div>
						</div>
		      		</div>
                </div>
                <!--toggle div ends-->

				<!-- Search Block Ends Here -->
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<div class="pull-left">
								<div class="info-links">
									<a href="#" class="{{($type=="messages")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-messages"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a>
									<a href="#" class="{{($type=="quotes")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-quotes"><i class="fa fa-file-text-o"></i> Quotes<span class="badge">{!! $countQuotes !!}</span></a>
									<a href="#" class="{{($type=="leads")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-leads"><i class="fa fa-thumbs-o-up"></i> Leads<span class="badge">{!! count($sellerDetailsLeads) !!}</span></a>
									<a href="#" class="tabs-showdiv" data-showdiv="ftl-buyer-marketanalytics"><i class="fa fa-line-chart"></i> Market Analytics<span class="badge">0</span></a>
									<a href="#" class="{{($type=="documentation")?"active":""}} tabs-showdiv" data-showdiv="ftl-buyer-documentation"><i class="fa fa-file-text-o"></i> Documentation<span class="badge">{{$docCount}}</span></a>
								</div>
							</div>
							@if($priceType == 'Competitive')
                            <div class="col-md-3 pull-right compare-fld">
                                @if($countQuotes!=0)
                                    <div class="normal-select comparision_types_div" data-buyerquoteid = "{{ $id }}">
                                            {!! Form::select('buyer_post_counter_offer_comparision_types', [0=>'Select compare type',2=>'Lowest Price'],
                                                    $comparisonType, ['id' => 'buyer_post_counter_offer_comparision_types', 'class' => 'selectpicker'])!!}
                                    </div>
                                @else
                                    <div class="pull-right">No Quotes to Compare</div>
                                @endif
							</div>
                            @endif
							<!-- Table Starts Here -->
							<div class="table-div">
                                {{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
                                <div id="ftl-buyer-messages" class="tabs-group" {{$msg_style}}>
                                        {!! $allMessagesList['grid'] !!}
                                </div>
                                <div id="ftl-buyer-marketanalytics" class="tabs-group" style="display: none">
                                    <div class="table-data inner-block-bg">
                                        No Data Available
                                    </div>
                                </div>
                                {{--*/ $docu_style   =($type=="documentation")?"style=display:block":"style=display:none" /*--}}                                 
                                <div id="ftl-buyer-documentation" class="tabs-group" {{$docu_style}}>
                                    <div class="table-data inner-block-bg">
                                       @if($docCount>0)                                     
                                        <div class="col-sm-4 padding-right-none">
                                            <h3>List of documents </h3> 
                                            <ul class="popup-list">                                               
                                                
                                                @foreach($docs_buyer as $doc)
                                                <li>{{$doc}}</li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                       @else
                                       No Documents Found
                                       @endif
                                        
                                    </div>
                                </div>
                                {{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}} 
                                <div id="ftl-buyer-leads" class="tabs-group" {{$leads_style}}>
                                    <div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">
                                            <input type="checkbox" id="ftl_select_all_leads_name" type="checkbox" /><span class="lbl padding-8"></span>
                                            Vendor Name<i class="fa  fa-caret-down"></i>
                                        </div>
                                        <div class="col-md-3 padding-left-none">Vehicle Type<i class="fa  fa-caret-down"></i></div>
                                        <div class="col-md-2 padding-left-none">Minimum Lease Period<i class="fa  fa-caret-down"></i></div>
                                        <div class="col-md-2 padding-left-none">Price<i class="fa  fa-caret-down"></i></div>
                                    </div>
                                    <div class="table-data">
                                        @if(isset($sellerDetailsLeads) && !empty($sellerDetailsLeads))
                                            @foreach ($sellerDetailsLeads as $sellerData)
                                                {{--*/ $buyerQuoteForLeadId = $sellerData->id /*--}}
                                                <div class="table-row inner-block-bg totalprice_calc_leads" leads_id="{{$sellerData->id}}">
                                                    <div class="col-md-3 padding-left-none">
                                                        {!! $sellerData->username !!}
                                                        <div class="red">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 padding-left-none">{!! $sellerData->vehicle_type !!}</div>
                                                    <div class="col-md-2 padding-left-none">{!! $sellerData->minimum_lease_period !!}
                                                    @if($sellerData->lkp_trucklease_lease_term_id == 1 )
                                                        Days
                                                        @elseif($sellerData->lkp_trucklease_lease_term_id == 2 )
                                                        Weeks
                                                        @elseif($sellerData->lkp_trucklease_lease_term_id == 3 )
                                                        Months
                                                        @else
                                                        Years
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 padding-none" data-price="{!! $sellerData->price !!}" id="buyer_leads_post_price_{{ $buyerQuoteForLeadId }}">{!! $sellerData->price !!} </div>
                                                    <div class="col-md-2 padding-none text-right table-details buyer_leads_book_now_button_div">
                                                        @if($commonComponent->CheckCartItem($id)==1)
                                                        @if($postStatus == OPEN)
                                                            <input type="button" class="btn red-btn pull-right buyer_leads_book_now" data-url="{{ $urlForLeads.$id.'/'.$buyerQuoteForLeadId }}"
                                                                            id = "buyer_leads_book_now_{{ $buyerQuoteForLeadId }}" data-buyerpostofferid="{{ $buyerQuoteForLeadId }}" value="Book Now" />
                                                        @endif
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- --Buyer leads in seller data -->
                                                    <div class="clearfix"></div>
                                                    
				                                       <div class="pull-right text-right">
															<div class="info-links">
																<a class="viewcount_show-data-link" data-quoteId="{{$sellerData->id}}"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
																<a href="#" class="red underline_link new_message" data-transaction="{{$sellerData->transaction_no}}"  data-userid='{{$sellerData->seller_id}}' data-id="{{$id}}" data-buyerleadsitemid="{{ $buyerQuoteForLeadId }}"><i class="fa fa-envelope-o"></i></a>
															</div>
														</div>                                               
                                                    
		                                               <div class="col-md-12 show-data-div">
														<div class="col-md-12 tab-modal-head">
															<h3>
																<i class="fa fa-map-marker"></i> {!! $sellerData->fromcity !!} 
																<span class="close-icon">x</span>
															</h3>
														</div>
														<div class="col-md-8 data-div">
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Valid From</span>
																<span class="data-value">{{date("d/m/Y", strtotime($sellerData->from_date))}}</span>
															</div>
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Valid To</span>
																<span class="data-value">{{date("d/m/Y", strtotime($sellerData->to_date))}}</span>
															</div>
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Vehicle Type</span>
																<span class="data-value">{!! $sellerData->vehicle_type !!}</span>
															</div>
		
															<div class="clearfix"></div>
		
															
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Payment</span>
																<span class="data-value">
																@if($sellerData->lkp_payment_mode_id == 1)
                                                                                                                                <i class="fa fa-credit-card"></i> Online Payment
                                                                                                                                @elseif($sellerData->lkp_payment_mode_id == 2)
                                                                                                                                <i class="fa fa-rupee"></i> {!! $sellerData->paymentmethod !!}
                                                                                                                                @elseif($sellerData->lkp_payment_mode_id == 3)
                                                                                                                                <i class="fa fa-rupee"></i> {!! $sellerData->paymentmethod !!}
                                                                                                                                @else
                                                                                                                                <i class="fa fa-rupee"></i> {!! $sellerData->paymentmethod !!} | {{$sellerData->credit_period}} {{$sellerData->credit_period_units}} 	
                                                                                                                                @endif   
																</span>
															</div>
															
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Document</span>
																<span class="data-value">0</span>
															</div>
															<div class="col-md-4 padding-left-none data-fld">
																<span class="data-head">Terms & Conditions</span>
																<span class="data-value">{!! $sellerData->terms_conditions !!}</span>
															</div>
		
														</div>
														<div class="col-md-4 margin-bottom">
															<span class="data-head">Total Price</span>
															<span class="data-value big-value">															
															
															{!! $disprice = $sellerData->price !!} /- 															
															{!! Form::hidden('total_leads_price', $disprice, array('id' => 'total_leads_price_'.$sellerData->id)) !!}															
															</span>
														</div>
														<div class="col-md-4 margin-bottom">
															<span class="data-head">Cancellation Charges</span>
															<span class="data-value big-value">{!! $sellerData->cancellation_charge_price !!} /-</span>
														</div>
														<div class="col-md-4 margin-bottom">
															<span class="data-head">Docket Charges</span>
															<span class="data-value big-value">{!! $sellerData->docket_charge_price !!} /-</span>
														</div>
												  </div>	
                                                 
                                                    {!! Form::hidden('leads_seller_post_item_id_'.$buyerQuoteForLeadId, $sellerData->id, array('id' => 'leads_seller_post_item_id_'.$buyerQuoteForLeadId)) !!}
                                                    {!! Form::hidden('buyer_leads_post_seller_id_'.$buyerQuoteForLeadId, $sellerData->seller_id, array('id' => 'buyer_leads_post_seller_id_'.$buyerQuoteForLeadId)) !!}
                                                    {!! Form::hidden('buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId, Auth::User()->id, array('id' => 'buyer_leads_post_buyer_id_'.$buyerQuoteForLeadId)) !!}

                                                    {!! Form::hidden('buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId,
                                                                $exactDispatchDate, array('id' =>'buyer_leads_counter_offer_seller_post_from_date_'.$buyerQuoteForLeadId)) !!}
                                                    {!! Form::hidden('buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId,
                                                                $exactDeliveryDate, array('id' =>'buyer_leads_counter_offer_seller_post_to_date_'.$buyerQuoteForLeadId)) !!}
                                                    {!! Form::hidden('buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId,
                                                                $sellerData->from_date, array('id' =>'buyer_leads_counter_offer_seller_from_date_'.$buyerQuoteForLeadId)) !!}
                                                    {!! Form::hidden('buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId,
                                                                $sellerData->to_date, array('id' =>'buyer_leads_counter_offer_seller_to_date_'.$buyerQuoteForLeadId)) !!}

                                                    {!! Form::hidden('fdispatch-date_'.$buyerQuoteForLeadId,
                                                                    $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteForLeadId)) !!}                                
                                                    <div class='col-md-12 col-sm-12 col-xs-12 padding-none margin-top buyer_leads_booknow_listdetails_{{ $buyerQuoteForLeadId }}' style='display:none'></div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <!-- End Leads here -->     
                                </div>
				<!-- Table Head Starts Here -->
                                {{--*/ $quotes_style   =($type=="quotes")?"style=display:block":"style=display:none" /*--}}                                
                                <div id="ftl-buyer-quotes" class="tabs-group" {{$quotes_style}}>
                                    <div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">
                                            <input type="checkbox" class="ftl_select_all_name" /><span class="lbl padding-8"></span>
                                            Vendor Name<i class="fa  fa-caret-down"></i>
                                        </div>
                                        <div class="col-md-3 padding-left-none">Vehicle Type<i class="fa  fa-caret-down"></i></div>
                                        <div class="col-md-2 padding-left-none">Minimum Lease Period<i class="fa  fa-caret-down"></i></div>
                                        <div class="col-md-1 padding-left-none">Price<i class="fa  fa-caret-down"></i></div>
                                        @if(!empty($comparisonType))
                                            <div class="col-md-1 padding-left-none">
                                                Ranking
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Table Head Ends Here -->
                                    {!! Form::open(array('url' => '#', 'id' => 'addbuyertlpostcounteroffer', 'name' => 'addbuyertlpostcounteroffer')) !!}
                                        {!!	Form::hidden('service_id',1,array('class'=>'','id'=>'service_id'))!!}
                                        @if(isset($arrayBuyerQuoteSellersQuotesPrices) && !empty($arrayBuyerQuoteSellersQuotesPrices))
                                            {!! Form::hidden('buyer_post_counter_offer_id', $arrayBuyerQuoteSellersQuotesPrices[0]->id, array('id' =>'buyer_post_counter_offer_id')) !!}
                                            <div class="table-data">

                                            @foreach($arrayBuyerQuoteSellersQuotesPrices as $key=>$buyerQuoteSellersQuotesDetails)
                                                {{--*/ $buyerQuoteId = $buyerQuoteSellersQuotesDetails->id /*--}}
                                                {{--*/ $sp_item_id = $buyerQuoteSellersQuotesDetails->seller_post_item_id /*--}}
                                                {{--*/ $priceval = 'initial_quote_price' /*--}}
                                                @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->final_quote_price /*--}}
                                                    {{--*/ $priceval = 'final_quote_price' /*--}}
                                                @elseif($buyerQuoteSellersQuotesDetails->firm_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->firm_price /*--}}
                                                    {{--*/ $priceval = 'firm_price' /*--}}
                                                @elseif($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->counter_quote_price /*--}}
                                                    {{--*/ $priceval = 'counter_quote_price' /*--}}
                                                @elseif($buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                                    {{--*/ $price = $buyerQuoteSellersQuotesDetails->initial_quote_price /*--}}
                                                    {{--*/ $priceval = 'initial_quote_price' /*--}}
                                                @endif
                                                
                                                    <!-- Table Row Starts Here -->
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-3 padding-left-none">
                                                            <input type="checkbox" class="ftl_select_name quotecheck" id="ftl_select_name_{!! $buyerQuoteId !!}" value="{!! $buyerQuoteId !!}" /><span class="lbl padding-8"></span>
                                                            {!! $buyerQuoteSellersQuotesDetails->username !!}
                                                            <div class="red rating-margin">
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 padding-left-none">{!! $buyerQuoteSellersQuotesDetails->vehicle_type !!}</div>
                                                        <div class="col-md-2 padding-left-none">{!! $buyerQuoteSellersQuotesDetails->minimum_lease_period !!} 
                                                        @if($buyerQuoteSellersQuotesDetails->lkp_trucklease_lease_term_id == 1 )
                                                        Days
                                                        @elseif($buyerQuoteSellersQuotesDetails->lkp_trucklease_lease_term_id == 2 )
                                                        Weeks
                                                        @elseif($buyerQuoteSellersQuotesDetails->lkp_trucklease_lease_term_id == 3 )
                                                        Months
                                                        @else
                                                        Years
                                                        @endif
                                                        </div>
                                                        <div class="col-md-1 padding-none" data-price="{!! $price !!}" id="buyer_post_price_{!! $buyerQuoteId !!}">{!! $price !!}/-</div>
                                                        @if(!empty($comparisonType))
                                                            <div class="col-md-1 padding-none text-left">
                                                                {!! $buyerQuoteSellersQuotesDetails->rank !!}
                                                            </div>
                                                        @endif
                                                        <input type="hidden" name="priceval" id="priceval" value="{!! $priceval !!}">
                                                        
                                                        <div class="col-md-2 padding-none text-right pull-right">
                                                            @if($commonComponent->CheckCartItem($id)==1)

                                                                @if($buyerQuoteSellersQuotesDetails->lkp_post_status_id != CANCELLED && $buyerQuoteSellersQuotesDetails->lkp_post_status_id != CLOSED && $buyerQuoteSellersQuotesDetails->lkp_post_status_id != BOOKED)
                                                                    
                                                                    @if($buyerQuoteSellersQuotesDetails->seller_acceptence == 1 ||
                                                                        ($buyerQuoteSellersQuotesDetails->counter_quote_price == '0.0000' && $buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000'))
                                                                        <input type="button" class="btn red-btn pull-right buyer_book_now" data-url="{{ $url.$id.'/'.$buyerQuoteId }}"
                                                                               id = "buyer_book_now_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}" value="Book Now" />
                                                                        <div class="clearfix"></div>
                                                                    @endif

                                                                    @if($buyerQuoteSellersQuotesDetails->final_quote_price == '0.0000' && $buyerQuoteSellersQuotesDetails->seller_acceptence == 0)
                                                                        <button class="btn red-btn pull-right buyer_submit_counter_offer" id = "buyer_submit_counter_offer_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}">    
                                                                              @if($buyerQuoteSellersQuotesDetails->firm_price == '0.0000')
                                                                                @if($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.0000')
                                                                                  Counter offer Submitted
                                                                                @else
                                                                                  Counter Offer
                                                                                @endif
                                                                              @else
                                                                                  Firm Price
                                                                              @endif
                                                                        </button>
                                                                    @endif
                                                                    @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000' || $buyerQuoteSellersQuotesDetails->final_quote_price != '0.00' )
                                                                     <button class="btn red-btn pull-right buyer_submit_counter_offer" id = "buyer_submit_counter_offer_{{ $buyerQuoteId }}" data-buyerpostofferid="{{ $buyerQuoteId }}">
                                                                        @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.00')
                                                                            Final Quote Received
                                                                        @elseif($buyerQuoteSellersQuotesDetails->firm_price == '0.0000')
                                                                            Counter offer Submitted
                                                                        @else
                                                                            Firm Offer accepted
                                                                        @endif
                                                                     </button>
                                                                    @endif
                                                                @endif
                                                            @elseif($commonComponent->CheckCart($id,$sp_item_id)==1)
                                                                <button class="btn red-btn pull-right buyer_submit_counter_offer">Booked</button>
                                                            @endif
                                                            <div class="clearfix"></div>
                                                            <div class="pull-right text-right">
                                                                <div class="info-links">
                                                                    <a href="#" class="underline_link new_message" data-transaction_no="{{$buyerQuoteSellersQuotesDetails->transaction_no}}" data-userid="{{$buyerQuoteSellersQuotesDetails->seller_id}}" data-id="{{$id}}" data-buyerquoteitemid="{{ $buyerQuoteSellersQuotesDetails->seller_post_item_id }}"><i class="fa fa-envelope-o"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        @if(($buyerQuoteSellersQuotesDetails->final_quote_price == '0.0000' && $buyerQuoteSellersQuotesDetails->seller_acceptence == 0) || $buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000')

                                                        <div class="col-md-12 padding-none submit-data-div table-slide table-slide-1 counter_offer_details_{{ $buyerQuoteId }} label-line-hight padding-top" style="display: none">
                                                                @if($buyerQuoteSellersQuotesDetails->firm_price == '0.0000')

                                                                    
                                                                            @if($buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                                                                <div class="col-md-3 padding-left-none hide-final">
                                                                                    <div class=" ">                                     
                                                                                        <div class="white-space data-head padding-top-8">Quote : Rs {{ $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->initial_quote_price) }} /-</div>
                                                                                        
                                                                                    </div>
                                                                               </div>                                                                                
                                                                            @else
                                                                            <div class="col-md-3 padding-left-none">
                                                                                <div class="padding-top-8">
                                                                                {!! Form::label('buyer_post_seller_quote_'.$buyerQuoteId,'Quote', array('class' => '')); !!}
                                                                                {!! Form::text('buyer_post_seller_quote_'.$buyerQuoteId,$buyerQuoteSellersQuotesDetails->initial_quote_price,array('class'=>'form-control numberVal', 'readonly')) !!}
                                                                                </div>
                                                                            </div> 
                                                                            @endif
                                                                                                                                           

                                                                    @if(($buyerQuoteSellersQuotesDetails->counter_quote_price != '0.0000' ||
                                                                            $buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000') &&
                                                                            $buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                                                            <div class="col-md-3 padding-left-none hide-final">
                                                                                <div class=" ">                                     
                                                                                    <div class="white-space data-head padding-top-8">Counter Offer : Rs {{ $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->counter_quote_price) }} /-</div>
                                                                                    
                                                                                </div>
                                                                           </div>                                                                                
                                                                    @else
                                                                        <div class="col-md-3 padding-left-none">
                                                                         <div class="white-space">
                                                                        {!! Form::text('buyer_post_counter_offer_'.$buyerQuoteId,'',
                                                                                    array('id'=>'buyer_post_counter_offer_'.$buyerQuoteId,'placeholder'=>'Counter Offer', 'class'=>'form-control form-control1 buyer_post_counter_offer_value clsTLCounterOffer')) !!}
                                                                        
                                                                        
                                                                         </div>
                                                                        </div>
                                                                    @endif
                                                                       
                                                                    <div class="col-md-3 padding-none text-right pull-right">
                                                                        @if($buyerQuoteSellersQuotesDetails->counter_quote_price == '0.0000' &&
                                                                                        $buyerQuoteSellersQuotesDetails->initial_quote_price != '0.0000')
                                                                            {!! Form::button('Submit',array('id'=>'add_buyer_counter_offer_details_'.$buyerQuoteId,
                                                                                            'class'=>'btn add-btn add_buyer_counter_offer_details')) !!}
                                                                        @endif
                                                                    </div>

                                                                    @if($buyerQuoteSellersQuotesDetails->final_quote_price != '0.0000')
                                                                        <div class="col-md-3 padding-left-none hide-final">
                                                                            <div class=" ">                                     
                                                                                <div class="white-space data-head padding-top-8">Final Quote : Rs {{ $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->final_quote_price) }} /-</div>
                                                                                
                                                                            </div>
                                                                       </div>                                                                        
                                                                    @endif


                                                                    
                                                                @else
                                                                    <div class="col-md-3 padding-left-none hide-final">
                                                                        <div class=" ">                                     
                                                                            <div class="white-space data-head padding-top-8">Firm Offer : Rs {{ $commonComponent->moneyFormat($buyerQuoteSellersQuotesDetails->firm_price) }} /-</div>
                                                                            
                                                                        </div>
                                                                   </div>                                                                   
                                                                @endif
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            @endif
                                                            {!! Form::hidden('buyer_post_buyer_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->buyer_id, array('id' => 'buyer_post_buyer_id_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_post_seller_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->seller_id, array('id' => 'buyer_post_seller_id_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_quote_item_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->buyer_quote_item_id, array('id' => 'buyer_quote_item_id_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('seller_post_item_id_'.$buyerQuoteId, $buyerQuoteSellersQuotesDetails->seller_post_item_id, array('id' => 'seller_post_item_id_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId,
                                                                        $exactDispatchDate, array('id' =>'buyer_counter_offer_seller_post_from_date_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId,
                                                                        $exactDeliveryDate, array('id' =>'buyer_counter_offer_seller_post_to_date_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_counter_offer_seller_from_date_'.$buyerQuoteId,
                                                                        $buyerQuoteSellersQuotesDetails->from_date, array('id' =>'buyer_counter_offer_seller_from_date_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('buyer_counter_offer_seller_to_date_'.$buyerQuoteId,
                                                                        $buyerQuoteSellersQuotesDetails->to_date, array('id' =>'buyer_counter_offer_seller_to_date_'.$buyerQuoteId)) !!}

                                                            {!! Form::hidden('fdispatch-date_'.$buyerQuoteId,
                                                                        $Dispatch_Date, array('id' =>'fdispatch-date_'.$buyerQuoteId)) !!}
                                                            {!! Form::hidden('cancel_buyer_counter_offer_enquiry',
                                                $id, array('id' =>'cancel_buyer_counter_offer_enquiry','data-id' =>$id)) !!}               
                                                    </div>
                                                    <!-- Table Row Ends Here -->
                                               
                                            @endforeach
                                             </div>
                                        @endif
                                    {!! Form::close() !!}
                                </div>
                            </div>
							<!-- Table Starts Here -->
						</div>
						<!-- Right Section Ends Here -->
					</div>
				</div>
				<div class="clearfix"></div>
			</div>

	@include('partials.footer')
@endsection