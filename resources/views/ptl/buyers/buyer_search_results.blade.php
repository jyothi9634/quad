@extends('app')
@section('content') 
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('ptlBuyerSpotComponent', 'App\Components\Ptl\PtlBuyerComponent')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}
    @if($serviceId == AIR_INTERNATIONAL)
    {{--*/ $str_from='From Airports' /*--}}
    {{--*/ $str_to='To Airports' /*--}}
    @elseif($serviceId == OCEAN)
    {{--*/ $str_from='From Seaports' /*--}}
    {{--*/ $str_to='To Seaports' /*--}}
    @else
    {{--*/ $str_from='From Pincodes' /*--}}
    {{--*/ $str_to='To Pincodes' /*--}}
    @endif
    {{--*/ $load_types=$commonComponent->getAllLoadTypes() /*--}}
    {{--*/ $CourierTypes=$commonComponent->getAllCourierPorposeTypes() /*--}}
    {{--*/ $packageTypes=$commonComponent->getAllPackageTypes() /*--}}
    {{--*/ $courier_types=$commonComponent->getAllCourierTypes() /*--}}
    {{--*/ $courier_delivery_types=$commonComponent->getAllCourierDeliveryTypes() /*--}}
    {{--*/ $volumeWeightTypes=$commonComponent->getVolumeWeightTypes() /*--}}
    {{--*/ $unitsWeightTypes=$commonComponent->getUnitsWeight() /*--}}
    {{--*/ $shipmentTypes=$commonComponent->getShipmentTypes() /*--}}
    {{--*/ $senderIdentity=$commonComponent->getSenderIdentity() /*--}}
    {{--*/ $request_blade=array() /*--}}
    {{--*/ $request_blade=Session::get('request') /*--}} 
     {{--*/ $request_blade=Session::get('request') /*--}} 
     <?php //echo "<pre>";print_r($request_blade);exit; ?>
	<!--swathis code for flexible dates-->
        {{--*/ $validFrom='' /*--}}{{--*/ $validTo='' /*--}}
{{--*/ $fdispatch='' /*--}}{{--*/ $fdelivery='' /*--}}
    @if($request_blade['ptlDispatchDate'][0]!="") 
        <?php  $validFrom = str_replace('/','-',$request_blade['ptlDispatchDate'][0]) ?>
        {{--*/ $validFrom = date('Y-m-d', strtotime($validFrom)) /*--}}
    @endif
    @if($request_blade['ptlDeliveryhDate'][0]!="") 
        <?php $validTo = str_replace("/","-",$request_blade['ptlDeliveryhDate'][0]) ?>
        {{--*/ $validTo = date('Y-m-d', strtotime($validTo)) /*--}}
    @endif
    @if($request_blade['ptlFlexiableDispatch'][0]== 1) 
        {{--*/ $fdispatch = $buyerCommonComponent->getPreviousNextThreeDays($validFrom) /*--}}
        {{--*/ $Dispatch_Date = ($validFrom) ?date("Y-m-d", strtotime($validFrom . ' -3 day')):'' /*--}}
    @else 
        {{--*/ $fdispatch = $commonComponent->checkAndGetDate($validFrom) /*--}}
        {{--*/ $Dispatch_Date =date("Y-m-d", strtotime($validFrom)) /*--}}
    @endif
    @if($request_blade['ptlFlexiableDelivery'][0]== 1 && $request_blade['ptlDeliveryhDate'][0]!='') 
        {{--*/ $fdelivery = $buyerCommonComponent->getPreviousNextThreeDays($validTo) /*--}}
    @else 
        {{--*/ $fdelivery = $commonComponent->checkAndGetDate($validTo) /*--}}
    @endif
    @if($request_blade['ptlLoadType'][0]!='') 
    {{--*/ $packageTypes=$commonComponent->getLoadBasedAllPackages($request_blade['ptlLoadType'][0]) /*--}}
    @endif
    
<!--swathis code for flexible dates-->
		<div class="main">

			<div class="container">
				<h1 class="page-title">Search Results 
				@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
				@elseif(Session::get('service_id') == RAIL)
				(RAIL)
				@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
				@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
				@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
				@elseif(Session::get('service_id') == COURIER)
				(COURIER)
				@else
				({!! $commonComponent->getServiceName(Session::get('service_id')) !!})
				@endif
				</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->
				{!! Form::open(['url' =>'#','class'=>'filter_form','id' => 'ptl_buyer_results_form','method'=>'get']) !!}	
				<input type="hidden" name="filter_set" id="filter_set" value="1">
				<div class="search-block inner-block-bg">
				
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
							<?php //echo "<pre>"; print_r($request_blade); echo $request_blade['post_delivery_types'][0];die;?>
							@if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL || Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == COURIER)
							@if(count($request_blade['ptlFromLocation'])>1)
							Multi | Multi
							@else
							    @if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL || Session::get('service_id') == AIR_DOMESTIC )
									
									{{ $commonComponent->getPinName($request_blade['ptlFromLocation'][0])}} | {{ $commonComponent->getPinName($request_blade['ptlToLocation'][0])}}
									
								@endif
								@if(Session::get('service_id') == COURIER)
									@if($request_blade['post_delivery_types'][0] == 1)
									{{ $commonComponent->getPinName($request_blade['ptlFromLocation'][0])}} | {{ $commonComponent->getPinName($request_blade['ptlToLocation'][0])}}
									@elseif($request_blade['post_delivery_types'][0] == 2)
									{{ $commonComponent->getPinName($request_blade['ptlFromLocation'][0])}} | {{ $commonComponent->getCountry($request_blade['ptlToLocation'][0])}}
									@endif
								@endif
							@endif  
							@endif  
							@if(Session::get('service_id')==AIR_INTERNATIONAL) 
							@if(count($request_blade['ptlFromLocation'])>1)
							Multi | Multi
							@else
							{{ $commonComponent->getAirportName($request_blade['ptlFromLocation'][0])}} | {{ $commonComponent->getAirportName($request_blade['ptlToLocation'][0])}}
							@endif
							@endif
							@if(Session::get('service_id')==OCEAN) 
							@if(count($request_blade['ptlFromLocation'])>1)
							Multi | Multi
							@else
							{{ $commonComponent->getSeaportName($request_blade['ptlFromLocation'][0])}} | {{ $commonComponent->getSeaportName($request_blade['ptlToLocation'][0])}}
							@endif	
							@endif						
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>								
								{{ $fdispatch }}<?php   //echo "<pre>";print_r(Session::get('ptlBuyerSearchform'));exit;?>
                                                                {!! Form::hidden('ptlFlexiableDispatch[]', Session::get('ptlBuyerSearchform.ptlFlexiableDispatch.0')) !!}
                                                                {!! Form::hidden('ptlDeliveryhDate[]', Session::get('ptlBuyerSearchform.ptlFlexiableDelivery.0')) !!}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if($request_blade['ptlDeliveryhDate'][0]!= '') {{ $fdelivery }} @else NA @endif					
							</span>
						</div>
					</div>
					@if(Session::get('service_id') != COURIER) 
					<div class="col-md-6">
						<p class="search-head">Load Type</p>
								@if(count($request_blade['ptlLoadType'])>1)
								Multi
								@else
								{{ $commonComponent->getLoadType($request_blade['ptlLoadType'][0])}}
								@endif								
					</div>
					<div class="col-md-6">
						<p class="search-head">Package Type</p>
								@if(count($request_blade['ptlPackageType'])>1)
								Multi
								@else
								{{ $commonComponent->getPackageType($request_blade['ptlPackageType'][0])}}
								@endif
					</div>
					@endif	
					@if(Session::get('service_id') == COURIER) 
					<div class="col-md-6">
						<p class="search-head">Destination Type</p>
								@if($request_blade['post_delivery_types'][0] == 1)
								Domestic
								@else
								International
								@endif								
					</div>
					<div class="col-md-6">
						<p class="search-head">Courier Type</p>
								@if($request_blade['courier_types'][0] == 1)
								Documents
								@else
								Parcel
								@endif
					</div>
					@endif					
					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>

				</div> 

				<!-- Left Filters Starts --> 
				<h2 class="side-head pull-left">Filter Results</h2>
				
				<!-- Content top navigation Starts Here-->
				@include('partials.content_top_navigation_links')
				<!-- Content top navigation ends Here-->

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->

						<div class="main-left">
                            @if ((Session::has('show_layered_filter') && Session::get('show_layered_filter')!="") || (!isset($_REQUEST['is_search'])))
								@include("partials.filter._price")
                            @endif
							<?php $selectedPayment = (isset($_REQUEST['selected_payments']) && !empty($_REQUEST['selected_payments'])) ? $_REQUEST['selected_payments'] : array(); ?>								

							@if (Session::has('show_layered_filter') && Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")	
							<h2 class="filter-head">Payment Mode</h2>
							<div class="payment-mode inner-block-bg">
								@if(Session::has('layered_filter_payments') && is_array(Session::get('layered_filter_payments')))
									@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
									<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
									<div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onChange="this.form.submit()"/><span class="lbl padding-8">
																			@if ($paymentName == 'Advance')
																			{{--*/ $paymentType = 'Online Payment' /*--}}
																			@else
																			{{--*/ $paymentType = $paymentName /*--}}
																			@endif
																			{{$paymentType}}
																		</span></div>
									@endforeach
								@endif
								<!--div class="check-box"><input type="checkbox" /><span class="lbl padding-8">Online</span></div>
								<div class="check-box"><input type="checkbox" /><span class="lbl padding-8">Cash on Delivery</span></div>
								<div class="check-box"><input type="checkbox" /><span class="lbl padding-8">Cash on Pickup</span></div-->
							</div>
							@endif
							@endif
                                                        @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
							@if (Session::has('layered_filter_from_location')&& Session::get('layered_filter_from_location')!="")	
							<h2 class="filter-head">From Location</h2>
							<div class="payment-mode inner-block-bg">
								<?php		$anamolies = (isset($_REQUEST['from_location_id']) && !empty($_REQUEST['from_location_id'])) ? $_REQUEST['from_location_id'] : array(); 	
																									
											foreach ( Session::get('layered_filter_from_location') as $fromId => $locationName) {
												$selected = in_array($fromId, $anamolies) ? 'checked="checked"' : "";
                                                                                            
											?>
												<div class="check-box"><input type="checkbox" name="from_location_id[]" value="<?php  echo $fromId; ?>"  {{$selected}} onChange="this.form.submit()"/><span class="lbl padding-8"><?php echo $locationName; ?></span></div>
											<?php
												//$anamolies[] = $Query_buyers_for_seller->from_location_id;
												
											}
										
									?>
							</div>		
							@endif
                                                        @endif
                                                        @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
                                                        @if (Session::has('layered_filter_to_location')&& Session::get('layered_filter_to_location')!="")
							<h2 class="filter-head">To Location</h2>					
							<div class="payment-mode inner-block-bg">
								<?php							
										//$anamolies= array();
                                                                                $anamolies = (isset($_REQUEST['to_location_id']) && !empty($_REQUEST['to_location_id'])) ? $_REQUEST['to_location_id'] : array(); 	
																									
											foreach ( Session::get('layered_filter_to_location') as $fromId => $locationName) {
												$selected = in_array($fromId, $anamolies) ? 'checked="checked"' : "";
											?>
												<div class="check-box"><input type="checkbox" name="to_location_id[]" value="<?php  echo $fromId; ?>"  {{$selected}} onChange="this.form.submit()"/><span class="lbl padding-8"><?php echo $locationName; ?></span></div>
											<?php
												//$anamolies[] = $Query_buyers_for_seller->to_location_id;
												
											}
										
									?>
							</div>
                                                        @endif
                                                        @endif
							<h2 class="filter-head">Tracking</h2>
							<div class="tracking inner-block-bg">
								<div class="check-box"><input type="checkbox" name="ptl_tracking_milestone" value="1" onChange="this.form.submit()" <?php if(isset($_REQUEST['ptl_tracking_milestone']) && $_REQUEST['ptl_tracking_milestone']) { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">{{TRACKING_MILE_STONE}}</span></div>
								<div class="check-box"><input type="checkbox" name="ptl_tracking_realtime" value="2" onChange="this.form.submit()" <?php if(isset($_REQUEST['ptl_tracking_realtime']) && $_REQUEST['ptl_tracking_realtime']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">{{TRACKING_REAL_TIME}}</span></div>
							</div>
							<div class="tracking inner-block-bg">
								<div class="check-box"><input type="checkbox" name="ptl_top_sellers_orders" value="1" <?php //if(isset($_REQUEST['ptl_top_sellers_orders']) && $_REQUEST['ptl_top_sellers_orders']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Orders) </span></div>
								<div class="check-box"><input type="checkbox" name="ptl_top_sellers_rated" value="2"  <?php //if(isset($_REQUEST['ptl_top_sellers_rated']) && $_REQUEST['ptl_top_sellers_rated']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Rated) </span></div>
							</div>

							<?php	$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
								?>
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
									@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
										<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
										<div class="seller-list inner-block-bg">
											@if(Session::has('layered_filter') && is_array(Session::get('layered_filter')))
												@foreach (Session::get('layered_filter') as $userId => $userName)
													<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
													<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
													<div class="col-xs-12 padding-none"> </div>
												@endforeach
											@endif
										</div>
									@endif
								@endif

							<?php  
							//echo $_REQUEST['ptlFlexiableDispatch'][0];
							//echo $_REQUEST['date_flexiable'];
							//echo "swapna";
							//echo $_REQUEST['ptlDispatchDate'];
							//echo $_REQUEST['ptlDispatchDate'][0];
							//echo $_REQUEST['date_flexiable'][0];
							//echo $flexdate = (isset($_REQUEST['ptlDispatchDate'][0]) && !empty($_REQUEST['ptlDispatchDate'][0])) ? $_REQUEST['ptlDispatchDate'][0] : (isset($_REQUEST['date_flexiable'][0]) ? $_REQUEST['date_flexiable'][0] : "");
 //echo "<pre>"; print_R($request_blade); echo "</pre>"; //die;	
							if((isset($_REQUEST['ptlFlexiableDispatch'][0]) && $_REQUEST['ptlFlexiableDispatch'][0]==1) || (isset($_REQUEST['date_flexiable'][0]) && ($_REQUEST['date_flexiable'][0]!=""))) { ?>
								
							<h2 class="filter-head">Preferred Dispatch Date</h2>
							<div class="seller-list inner-block-bg">
								 <?php //echo "<pre>"; print_R($_REQUEST); echo "</pre>"; die;								 
								if(isset($request_blade['ptlDispatchDate'][0])){
								$flexdate = $request_blade['ptlDispatchDate'][0]; ?>
								<input type="hidden" name="ptlDispatchDate" id="ptlDispatchDate_change" value=<?php echo $flexdate; ?>>
								<?php  }else{
								$flexdate = $_REQUEST['date_flexiable']; ?>
								<input type="hidden" name="ptlDispatchDate" id="ptlDispatchDate_change" value=<?php echo $flexdate; ?>>
								<?php 
								}//(isset($_REQUEST['ptlDispatchDate'][0]) && !empty($_REQUEST['ptlDispatchDate'][0])) ? $_REQUEST['ptlDispatchDate'][0] : (isset($_REQUEST['date_flexiable']) ? $_REQUEST['date_flexiable'] : "");
								//echo str_replace('/', '-',$flexdate);exit;
								  //echo "<select name='date_flexiable' onChange='this.form.submit()' class='selectpicker' >";
									for($i=-3;$i<=3;$i++){
										$selected = "";
										if($i<0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));//new DateTime($flexdate);
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else if($i>0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else{
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
										}										
										if(isset($_REQUEST['date_flexiable'])){
											if(($_REQUEST['date_flexiable'] == $date1->format('Y-m-d'))){
												$selected = "checked='checked'";
											}	
										}else {
											if(isset($_REQUEST['ptlDispatchDate'][0]) == $date1->format('Y-m-d')){
												$selected = "checked='checked'";
											}
										}
										if($date1->format('Y-m-d') >= date('Y-m-d')){
									   		echo "<div class='check-box'><input id ='date_flexiable_$i' type='radio' name='date_flexiable' ".$selected." value='".$date1->format('Y-m-d')."' onChange='this.form.submit()'/><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
									//echo "</select>";
									//echo "<pre>"; print_R($_REQUEST); echo "</pre>"; die;
								 ?>				
								
							</div><?php } ?>
						</div>
						{!! Form::close() !!}
						<div class="main-right">

							<!-- Table Starts Here -->

							<div class="table-div"> 							
			                    <div class="table-data table-div" id="ptl_buyer_serch_results">
								{!! $gridBuyer !!}
								</div>
							</div>
							<!-- Table Ends here -->
						</div>		
					</div>
				</div>					
			</div> <!-- container div -->
		</div>	<!-- main div -->
<!-- Page footer navigation Starts Here-->

<!-- Left Filters ends -->
@include('partials.footer')
<!-- Search Block Ends Here -->	
{{--*/ $request_buyer_data_modify = Session::get('request') /*--}}
<!-- Model Window starts --> 
	<div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        {{--*/ $serviceId = Session::get('service_id') /*--}}
	        
	        <div class="modal-body">
	        {!! Form::open(['url' =>'#','id' => 'ptlBuyerQuotelineitemsForm', 'autocomplete'=>'off','method'=>'get']) !!}
            {!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!} 
            <input type="hidden" name="ptlBuyerSearchCompareId" value="2" id="ptlBuyerSearchCompareId">
           
            {!! Form::hidden('is_commercial', $request_buyer_data_modify['is_commercial'] , array('id' => 'is_commercial', 'class' =>'is_commercial_check_ptl')) !!}
	          <div class="col-md-12 padding-none single-layout inner-form margin-bottom-none">
	          @if(Session::get('service_id') == COURIER)
	          
	          
	          @if(isset($request_buyer_data_modify['post_delivery_types'][0]) && $request_buyer_data_modify['post_delivery_types'][0] == 1)
					{{--*/ $domestic_dom = true /*--}}
					@else
					{{--*/ $domestic_dom = false /*--}}
					@endif
					
					
					@if(isset($request_buyer_data_modify['post_delivery_types'][0]) && $request_buyer_data_modify['post_delivery_types'][0] == 2)
					{{--*/ $domestic_int = true /*--}}
					@else
					{{--*/ $domestic_int = false /*--}}
					@endif
					
					@if(isset($request_buyer_data_modify['courier_types'][0]) && $request_buyer_data_modify['courier_types'][0] == 1)
					{{--*/ $doc_courier = true /*--}}
					@else
					{{--*/ $doc_courier = false /*--}}
					@endif
					
					
					@if(isset($request_buyer_data_modify['courier_types'][0]) && $request_buyer_data_modify['courier_types'][0] == 2)
					{{--*/ $parcel_courier = true /*--}}
					@else
					{{--*/ $parcel_courier = false /*--}}
					@endif
	          
	          
	          
	          
                                <div class="col-md-3 form-control-fld">
                                                <div class="radio-block">
                                                        <div class="radio_inline">
                                                        {!! Form::radio('post_delivery_type', 'Domestic', $domestic_dom, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
                                                        </div>
                                                        <div class="radio_inline">
                                                        {!! Form::radio('post_delivery_type', 'International', $domestic_int, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
                                                        {!! Form::hidden('post_or_delivery_type', $request_buyer_data_modify['post_delivery_types'][0], array('id' => 'post_delivery_type')) !!}
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="clearfix"></div>
				@endif
                                
                                
                                @if($request_buyer_data_modify['is_commercial'] == 1)
                                {{--*/ $is_commercial = "checked" /*--}}
                                @else
                                {{--*/ $is_commercial = "" /*--}}
                                @endif
                                
                                @if($request_buyer_data_modify['is_commercial'] == 0)
                                {{--*/ $is_noncommercial = "checked" /*--}}
                                @else
                                {{--*/ $is_noncommercial = "" /*--}}
                        		@endif
                                    
                                <div class="col-md-12 form-control-fld margin-none">
                                    <div class="radio-block">
                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="commercial" class="is_commercial_modify" value="1" {{ $is_commercial }} /> <label for="commercial"><span></span>Commercial</label></div>
                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" class="is_commercial_modify" value="0" {{$is_noncommercial}} /> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                    </div>
                                </div>
                                
                                
					@if($serviceId == AIR_INTERNATIONAL)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_airport', $request_buyer_data_modify['fromlocationName'][0], ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Airport*']) !!}
	                                {!! Form::hidden('from_airport_id', $request_buyer_data_modify['ptlFromLocation'][0], array('id' => 'from_airport_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_airport',$request_buyer_data_modify['tolocationName'][0], ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Airport*']) !!}
	                                {!! Form::hidden('to_airport_id', $request_buyer_data_modify['ptlToLocation'][0], array('id' => 'to_airport_id')) !!}
								</div>
							</div>

						@elseif($serviceId == OCEAN)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_airport', $request_buyer_data_modify['fromlocationName'][0], ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Ocean*']) !!}
	                                {!! Form::hidden('from_airport_id', $request_buyer_data_modify['ptlFromLocation'][0], array('id' => 'from_airport_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_airport',$request_buyer_data_modify['tolocationName'][0], ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Ocean*']) !!}
	                                {!! Form::hidden('to_airport_id', $request_buyer_data_modify['ptlToLocation'][0], array('id' => 'to_airport_id')) !!}
								</div>
							</div>

						@else
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlFromLocation', $request_buyer_data_modify['fromlocationName'][0] , ['id' => 'ptlFromLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Pincode*']) !!}
	                           		{!! Form::hidden('ptlFromLocationId', $request_buyer_data_modify['ptlFromLocation'][0] , array('id' => 'ptlFromLocationId')) !!}
								</div>
							</div>
							@if($serviceId == COURIER)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlToLocation', $request_buyer_data_modify['tolocationName'][0] , ['id' => 'ptlToLocation', 'class'=>'form-control', 'placeholder' => 'To Pincode*']) !!}
	                           		{!! Form::hidden('ptlToLocationId', $request_buyer_data_modify['ptlToLocation'][0] , array('id' => 'ptlToLocationId')) !!}
								</div>
							</div>
							@else
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlToLocation', $request_buyer_data_modify['tolocationName'][0] , ['id' => 'ptlToLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Pincode*']) !!}
	                           		{!! Form::hidden('ptlToLocationId', $request_buyer_data_modify['ptlToLocation'][0] , array('id' => 'ptlToLocationId')) !!}
								</div>
							</div>
							@endif
                        @endif
                        	
	                        

                    		<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('ptlDispatchDate',$request_buyer_data_modify['ptlDispatchDate'][0], ['id' => 'ptlDispatchDate','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *', 'readonly'=>"readonly"]) !!}
									Flexible Dispatch Dates
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('ptlDeliveryhDate',$request_buyer_data_modify['ptlDeliveryhDate'][0], ['id' => 'ptlDeliveryhDate','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date (Optional)', 'readonly'=>"readonly"]) !!}
									Flexible Delivery Dates
								</div>
							</div>
							
							<div class="clearfix"></div>
							
						  	@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN && $serviceId != COURIER)
						  	
						  	@if(isset($request_buyer_data_modify['ptlDoorpickup'][0]) && $request_buyer_data_modify['ptlDoorpickup'][0] == '1') 
										{{--*/ $ptlDoorpickup = true /*--}}
								@else
										{{--*/ $ptlDoorpickup = false /*--}}
							@endif
							
							@if(isset($request_buyer_data_modify['ptlDoorDelivery'][0]) && $request_buyer_data_modify['ptlDoorDelivery'][0] == '1') 
										{{--*/ $ptlDoorDelivery = true /*--}}
								@else
										{{--*/ $ptlDoorDelivery = false /*--}}
							@endif
						  	
						  	
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										
										
									</div>
									{!! Form::checkbox('ptlDoorpickup', 1, $ptlDoorpickup, ['class' => '' , 'id'=>'ptlDoorpickup']) !!}
										<span class="lbl padding-8">Door Pickup</span>
							    </div>
							    <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										
										
									</div>
									{!! Form::checkbox('ptlDoorDelivery', 1, $ptlDoorDelivery, ['class' => '', 'id'=>'ptlDoorDelivery']) !!}
										<span class="lbl padding-8">Door Delivery</span>
							    </div>
						    @endif	

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
	                                
	                            </div>
	                            <input type="hidden" name="ptlFlexiableDispatch" id="ptlFlexiableDispatch_hidden" value="<?php echo isset($_REQUEST['ptlFlexiableDispatch'][0]) ? $_REQUEST['ptlFlexiableDispatch'][0] : ""; ?>">	                                
	                            
	                        </div>

	                       <div class="col-md-3 form-control-fld">
								<div class="input-prepend">
	                               
	                            </div>
	                            <input type="hidden" name="ptlFlexiableDelivery" id="ptlFlexiableDelivery_hidden" value="<?php echo isset($_REQUEST['ptlFlexiableDelivery'][0]) ? $_REQUEST['ptlFlexiableDelivery'][0] : ""; ?>">
	                           
	                            
	                        </div>


						    @if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
						    	<div class="clearfix"></div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
								            {!!	Form::select('ptlShipmentType',(['' => 'Shipment Type *'] +$shipmentTypes), $request_buyer_data_modify['ptlShipmentType'][0] ,['class' =>'selectpicker','id'=>'ptlShipmentType']) !!}
								    </div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
								            {!!	Form::select('ptlSenderIdentity',(['' => 'Sender Identity *'] +$senderIdentity), $request_buyer_data_modify['ptlSenderIdentity'][0] ,['class' =>'selectpicker','id'=>'ptlSenderIdentity']) !!}
								        
								    </div>
								</div>
                            @endif
							
							@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
								<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
								    <div class="input-prepend">
								    		<span class="add-on"><i class="fa fa-balance-scale"></i></span>
								            {!! Form::text('ptlIECode', $request_buyer_data_modify['ptlIECode'][0],  ['class' => 'form-control numericvalidation' ,'maxlength'=>'10', 'id'=>'ptlIECode','placeholder' => 'IE Code']) !!}
								    </div>
								</div>
								<div class="col-md-3 col-sm-4 col-xs-12 padding-right-none mobile-padding-none">
								    <div class="input-prepend">
								    	<span class="add-on"><i class="fa fa-balance-scale"></i></span> 
								        {!! Form::text('ptlProductMade', $request_buyer_data_modify['ptlProductMade'][0],  ['class' => 'form-control' , 'id'=>'ptlProductMade','placeholder' => 'Product Made']) !!}
								    </div>
								</div>
							@endif
					

				<div class="col-md-12 form-control-fld form-control-fld1">
				<div class="col-md-12 inner-block-bg inner-block-bg1 margin-top margin-bottom">
				@if($serviceId==COURIER)
						<div class="col-md-12 padding-none">
								<div class="col-md-3 padding-none">
									<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', $doc_courier, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', $parcel_courier, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types', $request_buyer_data_modify['courier_types'][0], array('id' => 'courier_types')) !!}
											</div>
										</div>
								</div>
						</div>
						@endif
					<h2 class="sub-head margin-bottom margin-top">Add Item Details</h2>

					@if($serviceId == COURIER)
					
					@if(isset($request_buyer_data_modify['courier_types'][0]) && $request_buyer_data_modify['courier_types'][0] == 1)
							{{--*/ $doc_select =  'style = display:none'/*--}}
							@else
							{{--*/ $doc_select =  'style = display:block'/*--}}
							@endif
					
					
						<div id ='documents_display_courier' {{ $doc_select }} class="col-md-3 padding-none margin-none">
							<div class="normal-select">
									{!!	Form::select('ptlpurposesType',(['' => 'Courier Purposes*'] +$CourierTypes), $request_buyer_data_modify['ptlPurposesType'][0] ,['class' =>'selectpicker','id'=>'ptlPurposesType']) !!}
							</div>
						</div>
						<div class="clearfix"></div>
					@endif
							
				@if($serviceId != COURIER)
					<div class="col-md-6 padding-none">
						<h5 class="caption-head"></h5>
						<div class="col-md-6 form-control-fld">
							<div class="normal-select">
								{!!	Form::select('ptlLoadType',(['11'=>'Load Type (Any)'] +$load_types ), $request_buyer_data_modify['ptlLoadType'][0] ,['class' =>'selectpicker','id'=>'ptlLoadType']) !!}
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="normal-select">
								{!!	Form::select('ptlPackageType',(['' => 'Packaging Type *'] +$packageTypes), $request_buyer_data_modify['ptlPackageType'][0] ,['class' =>'selectpicker','id'=>'ptlPackageType']) !!}
							</div>
						</div>
					</div>
					
				@endif		
					
					@if($serviceId != COURIER)
					<div  id ='documents_display' class="col-md-6 padding-none">
					@else
					<div  id ='documents_display' {{ $doc_select }} class="col-md-6 padding-none">
					@endif
						<h5 class="caption-head margin-left-none">
									Package Weight (Volumetric Weight)
									<span class="pull-right">
									@if(isset($request_buyer_data_modify['ptlDisplayVolumeWeight'][0])&& $request_buyer_data_modify['ptlDisplayVolumeWeight'][0] != '')
										<span id="displayVolumeW">{{ $request_buyer_data_modify['ptlDisplayVolumeWeight'][0] }} CFT</span>
										@else
										<span id="displayVolumenone">Total Volume</span><span id="displayVolumeW"></span>
									@endif
										{!!	Form::hidden('ptlDisplayVolumeWeight',$request_buyer_data_modify['ptlDisplayVolumeWeight'][0],array('class'=>'form-control','placeholder'=>'Display Vol. Weight *','id'=>'ptlDisplayVolumeWeight')) !!}
									</span>
								</h5>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlLengthCourier',$request_buyer_data_modify['ptlLengthCourier'][0],array('class'=>'form-control form-control1 numberVal twodigitsthreedecimals_deciVal','placeholder'=>'L *','id'=>'ptlLengthCourier')) !!}
									@else		
										{!!	Form::text('ptlLength',$request_buyer_data_modify['ptlLength'][0],array('class'=>'form-control form-control1 numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'L *','id'=>'ptlLength')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlWidthCourier',$request_buyer_data_modify['ptlWidthCourier'][0],array('class'=>'form-control form-control1 border-left-none numberVal twodigitsthreedecimals_deciVal','placeholder'=>'B *','id'=>'ptlWidthCourier')) !!}
									@else
										{!!	Form::text('ptlWidth',$request_buyer_data_modify['ptlWidth'][0],array('class'=>'form-control form-control1 border-left-none numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'B *','id'=>'ptlWidth')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlHeightCourier',$request_buyer_data_modify['ptlHeightCourier'][0],array('class'=>'form-control form-control1 border-left-none numberVal twodigitsthreedecimals_deciVal','placeholder'=>'H *','id'=>'ptlHeightCourier')) !!}
									@else
										{!!	Form::text('ptlHeight',$request_buyer_data_modify['ptlHeight'][0],array('class'=>'form-control form-control1 border-left-none numberVal fourdigitsthreedecimals_deciVal','placeholder'=>'H *','id'=>'ptlHeight')) !!}
									@endif	
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days unit-days-align">
										@if($serviceId == COURIER)
											{!!	Form::select('ptlCheckVolWeightCourier',($volumeWeightTypes),$request_buyer_data_modify['ptlCheckVolWeightCourier'][0],['class' =>'selectpicker','id'=>'ptlCheckVolWeightCourier', 'onChange'=>'volumeWeight(this.value,21)']) !!}
										@else
											{!!	Form::select('ptlCheckVolWeight',($volumeWeightTypes), $request_buyer_data_modify['ptlCheckVolWeight'][0] ,['class' =>'selectpicker','id'=>'ptlCheckVolWeight', 'onChange'=>'volumeWeight(this.value)']) !!}
										@endif	
										</span>
									</div>
								</div>
					</div>
					@if($serviceId != COURIER)
					<div class="clearfix"></div>
					@endif
					
					@if($serviceId == COURIER)
					<h5 class="caption-head margin-left-none" id="parcel_hide"></h5>
					@endif
					<div class="col-md-3 form-control-fld padding-right-none">
					
						<div class="col-md-7 padding-none">
							<div class="input-prepend">	
							@if($serviceId == COURIER)	
								{!!	Form::text('ptlUnitsWeight',$request_buyer_data_modify['ptlUnitsWeight'][0],array('class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}	
							@else
								{!!	Form::text('ptlUnitsWeight',$request_buyer_data_modify['ptlUnitsWeight'][0],array('class'=>'form-control form-control1 numberVal fivedigitsthreedecimals_deciVal','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}	
							@endif
							</div>
						</div>
						<div class="col-md-5 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days">
									{!!	Form::select('ptlCheckUnitWeight',(['' => 'Unit Weight'] +$unitsWeightTypes), $request_buyer_data_modify['ptlCheckUnitWeight'][0] ,['class' =>'selectpicker','id'=>'ptlCheckUnitWeight']) !!}	
								</span>
							</div>
						</div>
					</div>
					
					<div class="col-md-3 form-control-fld">
						@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==RAIL )
						<div class="input-prepend">
							{!!	Form::text('ptlNopackages',$request_buyer_data_modify['ptlNopackages'][0],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
						</div>
					 @else
						<div class="input-prepend">
							{!!	Form::text('ptlNopackages',$request_buyer_data_modify['ptlNopackages'][0],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
						</div>
					 @endif

					</div>
					

					@if($serviceId==COURIER)
					<div class="clearfix margin-bottom"></div>
					<div class="col-md-3 form-control-fld padding-left-none">
							<div class="input-prepend">
								{!!	Form::text('packeagevalue',$request_buyer_data_modify['packeagevalue'][0],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'Package Value *','id'=>'packeagevalue', 'maxlength' => 5)) !!}
							</div>
					</div>
					@endif
					<div class="col-md-5 form-control-fld padding-left-none">
						
						{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
				    	{!! Form::submit('ADD +', ['class' => 'btn add-btn','name' => 'add','id' => 'ptlAddMoreItems','onclick'=>"updatepoststatus(0)"]) !!}	
				    	<div id="error-ptl-add-item" class="error "></div>
					</div>

				</div>
				</div>
				{!! Form::close() !!}

				
        </div>
				
          {!! Form::open(['url' =>'ptl/byersearchresults','id' => 'ptlBuyerSearchSendValues','method'=>'get']) !!}
				<div class="col-md-12 form-control-fld">
				<div class="col-md-12 inner-block-bg inner-block-bg1">
                                {!! Form::hidden('is_commercial', $request_buyer_data_modify['is_commercial'] , array('id' => 'is_commercial', 'class' =>'is_commercial_check_ptl')) !!}
 
                                    
 
 
					<div class="col-md-12 padding-none" id="ptl_addmore_locations">
						<div class="main-inner ptl_add_locations"> 
							

							<!-- Right Section Starts Here -->

							<div class="main-right">
								<h2 class="sub-head"><span class="from-head">From Location: <span class="fromPin"></span></span> - <span class="to-head">To Location: <span class="toPin"></span></span></h2>

								<!-- Table Starts Here -->

								<div class="table-div table-style1" >
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
									@if($serviceId!=21)
										<div class="col-md-2 padding-left-none">Load type</div>
										<div class="col-md-2 padding-left-none">Package Type</div>
										@endif
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Courier Purpose</div>
										@endif
										<div class="col-md-2 padding-left-none">Volume</div>
										<div class="col-md-2 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Package Value (Rs)</div>
										@endif
										<div class="col-md-2 padding-left-none"></div>
									</div>

									<input type="hidden" id='ptlBuyerAddMoreItems' value='0'>
									<!-- Table Head Ends Here -->

									<div class="table-data  ptlRequestRows">

									</div>

								</div>	

								

								

							</div>

							<!-- Right Section Ends Here -->

						</div>
					</div>
                                        <div class="col-md-12 padding-none text-right">
                                                <input type="button" value="Add Location" class="btn add-btn margin-top" id="addNewLocations">
                                        </div>
                                    
                                    
                                    
				</div>
				</div>
				<div class="">
					<div class="col-md-4 col-md-offset-4">
						{!! Form::submit('Search', ['name' => 'search','class'=>'btn theme-btn btn-block','id' => 'ptlAddBuyerSearch']) !!}
					</div>
				</div>
				
				{!! Form::close() !!}

	      </div>
	      
	    </div>
	  </div>
  </div>
<!-- Modal Window ends here --> 
@endsection