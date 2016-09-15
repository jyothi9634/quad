@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->


{{--*/ $serviceId = Session::get('service_id') /*--}}
@if(Session::has('transactionId') && Session::get('transactionId')!='')

	{{--*/ $transactionId = Session::get('transactionId') /*--}}
			<script>
			$(document).ready(function(){
			$("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");
     	   $("#erroralertmodal").modal({
               show: true
           }).one('click','.ok-btn',function (e){
        	   window.location="/ptl/buyerposts";
        	 
           });
		 });
			


</script>
				
		
@endif
<div class="main">

	<div class="container">
				<span class="pull-left">				
				@if(Session::get('service_id')==ROAD_FTL)
				<h1 class="page-title">Post & Get Quote (FTL)</h1>
				@elseif(Session::get('service_id')==ROAD_PTL)
				<h1 class="page-title">Post & Get Quote (LTL)</h1>
				@elseif(Session::get('service_id')==RAIL)
				<h1 class="page-title">Post & Get Quote (RAIL)</h1>
				@elseif(Session::get('service_id')==ROAD_INTRACITY)
				<h1 class="page-title">Post & Get Quote (INTRACITY)</h1>
				@elseif(Session::get('service_id')==OCEAN)
				<h1 class="page-title">Post & Get Quote (OCEAN)</h1>
				@elseif(Session::get('service_id')==COURIER)
				<h1 class="page-title">Post & Get Quote (COURIER)</h1>
				@elseif(Session::get('service_id')==AIR_INTERNATIONAL)
				<h1 class="page-title">Post & Get Quote (AIR INTERNATIONAL)</h1>
				@elseif(Session::get('service_id')==AIR_DOMESTIC)
				<h1 class="page-title">Post & Get Quote (AIR DOMESTIC)</h1>
				@else
				<h1 class="page-title">Post & Get Quote (FTL)</h1>
				@endif
                                <a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				</span>				
				@if ($url_search_search == 'byersearchresults')
				<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
				@endif

				<div class="clearfix"></div>
				
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-bottom-none margin-none">
					<div class="col-md-12 form-control-fld margin-bottom-none">
						<div class="radio-block margin-top">
							<div class="radio_inline"> {!! Form::radio('enquiry-type', SPOT , true, ['class' => 'field','id'=>'spot_enquiry_type']) !!} <label for="spot_enquiry_type"><span></span>Spot</label></div>
							<div class="radio_inline">{!! Form::radio('enquiry-type', TERM , '' , ['class' => 'field','id'=>'term_enquiry_type']) !!} <label for="term_enquiry_type"><span></span>Term Contract</label></div>
						</div>
					</div>
				</div>
				
			<div id="spot_show_hide_block"><!-- srinu added custom Div -->
			
			{!! Form::open(['url' =>'#','id' => 'ptlBuyerQuotelineitemsForm', 'autocomplete'=>'off']) !!}
			{!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!}
				
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
				<div class="col-md-12 form-control-fld">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="is_checkcommercial"  value="1" checked  /> <label for="is_checkcommercial"><span></span>Commercial</label></div>
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="non_checkcommercial" value="0" /> <label for="non_checkcommercial"><span></span>Non Commercial</label></div>
					</div>
				</div>
				@if(Session::get('service_id') == COURIER)
					@if(isset($session_search_values[6]) && $session_search_values[6] == 1)
					{{--*/ $domestic_dom = true /*--}}
					@else
					{{--*/ $domestic_dom = false /*--}}
					@endif
					
					
					@if(isset($session_search_values[6]) && $session_search_values[6] == 2)
					{{--*/ $domestic_int = true /*--}}
					@else
					{{--*/ $domestic_int = false /*--}}
					@endif
					
					@if(isset($session_search_values[10]) && $session_search_values[10] == 1)
					{{--*/ $doc_courier = true /*--}}
					@else
					{{--*/ $doc_courier = false /*--}}
					@endif
					
					
					@if(isset($session_search_values[10]) && $session_search_values[10] == 2)
					{{--*/ $parcel_courier = true /*--}}
					@else
					{{--*/ $parcel_courier = false /*--}}
					@endif
					
							@if(isset($session_search_values[6]) && $session_search_values[6] != '')
								<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', $domestic_dom, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', $domestic_int, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
											{!! Form::hidden('post_or_delivery_type', $session_search_values[6], array('id' => 'post_delivery_type')) !!}
											</div>
										</div>
									</div>
									
							@else		
									<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
											{!! Form::hidden('post_or_delivery_type', '1', array('id' => 'post_delivery_type')) !!}
											</div>
										</div>
									</div>
						@endif
				@endif
					<div class="col-md-12 padding-none inner-form margin-bottom-none">						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>

								@if($serviceId == AIR_INTERNATIONAL)					
				                    {!! Form::text('from_airport', $session_search_values[12], ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Airport*']) !!}
				                    {!! Form::hidden('from_airport_id', $session_search_values[13], array('id' => 'from_airport_id')) !!}
								@elseif($serviceId == OCEAN)									
				                    {!! Form::text('from_airport', $session_search_values[12], ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Ocean*']) !!}
				                    {!! Form::hidden('from_airport_id', $session_search_values[13], array('id' => 'from_airport_id')) !!}
			                    @elseif($serviceId == COURIER)									
				                    {!! Form::text('ptlFromLocation', $session_search_values[2] , ['id' => 'ptlFromLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Pincode *']) !!}
									{!! Form::hidden('ptlFromLocationId',$session_search_values[3], array('id' => 'ptlFromLocationId')) !!}
			                    @else
									{!! Form::text('ptlFromLocation', $session_search_values[12] , ['id' => 'ptlFromLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Pincode *']) !!}
									{!! Form::hidden('ptlFromLocationId', $session_search_values[13] , array('id' => 'ptlFromLocationId')) !!}	
								@endif
							</div>
							@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN &&  $serviceId!=COURIER)
							
								@if($session_search_values[10] == '1') 
										{{--*/ $session_search_selected = true /*--}}
									@else
										{{--*/ $session_search_selected = false /*--}}
									@endif
								{!! Form::checkbox('ptlDoorpickup', 1, $session_search_selected, ['class' => '' , 'id'=>'ptlDoorpickup']) !!}
								<span class="lbl padding-8">Door Pickup</span>
							@endif
						</div>
						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>

								@if($serviceId == AIR_INTERNATIONAL)   				                    
				                    {!! Form::text('to_airport',$session_search_values[14], ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Airport*']) !!}
				                    {!! Form::hidden('to_airport_id', $session_search_values[15], array('id' => 'to_airport_id')) !!}
								@elseif($serviceId == OCEAN)
									{!! Form::text('to_airport',$session_search_values[14], ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Ocean*']) !!}
				                    {!! Form::hidden('to_airport_id', $session_search_values[15], array('id' => 'to_airport_id')) !!}
				                @elseif($serviceId == COURIER)
									{!! Form::text('ptlToLocation', $session_search_values[4], ['id' => 'ptlToLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Pincode*']) !!}
									{!! Form::hidden('ptlToLocationId', $session_search_values[5] , array('id' => 'ptlToLocationId')) !!}
			                    @else
			                    	{!! Form::text('ptlToLocation', $session_search_values[14] , ['id' => 'ptlToLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Pincode*']) !!}
									{!! Form::hidden('ptlToLocationId', $session_search_values[15] , array('id' => 'ptlToLocationId')) !!}
			                    @endif
							</div>
							@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN &&  $serviceId!=COURIER)
								{!! Form::checkbox('ptlDoorDelivery', 1, $session_search_values[11], ['class' => '', 'id'=>'ptlDoorDelivery']) !!}
								<span class="lbl padding-8">Door Delivery</span>
							@endif
						</div>
						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('ptlDispatchDate',$session_search_values[0], ['id' => 'ptlDispatchDate','class' => 'flexible_dispatch_date calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *', 'readonly'=>"readonly"]) !!}
								
							</div>
								<input type="hidden" name="ptlFlexiableDispatch" id="ptlFlexiableDispatch_hidden" value="{{ $ptlFlexiableDispatch }}">
						</div>
												
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('ptlDeliveryhDate',$session_search_values[1], ['id' => 'ptlDeliveryhDate','class' => 'flexible_delivery_date calendar form-control to-date-control', 'placeholder' => 'Delivery Date (Optional)', 'readonly'=>"readonly"]) !!}
							</div>
								<input type="hidden" name="ptlFlexiableDelivery" id="ptlFlexiableDelivery_hidden" value="{{ $ptlFlexiableDelivery }}">
						</div>
						
						<div class="clearfix"></div>
	
						@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
                                                <div class="col-md-3 form-control-fld">
                                                    <div class="normal-select">
                                                    {!!	Form::select('ptlShipmentType',(['' => 'Shipment Type *'] +$shipmentTypes), $session_search_values[18] ,['class' =>'selectpicker','id'=>'ptlShipmentType']) !!}
                                                    </div>
						</div>
                                                <div class="col-md-3 form-control-fld">
							<div class="normal-select">
							  {!! Form::select('ptlSenderIdentity',(['' => 'Sender Identity *'] +$senderIdentity), $session_search_values[19] ,['class' =>'selectpicker','id'=>'ptlSenderIdentity']) !!}
							</div>
						</div>
                                                <div class="col-md-3 form-control-fld">
                                                    <div class="input-prepend">
                                                    {!! Form::text('ptlIECode', $session_search_values[20],  ['class' => 'form-control numericvalidation form-control1' ,'maxlength'=>'10', 'id'=>'ptlIECode','placeholder' => 'IE Code']) !!}
                                                    </div>
						</div>
						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								{!! Form::text('ptlProductMade', $session_search_values[21],  ['class' => 'form-control form-control1' , 'id'=>'ptlProductMade','placeholder' => 'Product Made']) !!}
							</div>
						</div>
	                 @endif
					</div>
					
					
					
					<div class="col-md-12 inner-block-bg inner-block-bg1 margin-sides">
					@if($serviceId==COURIER)
						<div class="col-md-12 padding-none margin-bottom">
								<div class="col-md-3 form-control-fld">
								
								@if(isset($session_search_values[10]) && $session_search_values[10] != '')
									<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', $doc_courier, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', $parcel_courier, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types', $session_search_values[10], array('id' => 'courier_types')) !!}
											</div>
										</div>
																		</div>
						</div>
								@else
								
									<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', false, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
											{!! Form::hidden('courier_or_types', '', array('id' => 'courier_types')) !!}
											</div>
										</div>
																	</div>
						</div>	
								@endif

						@endif
						<h2 class="sub-head margin-bottom">Add Item Details</h2>
						@if($serviceId == COURIER)
						
							@if(isset($session_search_values[10]) && $session_search_values[10] == 1)
							{{--*/ $doc_select =  'style = display:none'/*--}}
							@else
							{{--*/ $doc_select =  'style = display:block'/*--}}
							@endif
						
						<div id ='documents_display_courier' {{ $doc_select }} class="col-md-3 form-control-fld">
							<div class="normal-select">
									{!!	Form::select('ptlpurposesType',(['' => 'Courier Purposes*'] +$CourierTypes), $session_search_values[17] ,['class' =>'selectpicker','id'=>'ptlPurposesType']) !!}
							</div>
						</div>
						<div class="clearfix"></div>
						@endif	
						@if($serviceId!=COURIER)
							<div class="col-md-6 padding-none">
								<h5 class="caption-head"></h5>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										{!!	Form::select('ptlLoadType',(['' => 'Load Type *'] +$loadTypes), $session_search_values[2] ,['class' =>'selectpicker','id'=>'ptlLoadType']) !!}
									</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										{!!	Form::select('ptlPackageType',(['' => 'Packaging Type *'] +$packageTypes), $session_search_values[3] ,['class' =>'selectpicker','id'=>'ptlPackageType']) !!}
									</div>
								</div>
							</div>								
						@endif
								@if($serviceId==COURIER)
								<div id ='documents_display' {{ $doc_select }} class="col-md-6 form-control-fld">
								@else
								<div id ='documents_display' class="col-md-6 form-control-fld">
								@endif
								<h5 class="caption-head margin-left-none">
									Package Weight (Volumetric Weight)
									@if($serviceId==COURIER)
									<span class="pull-right">
									
									@if(isset($session_search_values[15]) && $session_search_values[15] != '')
									
									<span id="displayVolumeW">{{ $session_search_values[15] }} CCM</span>
									
									@else
									
									<span id="displayVolumenone">Total Volume</span><span id="displayVolumeW"></span>
									
									@endif						
								
										
										{!!	Form::hidden('ptlDisplayVolumeWeight','',array('class'=>'form-control','placeholder'=>'Display Vol. Weight *','id'=>'ptlDisplayVolumeWeight')) !!}
									</span>
									@else
									<span class="pull-right">
									@if(isset($session_search_values[17]) && $session_search_values[17] != '')
									
									<span id="displayVolumeW">{{ $session_search_values[17] }} CFT</span>
									
									@else
									
									<span id="displayVolumenone">Total Volume</span><span id="displayVolumeW"></span>
									
									@endif
										
										{!!	Form::hidden('ptlDisplayVolumeWeight','',array('class'=>'form-control','placeholder'=>'Display Vol. Weight *','id'=>'ptlDisplayVolumeWeight')) !!}
									</span>
									@endif
								</h5>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlLengthCourier',$session_search_values[11],array('class'=>'form-control form-control1 clsCOURLengthCM','placeholder'=>'L *','id'=>'ptlLengthCourier')) !!}
									@else		
										{!!	Form::text('ptlLength',$session_search_values[4],array('class'=>'form-control form-control1  clsLTL4LengthCM','placeholder'=>'L *','id'=>'ptlLength')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlWidthCourier',$session_search_values[12],array('class'=>'form-control form-control1 border-left-none clsCOURBreadthCM','placeholder'=>'B *','id'=>'ptlWidthCourier')) !!}
									@else
										{!!	Form::text('ptlWidth',$session_search_values[5],array('class'=>'form-control form-control1 border-left-none clsLTL4BreadthCM','placeholder'=>'B *','id'=>'ptlWidth')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlHeightCourier',$session_search_values[13],array('class'=>'form-control form-control1 border-left-none clsCOURHeightCM','placeholder'=>'H *','id'=>'ptlHeightCourier')) !!}
									@else
										{!!	Form::text('ptlHeight',$session_search_values[6],array('class'=>'form-control form-control1 border-left-none clsLTL4HeightCM','placeholder'=>'H *','id'=>'ptlHeight')) !!}
									@endif	
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days unit-days-align">
										@if($serviceId == COURIER)
											{!!	Form::select('ptlCheckVolWeightCourier',($volumeWeightTypes), $session_search_values[14] ,['class' =>'selectpicker clsIDPackageWeight','id'=>'ptlCheckVolWeightCourier', 'onChange'=>'volumeWeight(this.value,21)']) !!}
										@else
											{!!	Form::select('ptlCheckVolWeight',($volumeWeightTypes), $session_search_values[16] ,['class' =>'selectpicker clsIDPackageWeight','id'=>'ptlCheckVolWeight', 'onChange'=>'volumeWeight(this.value)']) !!}
										@endif	
										</span>
									</div>
								</div>
							</div>
							
							@if($serviceId==COURIER)
							<div class="col-md-6 padding-none">
								<h5 id ='parcel_hide' {{ $doc_select }} class="caption-head margin-left-none">
									
								</h5>
								<div class="col-md-7 form-control-fld">	
							@endif	
								
								
							@if($serviceId!=COURIER)	
							<div class="clearfix"></div>
							<div class="col-md-3 form-control-fld">
							@endif
								<div class="col-md-7 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlUnitsWeight',$session_search_values[7],array('class'=>'form-control form-control1 clsIDmax_weight_accepted0 clsCOURMaxWeightGms','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}
									@else
										{!!	Form::text('ptlUnitsWeight',$session_search_values[7],array('class'=>'form-control form-control1 clsIDptlUnitsWeight0 clsLTL4MaxWeightGms','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}
									@endif
									</div>
								</div>
								@if($serviceId!=COURIER)
								<div class="col-md-5 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days">
											{!!	Form::select('ptlCheckUnitWeight',(['' => 'Weight Unit *'] +$unitsWeightTypes), $session_search_values[8] ,['class' =>'selectpicker clsSelMaxwgtAptType','id'=>'ptlCheckUnitWeight', 'data-posttype' => '0']) !!}	
										</span>
									</div>	
								</div>
								@else
								<div class="col-md-5 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days">
											{!!	Form::select('ptlCheckUnitWeight',(['' => 'Weight Unit *'] +$unitsWeightTypes), $session_search_values[18] ,['class' =>'selectpicker clsSelMaxwgtAptType','id'=>'ptlCheckUnitWeight', 'data-posttype' => '0']) !!}	
										</span>
									</div>	
								</div>
								@endif
							</div>
							
								

							@if($serviceId==COURIER)	
							<div class="col-md-5 form-control-fld">
							 @else
							 <div class="col-md-3 form-control-fld">
							@endif
							
							@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==RAIL )
									<div class="input-prepend">
										{!!	Form::text('ptlNopackages',$session_search_values[9],array('class'=>'form-control form-control1 clsAirInitNoOfPackages','placeholder'=>'No of Packages *','id'=>'ptlNopackages')) !!}
									</div>
                                                        @elseif($serviceId==COURIER)
                                                                    <div class="input-prepend">
										{!!	Form::text('ptlNopackages',$session_search_values[9],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
									</div>
                                                        @else
                                                                    <div class="input-prepend">
										{!!	Form::text('ptlNopackages',$session_search_values[9],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
									</div>
								
                                                        @endif

							
							</div>
							
							@if($serviceId==COURIER)
							</div>
							<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								{!!	Form::text('packeagevalue',$session_search_values[16],array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'Package Value *','id'=>'packeagevalue', 'maxlength' => 5)) !!}
							</div>
							</div>
							<div class="col-md-3 form-control-fld">
							@endif
							
							@if($serviceId!=COURIER)
							<div class="col-md-6 form-control-fld">
							@endif
							
								{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
								
								{!! Form::submit('Add +', ['class' => 'btn add-btn','name' => 'add','id' => 'ptlAddMoreItems','onclick'=>"updatepoststatus(1)"]) !!}
								
			   					<div id="error-ptl-add-item" class="error "></div>
							</div>
					</div>
			





<input type="hidden" name="ptlBuyerSearchCompareId" value="" id="ptlBuyerSearchCompareId">
{!!	Form::hidden('update_ptl_line',0,array('class'=>'','id'=>'update_ptl_line'))!!}
{!!	Form::hidden('update_row_count_ptl','',array('class'=>'','id'=>'update_row_count_ptl'))!!}	
{!!	Form::hidden('update_location_divcount','',array('class'=>'','id'=>'update_location_divcount'))!!}
{!!	Form::hidden('locations_count',1,array('class'=>'','id'=>'locations_count'))!!}
				</div>
				{!! Form::close() !!}

				{!! Form::open(['url' =>'ptl/createbuyerquote','id' => 'ptlBuyerQuoteInsert']) !!}	
				{!!	Form::hidden('is_commercial[]',1,array('class'=>'','id'=>'is_commercial'))!!}
				
				<div class="col-md-12 inner-block-bg inner-block-bg1" >

					<div class="col-md-12 padding-none">
						<div class="main-inner"> 							

							<!-- Right Section Starts Here -->

							<div class="main-right">
								<div id="ptl_addmore_locations">
								<div class="ptl_add_locations" id="ptl_add_locations_1">
								<h2 class="sub-head"><span class="from-head fromPin">From Location</span> - <span class="to-head toPin">To Location</span></h2>
								
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									 <input type="hidden" id='ptlBuyerAddMoreItems' value='0'>
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										@if($serviceId!=21)
                                                                                <div class="col-md-2 padding-left-none">Load Type</div>
										<div class="col-md-2 padding-left-none">Package Type</div>										
										@endif
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Courier Purpose</div>
										@endif
										<div id ='volume_display' class="col-md-2 padding-left-none">Volume</div>
										<div class="col-md-2 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Package Value (Rs)</div>
										@endif
										<div class="col-md-2 padding-left-none"></div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data ptlRequestRows"></div>

								</div>
								</div></div>								

							<div class="col-md-12 form-control-fld margin-none text-right">
								<input type="button" value="Add New Location" class="btn add-btn flat-btn" id="addNewLocations">
							</div>	

							</div>

							<!-- Right Section Ends Here -->

						</div>
					</div>
					
				</div>

				<div class="col-md-12 inner-block-bg inner-block-bg1" >
						<div class="col-md-12 form-control-fld margin-none">
							<div class="radio-block">
								<div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service ptlcreate-posttype" /> <label for="post-public"><span></span>Post Public</label></div>
								<div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service ptlcreate-posttype"/> <label for="post-private"><span></span>Post Private</label></div>
							</div>
						</div>
						
						
					<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
						<input type="text" id="demo-input-local" class="form-control form-control1 alphaonly_strVal" name="seller_list" />
					</div>
					<div class="clearfix"></div>
					@if($serviceId==COURIER)
					<div class="check-box form-control-fld margin-top-5 margin-bottom-none">
						<div class="normal-checkbox">
						{!! Form::checkbox('prohibited', 1, 0, ['class' => 'field','id'=>'prohibited'])!!}
						<span class="lbl padding-8">I Agree that this package doesn't contain any prohibited items <a href="#" data-toggle="tooltip" title="nuclear material/animals etc."><strong>?</strong></a>.</span>
						</div>
					</div>
					@endif
					<div class="check-box form-control-fld margin-top-5 margin-bottom-none">
						<div class="normal-checkbox">
						{!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree'])!!}
						<span class="lbl padding-8">Accept Term &amp; Conditions ( Digital Contract )</span>
						</div>
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="col-md-4 col-md-offset-4">
						{!! Form::submit('Get Quote', ['name' => 'Get Quote','class'=>'btn theme-btn btn-block','id' => 'ptlAddBuyerQuote']) !!}
					</div>


			</div>	<!-- Close container Div -->

					
			{!! Form::close() !!}			
		</div> <!-- Srinu End div main custom div -->
		
		
		<div class="container">
		<!-- start Term in LTL -->
		<div id="term_show_hide_block"  style="display:none"><!-- srinu added custom Div -->
			
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none border-bottom-none margin-bottom-none">

					{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}
                    {!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!} 
					
					<div class="col-md-12 padding-none inner-form margin-bottom-none">						
						
						@if(Session::get('service_id') == COURIER)
					@if(isset($session_search_values[6]) && $session_search_values[6] == 1)
					{{--*/ $domestic_dom = true /*--}}
					@else
					{{--*/ $domestic_dom = false /*--}}
					@endif
					
					
					@if(isset($session_search_values[6]) && $session_search_values[6] == 2)
					{{--*/ $domestic_int = true /*--}}
					@else
					{{--*/ $domestic_int = false /*--}}
					@endif
					
					@if(isset($session_search_values[10]) && $session_search_values[10] == 1)
					{{--*/ $doc_courier = true /*--}}
					@else
					{{--*/ $doc_courier = false /*--}}
					@endif
					
					
					@if(isset($session_search_values[10]) && $session_search_values[10] == 2)
					{{--*/ $parcel_courier = true /*--}}
					@else
					{{--*/ $parcel_courier = false /*--}}
					@endif
					
							@if(isset($session_search_values[6]) && $session_search_values[6] != '')
								<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'Domestic', $domestic_dom, ['id' => 'term_domestic']) !!} <label for="term_domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'International', $domestic_int, ['id' => 'term_international']) !!} <label for="term_international"><span></span>International</label>
											{!! Form::hidden('term_post_or_delivery_type', $session_search_values[6], array('id' => 'term_post_delivery_type')) !!}
											</div>
										</div>
									</div>
									
							@else		
									<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'Domestic', true, ['id' => 'term_domestic']) !!} <label for="term_domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'International', false, ['id' => 'term_international']) !!} <label for="term_international"><span></span>International</label>
											{!! Form::hidden('term_post_or_delivery_type', '1', array('id' => 'term_post_delivery_type')) !!}
											</div>
										</div>
									</div>
						@endif
				@endif
						
						<div class="clearfix"></div>
						
						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('term_dispatch_date', '', ['id' => 'term_dispatch_date','class' => 'form-control calendar from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
							</div>
								
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('term_delivery_date', '', ['id' => 'term_delivery_date','class' => 'form-control calendar to-date-control', 'placeholder' => 'Valid To *','readonly'=>"readonly"]) !!}
							</div>
							
						</div>
					
					@if($serviceId == COURIER)
					<div class="col-md-3 padding-none">
								<div class="radio-block padding-top-8">
									<div class="radio_inline">
										{!! Form::radio('term_courier_types', 'Documents', true, ['id' => 'term_documents']) !!} <label for="term_documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
										{!! Form::radio('term_courier_types', 'Parcel', false, ['id' => 'term_parcel']) !!} <label for="term_parcel"><span></span>Parcel</label>
										{!! Form::hidden('term_courier_or_types', 1, array('id' => 'term_courier_types')) !!}
									</div>
								</div>
					</div>
					@endif
					</div>
					<div class="col-md-12 form-control-fld form-control-fld1 margin-none">
					<div class="col-md-12 inner-block-bg inner-block-bg1">
						<h2 class="sub-head margin-bottom">Add Item Details</h2>
							<div class="col-md-6 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										<div class="input-prepend">
										 <span class="add-on"><i class="fa fa-map-marker"></i></span>
										@if($serviceId == AIR_INTERNATIONAL)					
				                    	{!! Form::text('from_airport_term', '', ['id' => 'from_airport_term','class' => 'form-control', 'placeholder' => 'From Airport*']) !!}
				                   		{!! Form::hidden('from_airport_term_id', '', array('id' => 'from_airport_term_id')) !!}
										@elseif($serviceId == OCEAN)									
				                   		{!! Form::text('from_airport_term', '', ['id' => 'from_airport_term','class' => 'form-control', 'placeholder' => 'From Ocean*']) !!}
				                    	{!! Form::hidden('from_airport_term_id', '', array('id' => 'from_airport_term_id')) !!}
				                    	@else
										{!! Form::text('term_from_location_pincode', '', ['id' => 'term_from_location_pincode', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Pincode *']) !!}
										{!! Form::hidden('term_from_location_pincode_id', '', array('id' => 'term_from_location_pincode_id')) !!}
										@endif                                                                                
                                                                                
										</div>
									</div>
                                                                    
                                                                    @if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN &&  $serviceId!=COURIER)
                                                                    {!! Form::checkbox('term_door_pickup', 1, null, ['class' => '' , 'id'=>'term_door_pickup']) !!}
                                                                        <span class="lbl padding-8">Door Pickup</span>
                                                                    @endif
                                                                    
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
									<div class="input-prepend">
										 <span class="add-on"><i class="fa fa-map-marker"></i></span>
										@if($serviceId == AIR_INTERNATIONAL)   				                    
				                    	{!! Form::text('to_airport_term','', ['id' => 'to_airport_term','class' => 'form-control', 'placeholder' => 'To Airport*']) !!}
				                    	{!! Form::hidden('to_airport_term_id', '', array('id' => 'to_airport_term_id')) !!}
										@elseif($serviceId == OCEAN)
										{!! Form::text('to_airport_term','', ['id' => 'to_airport_term','class' => 'form-control', 'placeholder' => 'To Ocean*']) !!}
				                    	{!! Form::hidden('to_airport_term_id', '', array('id' => 'to_airport_term_id')) !!}
			                    		@else
										{!! Form::text('term_to_location_pincode', '', ['id' => 'term_to_location_pincode', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal','placeholder' => 'To Pincode *']) !!}
										{!! Form::hidden('term_to_location_pincode_id', '', array('id' => 'term_to_location_pincode_id')) !!}
										@endif
                                                                                
										</div>
									</div>
                                                                    
                                                                    @if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN &&  $serviceId!=COURIER)
                                                                        {!! Form::checkbox('term_door_delivery', 1, null, ['class' => '', 'id'=>'term_door_delivery']) !!}
                                                                        <span class="lbl padding-8">Door Delivery</span>
                                                                    @endif                                                                    
                                                                    
								</div>
								@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
			                    <div class="col-md-6 form-control-fld">
									<div class="normal-select">
			                        {!!	Form::select('term_shipment_type',(['' => 'Shipment Type *'] +$shipmentTypes), '' ,['class' =>'selectpicker','id'=>'term_shipment_type']) !!}
			                        </div>
								</div>
			                    <div class="col-md-6 form-control-fld">
									<div class="input-prepend">
			                        {!! Form::text('term_iecode', '',  ['class' => 'form-control numericvalidation form-control1' ,'maxlength'=>'10', 'id'=>'term_iecode','placeholder' => 'IE Code']) !!}
			                        </div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
									  {!! Form::select('term_sender_identify',(['' => 'Sender Identity *'] +$senderIdentity), '' ,['class' =>'selectpicker','id'=>'term_sender_identify']) !!}
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('term_product_mode', '',  ['class' => 'form-control form-control1' , 'id'=>'term_product_mode','placeholder' => 'Product Made']) !!}
									</div>
								</div>
								<div class="clearfix"></div>
			                 @endif								
							</div>		
							@if($serviceId !=COURIER)
							<div class="col-md-6 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										{!!	Form::select('term_load_type',(['' => 'Select Load Type *'] +$loadTypes), '',['class' =>'selectpicker','id'=>'term_load_type','onChange'=>'return getTermCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										{!!	Form::select('term_package_type',(['' => 'Packaging Type *'] +$packageTypes), '' ,['class' =>'selectpicker','id'=>'term_package_type']) !!}
									</div>
								</div>
							</div>	
                                                        
							@endif
                            <div class="clearfix"></div>
							
							@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==RAIL )
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										{!!	Form::text('term_noof_packages','',array('class'=>'form-control form-control1 clsAirInitNoOfPackages','placeholder'=>'No of Packages *','id'=>'term_noof_packages')) !!}																		
									</div>
								</div>	
                            @else
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										{!!	Form::text('term_noof_packages','',array('class'=>'form-control form-control1 clsAirInitNoOfPackages','placeholder'=>'No of Packages *','id'=>'term_noof_packages')) !!}																		
									</div>
								</div>	
                            @endif


							@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">								
									{!!	Form::text('term_volume','',array('class'=>'form-control form-control1 clsAirInitTVolumeCCM','placeholder'=>'Volume *','id'=>'term_volume')) !!}
									{!!	Form::hidden('term_units','CCM',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
									<span class="add-on unit">CCM</span>
								</div> 
							</div>	
							@elseif($serviceId==OCEAN)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">								
									{!!	Form::text('term_volume','',array('class'=>'form-control form-control1 clsAirInitTVolumeCCM','placeholder'=>'Volume *','id'=>'term_volume')) !!}
									{!!	Form::hidden('term_units','CM',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
									<span class="add-on unit">CM</span>
								</div> 
							</div>	
							@elseif($serviceId==COURIER)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">								
									{!!	Form::text('term_volume','',array('class'=>'form-control form-control1 numberVal threedigitstwodecimals_deciVal','placeholder'=>'Volume *','id'=>'term_volume')) !!}
									{!!	Form::hidden('term_units','CFT',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
									<span class="add-on unit">CFT</span>
								</div> 
							</div>
							@else
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">								
									{!!	Form::text('term_volume','',array('class'=>'form-control form-control1 clsRailVolumepCFT','placeholder'=>'Volume *','id'=>'term_volume')) !!}
									{!!	Form::hidden('term_units','CFT',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
									<span class="add-on unit">CFT</span>
								</div> 
							</div>	
							@endif
							<div class="clearfix"></div>
							
						</div>
					</div>
							<div class="col-md-12 form-control-fld text-right margin-none margin-top">
								{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
								{!!	Form::hidden('update_ltl_term_line',0,array('class'=>'','id'=>'update_ltl_term_line'))!!}
								{!!	Form::hidden('update_ltl_term_row_count','',array('class'=>'','id'=>'update_ltl_term_row_count'))!!}	
								{!!	Form::hidden('update_ltl_term_row_unique','',array('class'=>'','id'=>'update_ltl_term_row_unique'))!!}
			     				{!! Form::submit('Add New Location', ['class' => 'btn add-btn flat-btn','name' => 'term_add_more_locations','id' => 'term_add_more_locations']) !!}	
			   					<div id="error-add-item-term" class="error "></div>
							</div>

				</div>
				{!! Form::close() !!}

				{!! Form::open(['url' =>'ptl/createbuyerquote','id' => 'term_buyer_quote_ltl', 'files'=>true, 'autocomplete'=>'off']) !!}
				<input type="hidden" name ='price_slap_hidden_value' id='price_slap_hidden_value' value='0'>	
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none " >

					<div class="col-md-12 padding-none">
						<div class="main-inner"> 							

							<!-- Right Section Starts Here -->

							<div class="main-right">
								<div id="ptl_addmore_locations">
								<div class="ptl_add_locations">
								
								
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									 <input type="hidden" id='ptlBuyerAddMoreItems' value='0'>
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-2 padding-left-none">From</div>
										<div class="col-md-2 padding-left-none">To</div>
										@if($serviceId!=21)
										<div class="col-md-2 padding-left-none">Load Type</div>
										<div class="col-md-1 padding-left-none">Package Type</div>
										@endif
										<div class="col-md-1 padding-left-none">Volume</div>
										<div class="col-md-1 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
										<div class="col-md-1 padding-left-none"></div>
									</div>

									<!-- Table Head Ends Here -->

									<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
				 					<div class="table-data term_request_rows_ltl"></div>

								</div>
								</div></div>								

							</div>

							<!-- Right Section Ends Here -->

						</div>
					</div>
					
					</div>


					<div class="col-md-12 inner-block-bg inner-block-bg1" >	
					
					@if($serviceId == COURIER)
					<div class="col-md-12 padding-none">
					
					<div class="col-md-3 form-control-fld padding-right-none">
								<div class="col-md-8 padding-none">
									<div class="input-prepend">
										{!! Form::text('max_weight_accepted_text',null,['class'=>'form-control form-control1 clsIDmax_weight_accepted1 clsCOURMaxWeightGms','id'=>'max_weight_accepted','placeholder'=>'Maximum Weight *']) !!}
									</div>
								</div>
							
							<div class="col-md-4 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days manage">
											
										{!! Form::select('units_max_weight',($volumeWeightcourier), null, ['id' => 'units_max_weight','class' => 'selectpicker clsSelTermMaxwgtAptType bs-select-hidden', 'data-posttype' => '1']) !!}
										</span>
									</div>	
								</div>
								</div>
					<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none slabtable price-slap-add">

										<!-- Table Row Starts Here -->

										<div class="add-price-slap table-row inner-block-bg">
										 <div class="price-slap">
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													<input type="text" class="form-control form-control1" id = 'low_price' readonly value ='0.00' name = 'low_price' placeholder="0.00" />
												</div>
											</div>
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													<input type="text" class="form-control form-control1 numberVal" id = 'high_price' name = 'high_price' placeholder="0.00" />
												</div>
											</div>
											<div class="col-md-1 form-control-fld padding-left-none">
											<input type="button" class="btn add-box-buyer add-btn" value="Add">
											</div>
										</div>
										
										</div>

										<!-- Table Row Ends Here -->

									</div>
									<div class="col-md-5 form-control-fld padding-none">
										<input type="hidden" value="0" id ='check_max_weight_assign' name="check_max_weight_assign">
										<div class="col-md-1 form-control-fld">
										<div class="checkbox_inline padding-top-8">
										{!! Form::checkbox('check_max_weight', 1, false,array('id'=>'check_max_weight','disabled'=>'disabled')) !!}
										<span class="lbl padding-8"></span></div>
										</div>

										<div class="col-md-5 form-control-fld padding-none">
										{!! Form::text('incremental_weight_text',null,['class'=>'form-control form-control1 numberVal','id'=>'incremental_weight','placeholder'=>'Incremental Weight*','readonly']) !!}
										</div>	
									</div>	
								</div>	

								<!-- Table Starts Here -->
							</div> 
					</div>
					@endif
					
					<!-- bid type section starts-->
					<div class="col-md-12 padding-none inner-form">					
						<div class="col-md-3 form-control-fld">
						<label class="col-md-4 padding-left-none padding-right-none padding-top-8">Bid Type * :</label>
							<div class="normal-select col-md-8 padding-none">  
								{!!	Form::select('bid_type',($bid_type), '' ,['class' =>'selectpicker form_control','id'=>'bid_type']) !!}
							</div>
						</div>							
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('last_bid_date', '', ['id' => 'last_bid_date','class' => 'form-control calendar', 'placeholder' => 'Bid Closure Date *','readonly'=>"readonly"]) !!}
							</div>
					 	</div>							
							<div class="col-md-3 form-control-fld">
								<div id="bid_time_icon_add" class="input-prepend date clsbid_close_time">
									<span class="add-on"><i class="fa fa-clock-o"></i></span>
									{!! Form::text('bid_close_time', '', ['id' => 'bid_close_time','class' => 'form-control form-control1 disable-bg-white', 'placeholder' => 'Bid Closure Time *', 'readonly'=>"readonly"]) !!}
								</div>
								<label for="bid_close_time" id="err_bid_close_time" class="error"></label>
							</div>							
							<div class="clearix"></div>
							<div class="col-md-12 form-control-fld"><span>Bid Terms & Conditions</span></div>			
					
					</div>
					<!-- 	bid type section ends-->
					
					<!--file upload div starts-->
					<input type="hidden" name ='term_next_terms_count_search' id='term_next_terms_count_search' value='0'>
					<div class="documents-terms">
					<div class="col-md-12 padding-none text-box">
					<span style='display:none;' class="box-number-delete">2</span>
						<div class="col-md-4 form-control-fld">
							 	              
<!-- 	                			<input type="text" class="form-control form-control1 upload-control" readonly>  -->
										<div class="upload-fld">
											<button class="btn add-btn upload-browse-btn pull-right">Browse...</button>
											<input type="file" name="terms_condtion_types_term_defualt" class="form-control form-control1 update_txt" value="" id="terms_condtion_types_term_defualt" />
							            </div>
									</div>
									<div class="col-md-3 form-control-fld">	
								           		 									
						
								<input type="button" class="documents-add btn add-btn" value="Add +">
							</div>	
							<div class="clearfix"></div>													
					</div>
					</div>
					
					<div class="col-md-9 form-control-fld">								
							<textarea  class="form-control form-control1" name="buyer_notes" id="buyer_notes" placeholder="Comments" maxlength="500"></textarea>	
					</div>
					
					
					<!--file upload div ends-->		

					</div>
					<div class="col-md-12 inner-block-bg inner-block-bg1" >				
					
					<div class="col-md-12 form-control-fld move-bottom-10">
						<div class="radio-block">
							<div class="radio_inline">
							{!! Form::radio('quoteaccess_id', 1 , true, ['class' => 'field','id'=>'term_quote_access_public']) !!} <label for="term_quote_access_public"><span></span>Post Public</label></div>
							<div class="radio_inline">
							{!! Form::radio('quoteaccess_id', 2 , '' , ['class' => 'field create-posttype-service-ltl-term','id'=>'term_quote_access_private']) !!} <label for="term_quote_access_private"><span></span>Post Private</label></div>
						</div>
					</div>					
					<div class="clearfix"></div>					
					<div class="col-md-3 form-control-fld" id="showhidepost" style="display:none;">					
						<input type="text" class="form-control form-control1 alphaonly_strVal" id="term_seller_list" name="term_seller_list" placeholder="Seller Name (Auto Search)"/>
					</div>
					<div class="clearfix"></div>
					<div class="check-box form-control-fld">
					{!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree'])!!}
					<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					</div>
				 </div>
				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					{!!	Form::hidden('enquiry_type',TERM,array('class'=>'form-control','id'=>'enquiry_type')) !!}
					<input type="hidden" name="confirm_but" id="confirm_but" value="">
					{!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote_save_ltl']) !!}
					{!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote_save_ltl_draft']) !!}
				</div>


			</div>	<!-- Close container Div -->
			</div>
			{!! Form::close() !!}			
		</div> <!-- Srinu End div main custom div -->
			
				
</div>		<!-- Enad Main Div -->
@include('partials.footer')
@endsection