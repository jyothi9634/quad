@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


			<!--div class="home-search gray-bg"-->	
					
		    {{--*/ $serviceId = Session::get('service_id') /*--}}
		    
		    <div class="main">		 	
			<div class="container">
				{!! Form::open(['url' =>'#','id' => 'ptlBuyerQuotelineitemsForm', 'autocomplete'=>'off','method'=>'get']) !!}
                {!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!} 
				
				
				<div class="col-md-12 inner-block-bg inner-block-bg1">
				@if(Session::get('service_id') == COURIER)
					<div class="col-md-3 form-control-fld margin-bottom-none margin-top">
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
                                
                                <div class="col-md-12 form-control-fld margin-none">
                                    <div class="radio-block">
                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial" class="is_commercial" value="1" checked /> <label for="is_commercial"><span></span>Commercial</label></div>
                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" class="is_commercial" value="0" /> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                    </div>
                                </div>
                                
				<div class="col-md-12 padding-none inner-form margin-bottom-none margin-top">
					@if($serviceId == AIR_INTERNATIONAL)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_airport', '', ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Airport*']) !!}
	                                {!! Form::hidden('from_airport_id', '', array('id' => 'from_airport_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_airport','', ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Airport*']) !!}
	                                {!! Form::hidden('to_airport_id', '', array('id' => 'to_airport_id')) !!}
								</div>
							</div>

						@elseif($serviceId == OCEAN)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_airport', '', ['id' => 'from_airport','class' => 'form-control', 'placeholder' => 'From Ocean*']) !!}
	                                {!! Form::hidden('from_airport_id', '', array('id' => 'from_airport_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_airport','', ['id' => 'to_airport','class' => 'form-control', 'placeholder' => 'To Ocean*']) !!}
	                                {!! Form::hidden('to_airport_id', '', array('id' => 'to_airport_id')) !!}
								</div>
							</div>

						@else
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlFromLocation', '' , ['id' => 'ptlFromLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Pincode*']) !!}
	                           		{!! Form::hidden('ptlFromLocationId', '' , array('id' => 'ptlFromLocationId')) !!}
								</div>
							</div>
							@if($serviceId == COURIER)
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlToLocation', '' , ['id' => 'ptlToLocation', 'class'=>'form-control', 'placeholder' => 'To Pincode*']) !!}
	                           		{!! Form::hidden('ptlToLocationId', '' , array('id' => 'ptlToLocationId')) !!}
								</div>
							</div>
							@else
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('ptlToLocation', '' , ['id' => 'ptlToLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'To Pincode*']) !!}
	                           		{!! Form::hidden('ptlToLocationId', '' , array('id' => 'ptlToLocationId')) !!}
								</div>
							</div>
							@endif
                        @endif
                        	

							
                    		<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('ptlDispatchDate','', ['id' => 'ptlDispatchDate','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *', 'readonly'=>"readonly"]) !!}
									Flexible Dispatch Dates
								</div>
							</div>
							<div class="col-md-3 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('ptlDeliveryhDate','', ['id' => 'ptlDeliveryhDate','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date (Optional)', 'readonly'=>"readonly"]) !!}
									Flexible Delivery Dates
								</div>
							</div>
							
							<div class="clearfix"></div>
							
								@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN && $serviceId!=COURIER)
								
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										
										
									</div>
									{!! Form::checkbox('ptlDoorpickup', 1, null, ['class' => '' , 'id'=>'ptlDoorpickup']) !!}
										<span class="lbl padding-8">Door Pickup</span>
							    </div>
							    <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										
										
									</div>
									{!! Form::checkbox('ptlDoorDelivery', 1, null, ['class' => '', 'id'=>'ptlDoorDelivery']) !!}
										<span class="lbl padding-8">Door Delivery</span>
							    </div>
						    @endif
						    
						    
						    @if($serviceId == AIR_INTERNATIONAL ||  $serviceId == OCEAN ||  $serviceId == COURIER)
						    <div class="col-md-3 form-control-fld"></div>
							<div class="col-md-3 form-control-fld"></div>	
							@endif
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
	                                
	                            </div>
	                            <input type="hidden" name="ptlFlexiableDispatch" id="ptlFlexiableDispatch_hidden" value="0">	                                
	                            
	                        </div>

	                       <div class="col-md-3 form-control-fld">
								<div class="input-prepend">
	                               
	                            </div>
	                            <input type="hidden" name="ptlFlexiableDelivery" id="ptlFlexiableDelivery_hidden" value="0">
	                           
	                            
	                        </div>
						  
							<div class="clearfix"></div>



						    @if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
								            {!!	Form::select('ptlShipmentType',(['' => 'Shipment Type *'] +$shipmentTypes), '' ,['class' =>'selectpicker','id'=>'ptlShipmentType']) !!}
								    </div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
											<span class="add-on"><i class="fa fa-archive"></i></span>
								            {!!	Form::select('ptlSenderIdentity',(['' => 'Sender Identity *'] +$senderIdentity), '' ,['class' =>'selectpicker','id'=>'ptlSenderIdentity']) !!}
								        
								    </div>
								</div>
                            @endif
							
							@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
								<div class="col-md-3 form-control-fld margin-none">
								    <div class="input-prepend">
								    		<span class="add-on"><i class="fa fa-balance-scale"></i></span>
								            {!! Form::text('ptlIECode', '',  ['class' => 'form-control numericvalidation', 'maxlength'=>'10','id'=>'ptlIECode','placeholder' => 'IE Code']) !!}
								    </div>
								</div>
								<div class="col-md-3 form-control-fld margin-none">
								    <div class="input-prepend">
								    	<span class="add-on"><i class="fa fa-balance-scale"></i></span> 
								        {!! Form::text('ptlProductMade', '',  ['class' => 'form-control' , 'id'=>'ptlProductMade','placeholder' => 'Product Made']) !!}
								    </div>
								</div>
							@endif
					</div>
					
					
				
				<div class="small-align-div">		
				<div class="col-md-12 padding-none inner-block-bg padding-10 margin-bottom">

					@if($serviceId==COURIER)
					<div class="col-md-12 padding-none">
							<div class="col-md-3 padding-none margin-bottom">
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
					<h2 class="sub-head margin-bottom margin-left-none">Add Item Details</h2>

						@if($serviceId == COURIER)
						<div id ='documents_display_courier' class="col-md-3 form-control-fld padding-none">
							<div class="normal-select">
									{!!	Form::select('ptlpurposesType',(['' => 'Courier Purposes*'] +$CourierTypes), '' ,['class' =>'selectpicker','id'=>'ptlPurposesType']) !!}
							</div>
						</div>
						<div class="clearfix"></div>
						@endif	
						
					@if($serviceId!=COURIER)
					<div class="col-md-6 padding-none">
						<h5 class="caption-head"></h5>
						<div class="col-md-6 form-control-fld">
							<div class="normal-select">
								{!!	Form::select('ptlLoa	dType',(['11'=>'Load Type (Any)'] +$loadTypes), '' ,['class' =>'selectpicker','id'=>'ptlLoadType']) !!}
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="normal-select">
								{!!	Form::select('ptlPackageType',(['' => 'Packaging Type *'] +$packageTypes), '' ,['class' =>'selectpicker','id'=>'ptlPackageType']) !!}
							</div>
						</div>
					</div>
					@endif




					<div  id ='documents_display' class="col-md-6 padding-none">
						<h5 class="caption-head margin-left-none">
									Package Weight (Volumetric Weight)
									<span class="pull-right">
										<span id="displayVolumenone">Total Volume</span><span id="displayVolumeW"></span>
										{!!	Form::hidden('ptlDisplayVolumeWeight','',array('class'=>'form-control','placeholder'=>'Display Vol. Weight *','id'=>'ptlDisplayVolumeWeight')) !!}
									</span>
								</h5>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlLengthCourier','',array('class'=>'form-control clsCOURLengthCM form-control1','placeholder'=>'L *','id'=>'ptlLengthCourier')) !!}
									@else		
										{!!	Form::text('ptlLength','',array('class'=>'form-control clsLTL4LengthCM form-control1','placeholder'=>'L *','id'=>'ptlLength')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlWidthCourier','',array('class'=>'form-control clsCOURBreadthCM form-control1 border-left-none','placeholder'=>'B *','id'=>'ptlWidthCourier')) !!}
									@else
										{!!	Form::text('ptlWidth','',array('class'=>'form-control clsLTL4BreadthCM form-control1 border-left-none','placeholder'=>'B *','id'=>'ptlWidth')) !!}
									@endif
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
									@if($serviceId == COURIER)
										{!!	Form::text('ptlHeightCourier','',array('class'=>'form-control clsCOURHeightCM form-control1 border-left-none','placeholder'=>'H *','id'=>'ptlHeightCourier')) !!}
									@else
										{!!	Form::text('ptlHeight','',array('class'=>'form-control clsLTL4HeightCM form-control1 border-left-none','placeholder'=>'H *','id'=>'ptlHeight')) !!}
									@endif	
									</div>
								</div>
								<div class="col-md-3 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days unit-days-align">
										@if($serviceId == COURIER)
											{!!	Form::select('ptlCheckVolWeightCourier',($volumeWeightTypes), '' ,['class' =>'selectpicker clsIDPackageWeight','id'=>'ptlCheckVolWeightCourier', 'onChange'=>'volumeWeight(this.value,21)']) !!}
										@else
											{!!	Form::select('ptlCheckVolWeight',($volumeWeightTypes), '' ,['class' =>'selectpicker clsIDPackageWeight','id'=>'ptlCheckVolWeight', 'onChange'=>'volumeWeight(this.value)']) !!}
										@endif	
										</span>
									</div>
								</div>
					</div>
						
					@if($serviceId==COURIER)
					<div class="col-md-6 padding-none">
						<h5 id ='parcel_hide' class="caption-head margin-left-none">
							
						</h5>
						<div class="col-md-7 form-control-fld">	
					@endif

					@if($serviceId!=COURIER)	
					<div class="clearfix"></div>
					<div class="col-md-3 form-control-fld">
					@endif
						<div class="col-md-7 padding-none">
							<div class="input-prepend">																@if($serviceId == COURIER)	
									{!!	Form::text('ptlUnitsWeight','',array('class'=>'form-control clsIDmax_weight_accepted0 clsCOURMaxWeightGms form-control1','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}
								@else			
									{!!	Form::text('ptlUnitsWeight','',array('class'=>'form-control clsIDptlUnitsWeight0 clsLTL4MaxWeightGms form-control1','placeholder'=>'Unit Weight *','id'=>'ptlUnitsWeight')) !!}
								@endif
							</div>
						</div>
						<div class="col-md-5 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days">
									{!!	Form::select('ptlCheckUnitWeight',(['' => 'Weight Unit *'] +$unitsWeightTypes), '' ,['class' =>'selectpicker','id'=>'ptlCheckUnitWeight', 'data-servicetype' => $serviceId]) !!}	
								</span>
							</div>	
						</div>
					</div>

					@if($serviceId==COURIER)	
					<div class="col-md-5 form-control-fld">
					 @else
					 <div class="col-md-3 form-control-fld">
					@endif
					
					@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==RAIL )
						<div class="input-prepend">	
							{!!	Form::text('ptlNopackages','',array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
						</div>
					 @elseif($serviceId==COURIER)
						<div class="input-prepend">	
							{!!	Form::text('ptlNopackages','',array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
						</div>
                                         @else
                                                <div class="input-prepend">	
							{!!	Form::text('ptlNopackages','',array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'ptlNopackages', 'maxlength' => 5)) !!}
						</div>
					 @endif
					</div>

					@if($serviceId==COURIER)
							</div>
							<div class="col-md-3 form-control-fld padding-left-none">
							<div class="input-prepend">
								{!!	Form::text('packeagevalue','',array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'Package Value *','id'=>'packeagevalue', 'maxlength' => 5)) !!}
							</div>
							</div>
							<div class="col-md-3 form-control-fld">
							@endif
							
							@if($serviceId!=COURIER)
							<div class="col-md-6 form-control-fld">
							@endif

					<input type="hidden" name="ptlBuyerSearchCompareId" value="2" id="ptlBuyerSearchCompareId">

						
						{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
				    	{!! Form::submit('ADD +', ['class' => 'btn add-btn','name' => 'add','id' => 'ptlAddMoreItems','onclick'=>"updatepoststatus(1)"]) !!}	
				    	<div id="error-ptl-add-item" class="error "></div>
					</div>

				</div>
				</div>
				</div>
				{!! Form::close() !!}
				

				{!! Form::open(['url' =>'ptl/byersearchresults','id' => 'ptlBuyerSearchSendValues','method'=>'get']) !!}
				<div class="col-md-12 inner-block-bg inner-block-bg1">

                                {!! Form::hidden('is_commercial', 1 , array('id' => 'is_commercial', 'class' =>'is_commercial_check_ptl')) !!}
					<div class="col-md-12 padding-none" id="ptl_addmore_locations">
						<div class="main-inner ptl_add_locations"> 
							

							<!-- Right Section Starts Here -->

							<div class="main-right">
								<h2 class="sub-head"><span class="from-head">From Location: <span class="fromPin"></span></span> - <span class="to-head">To Location: <span class="toPin"></span></span></h2>

								<!-- Table Starts Here -->

								<div class="table-div table-style1 margin-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Courier Purpose</div>
										@endif
										@if($serviceId!=21)
                                                                                <div class="col-md-2 padding-left-none">Package Type</div>
										<div class="col-md-2 padding-left-none">Load Type</div>										
										@endif
										<div id ='volume_display' class="col-md-2 padding-left-none">Volume</div>
										<div class="col-md-2 padding-left-none">Unit Weight</div>
										<div class="col-md-2 padding-left-none">No of Packages</div>
										@if($serviceId==21)
										<div class="col-md-2 padding-left-none">Package Value (Rs)</div>
										@endif
										<div class="col-md-2 padding-left-none"></div>
									</div>

									<input type="hidden" id='ptlBuyerAddMoreItems' value='0'>
				 					<!--div class="table-data ptlRequestRows"></div-->

									<!-- Table Head Ends Here -->

									<div class="table-data  ptlRequestRows">										

									</div>


								</div>	

								

								
							</div>
							<!-- Right Section Ends Here -->
						</div>
					</div>
                                        <div class="col-md-12 form-control-fld text-right">
                                            <input type="button" value="Add Location" class="btn add-btn margin-top" id="addNewLocations">
                                        </div>	
				
					
				</div>
				<div class="">
					<div class="col-md-4 col-md-offset-4">
						{!! Form::submit('Search', ['name' => 'search','class'=>'btn theme-btn btn-block','id' => 'ptlAddBuyerSearch']) !!}
					</div>
				</div>
				
				{!! Form::close() !!}



			</div>	

		</div> <!--div class="home-search gray-bg"-->




			<!-- Include static content block on the search page and footer -->
			@include('partials.searchcontentblock')
			@include('partials.footer')

		
@stop
@endsection