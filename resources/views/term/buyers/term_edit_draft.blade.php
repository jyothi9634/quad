@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->
@if(Session::has('transactionId') && Session::get('transactionId')!='')

	{{--*/ $transactionId = Session::get('transactionId') /*--}}
	{{--*/ Session::get('postsCount') /*--}}
	{{--*/ Session::get('postType') /*--}}

	<script>
		$(document).ready(function(){
			var postCount = {{ Session::get('postsCount') }}
				if (postCount==1) {
				$("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");
				$("#erroralertmodal").modal({
					show: true
				}).one('click','.ok-btn',function (e){
					window.location="/buyerposts";
				});
			} else {
				var postType = {{ Session::get('postType') }}
						if (postType == 2) {
					$("#erroralertmodal .modal-body").html("Term Quote submitted successfully.");
				} else {
					$("#erroralertmodal .modal-body").html(postCount + " Quotes submitted successfully .");
				}
				$("#erroralertmodal").modal({
					show: true
				}).one('click','.ok-btn',function (e){
					window.location="/buyerposts";
				});
			}
		});
	</script>


@endif
{{--*/ $readonly = ($termQuotes->lkp_post_status_id == 2) ? true : false /*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/buyerposts') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
<div class="main">

	<div class="container">
	<span class="pull-right"><a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a></span>
	
<!-- ------------Starts FTL Term starts Here---------- -->
	
<div class="showhide_term" id="showhide_term" > <!-- Add custom div for FTl term srinu -->

	<div class="col-md-12 inner-block-bg single-layout padding-none">
		@if(Session::get('service_id')==ROAD_FTL)
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
					<div class="col-md-12 padding-none inner-form">
					{!! Form::open(['url' =>'#','id' => 'ftl_term_insert' , 'autocomplete'=>'off']) !!}               
	                    	<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>									
									{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)) , ['id' => 'term_dispatch_date','class' => 'form-control calendar', 'placeholder' => 'Valid From *','readonly'=>"readonly",($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)) , ['id' => 'term_delivery_date','class' => 'form-control calendar', 'placeholder' => 'Valid To *','readonly'=>"readonly", ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
	                        
						<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control clsFTLTFromLocation', 'placeholder' => 'From Location *', ($readonly == true) ? 'disabled' : '']) !!}
								   	{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control clsFTLTtoLocation','placeholder' => 'To Location *', ($readonly == true) ? 'disabled' : '']) !!}
									{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
								</div>
							</div>
	                        <div class="clearfix"></div>
	                        
	                        <div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!!	Form::select('term_load_type',(['' => 'Select Load Type *'] +$load_type), '',['class' =>'selectpicker form_control','id'=>'term_load_type','onChange'=>'return getTermCapacity()', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
	                        <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									{!!	Form::text('term_quantity','',array('class'=>'form-control clsFTLTQuantity','placeholder'=>'Quantity *','id'=>'term_quantity', ($readonly == true) ? 'disabled' : '')) !!}
									<span class="add-on unit1">
										{!!	Form::text('term_capacity','',array('class'=>'form-control clsFTLTQuantity','id'=>'term_capacity','placeholder'=>'Capacity','readonly', ($readonly == true) ? 'disabled' : '')) !!}
									</span>
									</div>
							</div>
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('term_vehicle_type',(['' => 'Select Vehicle Type *'] +$vehicle_type), '' ,['class' =>'selectpicker form_control','id'=>'term_vehicle_type', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div> 
							<!--div class="col-md-3 form-control-fld term_buyer_add">
								  	<input type="submit" value="Add +" class="btn add-btn" id="term_add_buyer_more">
									<div id="error-add-item" class="error "></div>
							</div-->
							<div class="col-md-3 form-control-fld term_buyer_update" style="display:none;">								  	
								  	<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more">
									<div id="error-update-item" class="error "></div>
							</div>
						 <input type="hidden" name="buyer_item_id" id="buyer_item_id" value="">	
					{!! Form::close() !!}
					</div>

				</div>                
                                        @if($readonly == 1)
					{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                        @else
                                        {!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                        @endif
					{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
					{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
					{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
					{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
					{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}
					
					
					<div class="col-md-12 padding-none">
						<div class="main-inner"> 							
							<!-- Right Section Starts Here -->
							<div class="main-right">
								<!-- Table Starts Here -->
								<div class="table-div table-style1 padding-none">
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
										<div class="col-md-2 padding-left-none">From</div>
										<div class="col-md-2 padding-left-none">To</div>
										<div class="col-md-3 padding-left-none">Load Type</div>
										<div class="col-md-2 padding-left-none">Vehicle Type</div>
										<div class="col-md-2 padding-left-none">Quantity</div>
										<div class="col-md-1 padding-left-none"></div>
									</div>
									<!-- Table Head Ends Here -->
									<div class="table-data term_request_rows">
										@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
											{{--*/ $slNumber = '1' /*--}}
											@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
												<div class="table-row col-md-12 col-sm-12 col-xs-12 inner-block-bg padding-left-none padding-right-none table-row inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
													<div class="col-md-2 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
													<div class="col-md-3 col-sm-2 col-xs-3 padding-none load_type_text">{!! $getBuyerTermQuotesdata->load_type !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none vehicle_type_text">{!! $getBuyerTermQuotesdata->vehicle_type !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none quantity_type_text">{!! $getBuyerTermQuotesdata->quantity !!}</div>
													<div class="col-md-1 col-sm-2 col-xs-3 padding-none">
													@if($readonly == false)
														<a href="javascript:void(0)" onclick="updatetermpostlineitem({{$getBuyerTermQuotesdata->id}});" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
													@endif
													</div>

													<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->quantity!!}" name="quantity[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->units !!}" name="capacity[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_load_type_id !!}" name="load_type[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_vehicle_type_id !!}" name="vechile_type[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">

													<div class="clearfix"></div>
												</div>
												{{--*/ $slNumber++ /*--}}
											@endforeach
										@endif
                                
										<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
										<div class="table-data term_request_rows_ltl"></div>
									</div>
									<div class="clearfix"></div>
									<input type="hidden" id='next_term_add_buyer_more_id' value='0'>
									<div class="table-data term_request_rows inner-block-bg"></div>					
									<div class="clearfix"></div>					
								</div>
								<!-- Table Starts Here -->
							</div>
							<!-- Right Section Ends Here -->
						</div>
					</div>
			@elseif(Session::get('service_id')==ROAD_PTL || Session::get('service_id')==RAIL || Session::get('service_id')==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
				<div class="col-md-12 inner-block-bg inner-block-bg1 padding-bottom-none border-bottom-none margin-none">
					<div class="col-md-12 padding-none inner-form margin-bottom-none">

						{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}
						{!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!}

						<div class="col-md-12 padding-none inner-form margin-bottom-none">

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)), ['id' => 'term_dispatch_date','class' => 'form-control calendar', 'placeholder' => 'Valid From *', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
								@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN)
									{{--*/ $pickupchecked = ($termQuotes->is_door_pickup == 1) ? true : false /*--}}
									{!! Form::checkbox('term_door_pickup', 1, $pickupchecked, ['class' => '' , 'id'=>'term_door_pickup', ($readonly == true) ? 'disabled' : '']) !!}
									<span class="lbl padding-8">Door Pickup</span>
								@endif
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)), ['id' => 'term_delivery_date','class' => 'form-control calendar', 'placeholder' => 'Valid To *', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
								@if($serviceId!=AIR_INTERNATIONAL &&  $serviceId!=OCEAN)
									{{--*/ $deliverychecked = ($termQuotes->is_door_delivery == 1) ? true : false /*--}}
									{!! Form::checkbox('term_door_delivery', 1, $deliverychecked, ['class' => '', 'id'=>'term_door_delivery', ($readonly == true) ? 'disabled' : '']) !!}
									<span class="lbl padding-8">Door Delivery</span>
								@endif
							</div>
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
													{!! Form::text('from_airport_term', '', ['id' => 'from_airport_term','class' => 'form-control', 'placeholder' => 'From Airport*', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('from_airport_term_id', '', array('id' => 'from_airport_term_id')) !!}
												@elseif($serviceId == OCEAN)
													{!! Form::text('from_airport_term', '', ['id' => 'from_airport_term','class' => 'form-control', 'placeholder' => 'From Ocean*', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('from_airport_term_id', '', array('id' => 'from_airport_term_id')) !!}
												@else
													{!! Form::text('term_from_location_pincode', '', ['id' => 'term_from_location_pincode', 'class'=>'form-control', 'placeholder' => 'From Pincode *', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('term_from_location_pincode_id', '', array('id' => 'term_from_location_pincode_id')) !!}
												@endif
											</div>
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												@if($serviceId == AIR_INTERNATIONAL)
													{!! Form::text('to_airport_term','', ['id' => 'to_airport_term','class' => 'form-control', 'placeholder' => 'To Airport*', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('to_airport_term_id', '', array('id' => 'to_airport_term_id')) !!}
												@elseif($serviceId == OCEAN)
													{!! Form::text('to_airport_term','', ['id' => 'to_airport_term','class' => 'form-control', 'placeholder' => 'To Ocean*', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('to_airport_term_id', '', array('id' => 'to_airport_term_id')) !!}
												@else
													{!! Form::text('term_to_location_pincode', '', ['id' => 'term_to_location_pincode', 'class'=>'form-control','placeholder' => 'To Pincode *', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('term_to_location_pincode_id', '', array('id' => 'term_to_location_pincode_id')) !!}
												@endif
											</div>
										</div>
									</div>
									@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
												{!!	Form::select('term_shipment_type',(['' => 'Shipment Type *'] +$shipmentTypes), '' ,['class' =>'selectpicker','id'=>'term_shipment_type', ($readonly == true) ? 'disabled' : '']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!! Form::text('term_iecode', '',  ['class' => 'form-control form-control1' , 'id'=>'term_iecode','placeholder' => 'IE Code', ($readonly == true) ? 'disabled' : '']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
												{!! Form::select('term_sender_identify',(['' => 'Sender Identity *'] +$senderIdentity), '' ,['class' =>'selectpicker','id'=>'term_sender_identify', ($readonly == true) ? 'disabled' : '']) !!}
											</div>
										</div>

										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!! Form::text('term_product_mode', '',  ['class' => 'form-control form-control1' , 'id'=>'term_product_mode','placeholder' => 'Product Made', ($readonly == true) ? 'disabled' : '']) !!}
											</div>
										</div>
										<div class="clearfix"></div>
									@endif
								</div>

								<div class="col-md-6 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
												<span class="add-on"><i class="fa fa-archive"></i></span>
											{!!	Form::select('term_load_type',(['' => 'Select Load Type *'] +$loadTypes), '',['class' =>'selectpicker','id'=>'term_load_type','onChange'=>'return getTermCapacity()', ($readonly == true) ? 'disabled' : '']) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
												<span class="add-on"><i class="fa fa-archive"></i></span>
											{!!	Form::select('term_package_type',(['' => 'Packaging Type *'] +$packageTypes), '' ,['class' =>'selectpicker','id'=>'term_package_type', ($readonly == true) ? 'disabled' : '']) !!}
										</div>
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										{!!	Form::text('term_noof_packages','',array('class'=>'form-control form-control1 clsAirInitNoOfPackages','placeholder'=>'No of Packages *','id'=>'term_noof_packages', ($readonly == true) ? 'disabled' : '')) !!}
									</div>
								</div>
								@if($serviceId==AIR_DOMESTIC ||  $serviceId==AIR_INTERNATIONAL)
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											{!!	Form::text('term_volume','',array('class'=>'form-control form-control1','placeholder'=>'Volume *','id'=>'term_volume', ($readonly == true) ? 'disabled' : '')) !!}
											{!!	Form::hidden('term_units','CCM',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
											<span class="add-on unit">CCM</span>
										</div>
									</div>
								@elseif($serviceId==OCEAN)
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											{!!	Form::text('term_volume','',array('class'=>'form-control form-control1','placeholder'=>'Volume *','id'=>'term_volume', ($readonly == true) ? 'disabled' : '')) !!}
											{!!	Form::hidden('term_units','CBM',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
											<span class="add-on unit">CBM</span>
										</div>
									</div>
								@else
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											{!!	Form::text('term_volume','',array('class'=>'form-control form-control1 clsRailVolumepCFT','placeholder'=>'Volume *','id'=>'term_volume', ($readonly == true) ? 'disabled' : '')) !!}
											{!!	Form::hidden('term_units','CFT',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
											<span class="add-on unit">CFT</span>
										</div>
									</div>
								@endif
								<div class="col-md-3 form-control-fld term_buyer_update" style="display:none;">
									<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_ptl">
									<div id="error-update-item" class="error "></div>
								</div>
								<div class="clearfix"></div>

							</div>
						</div>
						<div class="col-md-12 form-control-fld text-right margin-none margin-top">
							{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
							<div id="error-add-item-term" class="error "></div>
						</div>

					{!! Form::close() !!}



					</div>
				</div>
                                @if($readonly == 1)
				{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @else
                                {!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @endif
				{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
				{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
				{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
				@if(Session::get('service_id')==ROAD_PTL || Session::get('service_id')==RAIL || Session::get('service_id')==AIR_DOMESTIC)
					{!!	Form::hidden('is_door_pickup',($pickupchecked == true) ? 1: 0,array('class'=>'','id'=>'is_door_pickup'))!!}
					{!!	Form::hidden('is_door_delivery',($deliverychecked == true) ? 1: 0,array('class'=>'','id'=>'is_door_delivery'))!!}
				@endif
				{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
				{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}
				{!! Form::hidden('current_service_id', Session::get('service_id'), array('id' => 'current_service_id')) !!}
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
				<div class="col-md-12 padding-none">
					<div class="main-inner">
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div table-style1 padding-none margin-none">
								<!-- Table Head Starts Here -->
								<div class="table-heading inner-block-bg">
									<div class="col-md-2 padding-left-none">From</div>
									<div class="col-md-2 padding-left-none">To</div>
									<div class="col-md-2 padding-left-none">Load Type</div>
									<div class="col-md-1 padding-left-none">Package Type</div>
									<div class="col-md-1 padding-left-none">Volume</div>
									<div class="col-md-1 padding-left-none">Unit Weight</div>
									<div class="col-md-2 padding-left-none">No of Packages</div>
									<div class="col-md-1 padding-left-none"></div>
								</div>
								<!-- Table Head Ends Here -->
								<div class="table-data term_request_rows">
									@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
										{{--*/ $slNumber = '1' /*--}}
										@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
											<div class="table-row col-md-12 col-sm-12 col-xs-12 inner-block-bg padding-left-none padding-right-none table-row inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
												<div class="col-md-2 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
												<div class="col-md-2 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
												<div class="col-md-2 col-sm-1 col-xs-3 padding-none load_type_text">{!! $getBuyerTermQuotesdata->load_type !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none package_type_text">{!! $getBuyerTermQuotesdata->packaging_type !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none volume_text">{!! $getBuyerTermQuotesdata->volume !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none units_text">{!! $getBuyerTermQuotesdata->units !!}</div>
												<div class="col-md-2 col-sm-1 col-xs-3 padding-none number_packages_text">{!! $getBuyerTermQuotesdata->number_packages !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none">
                                                                                                    @if($readonly == false)
													<a href="javascript:void(0)" onclick="updateptltermpostlineitem({{$getBuyerTermQuotesdata->id}});" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                                                                        @endif
												</div>

												<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_load_type_id !!}" name="load_type[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_packaging_type_id !!}" name="package_type[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->volume!!}" name="volume[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->units !!}" name="capacity[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->number_packages !!}" name="number_packages[]">

												@if($serviceId==AIR_INTERNATIONAL ||  $serviceId==OCEAN)
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->ie_code !!}" name="ie_code[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->product_made !!}" name="product_made[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_air_ocean_shipment_type_id !!}" name="lkp_air_ocean_shipment_type_id[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_air_ocean_sender_identity_id !!}" name="lkp_air_ocean_sender_identity_id[]">
												@endif

												<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">

												<div class="clearfix"></div>
											</div>
											{{--*/ $slNumber++ /*--}}
										@endforeach
									@endif

									<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
									<div class="table-data term_request_rows_ltl"></div>
								</div>
								<div class="clearfix"></div>
								<input type="hidden" id='next_term_add_buyer_more_id' value='0'>
								<div class="table-data term_request_rows inner-block-bg"></div>
								<div class="clearfix"></div>
							</div>
							<!-- Table Starts Here -->
						</div>
						<!-- Right Section Ends Here -->
					</div>
				</div>
			</div>
			@elseif(Session::get('service_id')==COURIER)
			<div class="col-md-12 inner-block-bg inner-block-bg1 padding-bottom-none border-bottom-none margin-none">
					<div class="col-md-12 padding-none inner-form margin-bottom-none">

						{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}
						{!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!}

						<div class="col-md-12 padding-none inner-form margin-bottom-none">

						
						@if(isset($termQuotes->lkp_courier_delivery_type_id) && $termQuotes->lkp_courier_delivery_type_id == 1)
						{{--*/ $domestic_dom = true /*--}}
						@else
						{{--*/ $domestic_dom = false /*--}}
						@endif
					
					
						@if(isset($termQuotes->lkp_courier_delivery_type_id) && $termQuotes->lkp_courier_delivery_type_id == 2)
						{{--*/ $domestic_int = true /*--}}
						@else
						{{--*/ $domestic_int = false /*--}}
						@endif
						
						@if(isset($termQuotes->lkp_courier_type_id) && $termQuotes->lkp_courier_type_id == 1)
						{{--*/ $doc_courier = true /*--}}
						@else
						{{--*/ $doc_courier = false /*--}}
						@endif
					
					
						@if(isset($termQuotes->lkp_courier_type_id) && $termQuotes->lkp_courier_type_id == 2)
						{{--*/ $parcel_courier = true /*--}}
						@else
						{{--*/ $parcel_courier = false /*--}}
						@endif
						
						
							<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'Domestic', $domestic_dom, ['id' => 'term_domestic', 'disabled']) !!} <label for="term_domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('term_post_delivery_type', 'International', $domestic_int, ['id' => 'term_international', 'disabled']) !!} <label for="term_international"><span></span>International</label>
											{!! Form::hidden('term_post_or_delivery_type', $termQuotes->lkp_courier_delivery_type_id, array('id' => 'term_post_delivery_type')) !!}
											</div>
										</div>
									</div>
							<div class="clearfix"></div>		
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)), ['id' => 'term_dispatch_date','class' => 'form-control calendar', 'placeholder' => 'Valid From *', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)), ['id' => 'term_delivery_date','class' => 'form-control calendar', 'placeholder' => 'Valid To *', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
						

						<div class="col-md-3 padding-none">
								<div class="radio-block padding-top-8">
									<div class="radio_inline">
										{!! Form::radio('term_courier_types', 'Documents', $doc_courier, ['id' => 'term_documents', 'disabled']) !!} <label for="term_documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
										{!! Form::radio('term_courier_types', 'Parcel', $parcel_courier, ['id' => 'term_parcel', 'disabled']) !!} <label for="term_parcel"><span></span>Parcel</label>
										{!! Form::hidden('term_courier_or_types', 1, array('id' => 'term_courier_types')) !!}
									</div>
								</div>
					</div>
						</div>
						<div class="col-md-12 form-control-fld form-control-fld1 margin-none">
							<div class="col-md-12 inner-block-bg inner-block-bg1">
								<h2 class="sub-head margin-bottom">Add Item Details</h2>

								<div class="col-md-6 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('term_from_location_pincode', '', ['id' => 'term_from_location_pincode', 'class'=>'form-control', 'placeholder' => 'From Pincode *', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('term_from_location_pincode_id', '', array('id' => 'term_from_location_pincode_id')) !!}
											</div>
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('term_to_location_pincode', '', ['id' => 'term_to_location_pincode', 'class'=>'form-control','placeholder' => 'To Pincode *', ($readonly == true) ? 'disabled' : '']) !!}
													{!! Form::hidden('term_to_location_pincode_id', '', array('id' => 'term_to_location_pincode_id')) !!}
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										{!!	Form::text('term_noof_packages','',array('class'=>'form-control form-control1 numericvalidation','placeholder'=>'No of Packages *','id'=>'term_noof_packages', ($readonly == true) ? 'disabled' : '')) !!}
									</div>
								</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											{!!	Form::text('term_volume','',array('class'=>'form-control form-control1','placeholder'=>'Volume *','id'=>'term_volume', ($readonly == true) ? 'disabled' : '')) !!}
											{!!	Form::hidden('term_units','CFT',array('class'=>'form-control','placeholder'=>'Units *','id'=>'term_units')) !!}
											<span class="add-on unit">CFT</span>
										</div>
									</div>
								<div class="col-md-3 form-control-fld term_buyer_update" style="display:none;">
									<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_ptl">
									<div id="error-update-item" class="error "></div>
								</div>
								<div class="clearfix"></div>

							</div>
						</div>
						<div class="col-md-12 form-control-fld text-right margin-none margin-top">
							{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
							<div id="error-add-item-term" class="error "></div>
						</div>

					{!! Form::close() !!}



					</div>
				</div>
                                @if($readonly == 1)
				{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @else
                                {!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @endif
				{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
				{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
				{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
				{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
				{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}
				{!! Form::hidden('current_service_id', Session::get('service_id'), array('id' => 'current_service_id')) !!}
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
				<div class="col-md-12 padding-none">
					<div class="main-inner">
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div table-style1 padding-none margin-none">
								<!-- Table Head Starts Here -->
								<div class="table-heading inner-block-bg">
									<div class="col-md-2 padding-left-none">From</div>
									<div class="col-md-2 padding-left-none">To</div>
									<div class="col-md-1 padding-left-none">Volume</div>
									<div class="col-md-1 padding-left-none">Unit Weight</div>
									<div class="col-md-2 padding-left-none">No of Packages</div>
									<div class="col-md-1 padding-left-none"></div>
								</div>
								<!-- Table Head Ends Here -->
								<div class="table-data term_request_rows">
									@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
										{{--*/ $slNumber = '1' /*--}}
										@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
											<div class="table-row col-md-12 col-sm-12 col-xs-12 inner-block-bg padding-left-none padding-right-none table-row inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
												<div class="col-md-2 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
												<div class="col-md-2 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none volume_text">{!! $getBuyerTermQuotesdata->volume !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none units_text">CFT</div>
												<div class="col-md-2 col-sm-1 col-xs-3 padding-none number_packages_text">{!! $getBuyerTermQuotesdata->number_packages !!}</div>
												<div class="col-md-1 col-sm-1 col-xs-3 padding-none">
                                                                                                    @if($readonly == false)
													<a href="javascript:void(0)" onclick="updateptltermpostlineitem({{$getBuyerTermQuotesdata->id}});" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                                                                        @endif
												</div>

												<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->volume!!}" name="volume[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->units !!}" name="capacity[]">
												<input type="hidden" value="{!! $getBuyerTermQuotesdata->number_packages !!}" name="number_packages[]">

												<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">

												<div class="clearfix"></div>
											</div>
											{{--*/ $slNumber++ /*--}}
										@endforeach
									@endif

									<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
									<div class="table-data term_request_rows_ltl"></div>
								</div>
								<div class="clearfix"></div>
								<input type="hidden" id='next_term_add_buyer_more_id' value='0'>
								<div class="table-data term_request_rows inner-block-bg"></div>
								<div class="clearfix"></div>
							</div>
							<!-- Table Starts Here -->
						</div>
						<!-- Right Section Ends Here -->
					</div>
				</div>
			</div>

			@elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
					<div class="col-md-12 padding-none inner-form">
						{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}
						<div class="col-md-12 padding-none inner-form margin-bottom-none">

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)), ['id' => 'term_dispatch_date','class' => 'form-control calendar', 'placeholder' => 'Valid From *','readonly'=>"readonly", ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)), ['id' => 'term_delivery_date','class' => 'form-control calendar', 'placeholder' => 'Valid To *','readonly'=>"readonly", ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
						</div>


						<div class="col-md-12 form-control-fld">
							<div class="col-md-12 padding-none inner-block-bg padding-10 margin-top">
								<h2 class="sub-head">Add Item Details</h2>

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control', 'placeholder' => 'From Location *', ($readonly == true) ? 'disabled' : '']) !!}
										{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
									</div>

								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control','placeholder' => 'To Location *', ($readonly == true) ? 'disabled' : '']) !!}
										{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
									</div>

								</div>
								@if($termQuotes->lkp_post_ratecard_type == 1)
									<div class="col-md-3 form-control-fld">
										<div class="col-md-8 padding-none">
											<div class="input-prepend">
												<input type="text" placeholder="Avg Volume/Shipment *" class="form-control form-control1 clsRelocationAvgVolShip" name="relocation_term_volume" id="relocation_term_volume" <?php if($readonly==true) { ?> disabled='true' <?php } ?>>
											</div>
										</div>
										<div class="col-md-4 padding-none">
											<div class="input-prepend">
											<span class="add-on unit-days manage" >
												<div class="normal-select" name="relocation_term_weighttype" id="relocation_term_weighttype">
													<select class="selectpicker bs-select-hidden" <?php if($readonly==true) { ?> disabled='true' <?php } ?>>
														<option>CFT</option>
														<option>CCM</option>
													</select>
												</div>
											</span>
											</div>
										</div>
									</div>

									<div class="col-md-2 form-control-fld">
										<input type="text" class="form-control form-control1" placeholder="No of Shipments *" name="relocation_term_noofshipments" id="relocation_term_noofshipments" <?php if($readonly==true) { ?> disabled='true' <?php } ?>/>
									</div>
									<div class="col-md-1 form-control-fld term_buyer_update" style="display:none;">
										<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_relocation" term_type="house">
										<div id="error-update-item" class="error "></div>
									</div>
								@else
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-truck"></i></span>
											{!!	Form::select('term_vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), '' ,['class' =>'selectpicker','id'=>'term_vehicle_category','onchange'=>'return getVehicleTypesTerm()']) !!}
										</div>
									</div>

									<div class="col-md-3 form-control-fld vehicle_type_car_term">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-truck"></i></span>
											{!!	Form::select('term_vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), '' ,['class' =>'selectpicker','id'=>'term_vehicle_category_type']) !!}
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-2 form-control-fld padding-top">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-truck"></i></span>
											{!! Form::text('term_vehicle_model', '', ['id' => 'term_vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*']) !!}
										</div>
									</div>
									<div class="col-md-2 form-control-fld padding-top">
										<input type="text" class="form-control form-control1" placeholder="No of Vehicles *" name="relocation_term_nooftrips" id="relocation_term_nooftrips" />
									</div>
									<div class="col-md-1 form-control-fld term_buyer_update padding-top" style="display:none;">
										<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_relocation" term_type="vehicle">
										<div id="error-update-item" class="error "></div>
									</div>
								@endif


							</div>
						</div>
						{!! Form::close() !!}
					</div>

				</div>
                                @if($readonly == 1)
				{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @else
                                {!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
                                @endif
				{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
				{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
				{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
				{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
				{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}


				<div class="col-md-12 padding-none">
					<div class="main-inner">
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div table-style1 padding-none">
								<!-- Table Head Starts Here -->
								<div class="table-heading inner-block-bg">
									@if($termQuotes->lkp_post_ratecard_type == 1)
										<div class="col-md-3 padding-left-none">From<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">To<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">Avg Volume/Shipment<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">No of Shipments<i class="fa fa-caret-down"></i></div>
									@else
										<div class="col-md-2 padding-left-none">From<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">To<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Category<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Category Type<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Model<i class="fa fa-caret-down"></i></div>
										<div class="col-md-1 padding-left-none">No of Trips<i class="fa fa-caret-down"></i></div>
									@endif

									<div class="col-md-1 padding-left-none"></div>
								</div>
								<!-- Table Head Ends Here -->
								<div class="table-data term_request_rows">
									@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
										{{--*/ $slNumber = '1' /*--}}
										@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
											<div class="table-row col-md-12 col-sm-12 col-xs-12 inner-block-bg padding-left-none padding-right-none table-row inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
												@if($termQuotes->lkp_post_ratecard_type == 1)
													<div class="col-md-3 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
													<div class="col-md-3 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
													<div class="col-md-3 col-sm-2 col-xs-3 padding-none volume">{!! $getBuyerTermQuotesdata->volume !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none number_packages">{!! $getBuyerTermQuotesdata->number_packages  !!}</div>
													<div class="col-md-1 col-sm-2 col-xs-3 padding-none">
                                                                                                            @if($readonly == false)
														<a href="javascript:void(0)" onclick="updaterelcoationtermpostlineitem({{$getBuyerTermQuotesdata->id}},'house');" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                                                                                @endif
													</div>

													<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->volume!!}" name="volume[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->number_packages !!}" name="number_packages[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">
												@else
													<div class="col-md-2 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none vehicle_category">{!! $common::getVehicleCategoryById($getBuyerTermQuotesdata->lkp_vehicle_category_id) !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none vehicle_category_type">{!! $common::getVehicleCategorytypeById($getBuyerTermQuotesdata->lkp_vehicle_category_type_id)  !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none vehicle_model">{!! $getBuyerTermQuotesdata->vehicle_model  !!}</div>
													<div class="col-md-1 col-sm-1 col-xs-3 padding-none no_of_vehicles">{!! $getBuyerTermQuotesdata->no_of_vehicles  !!}</div>
													<div class="col-md-1 col-sm-2 col-xs-3 padding-none">
                                                                                                            @if($readonly == false)
														<a href="javascript:void(0)" onclick="updaterelcoationtermpostlineitem({{$getBuyerTermQuotesdata->id}},'vehicle');" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                                                                                @endif
													</div>

													<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_vehicle_category_id!!}" name="lkp_vehicle_category_id[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->lkp_vehicle_category_type_id !!}" name="lkp_vehicle_category_type_id[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->vehicle_model !!}" name="vehicle_model[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->no_of_vehicles !!}" name="no_of_vehicles[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">
												@endif
												<div class="clearfix"></div>
											</div>
											{{--*/ $slNumber++ /*--}}
										@endforeach
									@endif

									<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
									<div class="table-data term_request_rows_ltl"></div>
								</div>
								<div class="clearfix"></div>
								<input type="hidden" id='next_term_add_buyer_more_id' value='0'>
								<div class="table-data term_request_rows inner-block-bg"></div>
								<div class="clearfix"></div>
							</div>
							<!-- Table Starts Here -->
						</div>
						<!-- Right Section Ends Here -->
					</div>
				</div>
			@elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
					<div class="col-md-12 padding-none inner-form">
						{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}
						<div class="col-md-12 padding-none inner-form margin-bottom-none">

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)), ['id' => 'term_dispatch_date','class' => 'form-control calendar', 'placeholder' => 'Valid From *','readonly'=>"readonly", ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)), ['id' => 'term_delivery_date','class' => 'form-control calendar', 'placeholder' => 'Valid To *','readonly'=>"readonly", ($readonly == true) ? 'disabled' : '']) !!}
								</div>
							</div>
						</div>


						<div class="col-md-12 form-control-fld padding-none">
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control', 'placeholder' => 'From Location *', ($readonly == true) ? 'disabled' : '']) !!}
									{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
								</div>

							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control','placeholder' => 'To Location *', ($readonly == true) ? 'disabled' : '']) !!}
									{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
								</div>

							</div>

							<div class="col-md-2 form-control-fld">
								{!! Form::text('term_number_loads', '', ['id' => 'term_number_loads','class' => 'form-control form-control1 clsRIATNoofMoves', 'placeholder' => 'Number of Moves*']) !!}
							</div>
							<div class="col-md-2 form-control-fld">
								<input type="text" class="form-control form-control1 clsRIATAvgKgPerMove" placeholder="Average KG per Move*" name="relocation_term_avg_kg_per_move" id="relocation_term_avg_kg_per_move" />
							</div>
							<div class="col-md-1 form-control-fld term_buyer_update" style="display:none;">
								<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_relocationint" term_type="vehicle">
								<div id="error-update-item" class="error "></div>
							</div>
						</div>
						{!! Form::close() !!}
					</div>

				</div>
				@if($readonly == 1)
					{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
				@else
					{!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
				@endif
				{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
				{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
				{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
				{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
				{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}


				<div class="col-md-12 padding-none">
					<div class="main-inner">
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div table-style1 padding-none">
								<!-- Table Head Starts Here -->
								<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none">From<i class="fa fa-caret-down"></i></div>
									<div class="col-md-3 padding-left-none">To<i class="fa fa-caret-down"></i></div>
									<div class="col-md-3 padding-left-none">No of Moves<i class="fa fa-caret-down"></i></div>
									<div class="col-md-2 padding-left-none">Average KG/Move<i class="fa fa-caret-down"></i></div>
									<div class="col-md-1 padding-left-none"></div>
								</div>
								<!-- Table Head Ends Here -->
								<div class="table-data term_request_rows">
									@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
										{{--*/ $slNumber = '1' /*--}}
										@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
											<div class="table-row col-md-12 col-sm-12 col-xs-12 inner-block-bg padding-left-none padding-right-none table-row inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
													<div class="col-md-3 col-sm-2 col-xs-3 left-none from_location_text">{!! $getBuyerTermQuotesdata->from_locationcity !!}</div>
													<div class="col-md-3 col-sm-2 col-xs-3 padding-none to_location_text">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
													<div class="col-md-3 col-sm-2 col-xs-3 padding-none volume">{!! $getBuyerTermQuotesdata->number_loads !!}</div>
													<div class="col-md-2 col-sm-2 col-xs-3 padding-none number_packages">{!! $getBuyerTermQuotesdata->avg_kg_per_move  !!}</div>
													<div class="col-md-1 col-sm-2 col-xs-3 padding-none">
														@if($readonly == false)
															<a href="javascript:void(0)" onclick="updaterelcoationInttermpostlineitem({{$getBuyerTermQuotesdata->id}},'house');" row_id="{{$getBuyerTermQuotesdata->id}}" style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
														@endif
													</div>

													<input type="hidden" value="{!! $getBuyerTermQuotesdata->from_location_id !!}" name="from_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->to_location_id !!}" name="to_location[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->number_loads!!}" name="number_loads[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->avg_kg_per_move !!}" name="avg_kg_per_move[]">
													<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">
												<div class="clearfix"></div>
											</div>
											{{--*/ $slNumber++ /*--}}
										@endforeach
									@endif

									<input type="hidden" id='next_term_add_buyer_more_id_term' value='0'>
									<div class="table-data term_request_rows_ltl"></div>
								</div>
								<div class="clearfix"></div>
								<input type="hidden" id='next_term_add_buyer_more_id' value='0'>
								<div class="table-data term_request_rows inner-block-bg"></div>
								<div class="clearfix"></div>
							</div>
							<!-- Table Starts Here -->
						</div>
						<!-- Right Section Ends Here -->
					</div>
				</div>


		@elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none">
				<div class="col-md-12 padding-none inner-form">
					{!! Form::open(['url' =>'#','id' => 'ftl_term_insert', 'autocomplete'=>'off']) !!}

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('term_dispatch_date', date("d/m/Y", strtotime($termQuotes->from_date)), ['id' => 'term_dispatch_date','class' => 'calender form-control calendar  from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('term_delivery_date', date("d/m/Y", strtotime($termQuotes->to_date)), ['id' => 'term_delivery_date','class' => 'form-control calendar  to-date-control', 'placeholder' => 'Valid To *','readonly'=>"readonly"]) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', $getBuyerTermQuotesdata['0']->from_locationcity, ['id' => 'from_location', 'class'=>'form-control', 'placeholder' => 'Location *']) !!}
							{!! Form::hidden('from_location_id', $getBuyerTermQuotesdata['0']->from_location_id, array('id' => 'from_location_id')) !!}
						</div>

					</div>


					{!! Form::close() !!}
				</div>

			</div>
			@if($readonly == 1)
				{!! Form::open(['url' =>'updatetermseller/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
			@else
				{!! Form::open(['url' =>'updatbuyertermpost/'.$termQuotes->id,'id' => 'term_buyer_quote', 'files'=>true]) !!}
			@endif
			{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
			{!! Form::hidden('dispatch_date', $termQuotes->from_date, array('id' => 'dispatch_date')) !!}
			{!! Form::hidden('delivery_date', $termQuotes->to_date, array('id' => 'delivery_date')) !!}
			{!! Form::hidden('poststatus', $termQuotes->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
			{!! Form::hidden('current_row_id', 0, array('id' => 'current_row_id')) !!}

			{{--*/ $term='term_' /*--}}
			<div class="col-md-12 inner-block-bg inner-block-bg1">

				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-cog"></i></span>
						{!!	Form::select($term.'relgm_service_type',$lkp_relgm_services, '' ,['class' =>'selectpicker','id'=>$term.'relgm_service_type','onchange'=>'return getServiceTypeMeasurementUnit()']) !!}
					</div>
				</div>

				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">

						<input class="form-control form-control1 clsGMSNoOfDays" id="{{$term}}measurement" name="{{$term}}measurement" type="text" >
						<span class="add-on unit1 manage"><input type="text" name="{{$term}}measurement_unit" readonly="readonly" placeholder="Rs / Day" id="{{$term}}measurement_unit" class="form-control form-control1 valid" value="" ></span>
					</div>
					<label for="{{$term}}measurement" id="err_{{$term}}measurement" class="error" style="display: none;"></label>
				</div>

				<div class="col-md-3 form-control-fld">
					<input type="hidden" name ='{{$term}}service_slab_hidden_value' id='{{$term}}service_slab_hidden_value' value='0'>
					<div class="col-md-1 form-control-fld term_buyer_update" style="display:none;">
						<input type="button" value="Update" class="btn add-btn" id="term_update_buyer_more_relocationglobal">
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="table-div table-style1 ">
					<!-- Table Head Starts Here -->
					<div class="table-heading inner-block-bg">
						<div class="col-md-5 padding-left-none">Service</div>
						<div class="col-md-5 padding-left-none">Numbers</div>
						<div class="col-md-2 padding-left-none"></div>
					</div>
					<!-- Table Head Ends Here -->
					<div class="{{$term}}servicetable table-data">
						<!-- Table Row Starts Here -->
						<div class="{{$term}}servicedata table-data term_request_rows">
							@if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))
								{{--*/ $slNumber = '1' /*--}}
								@foreach($getBuyerTermQuotesdata as $getBuyerTermQuotesdata)
									<div class="table-row inner-block-bg col-md-12 col-sm-12 col-xs-12 inner-block-bg request_row_{!! $getBuyerTermQuotesdata->id !!}" id="single_post_item_{!! $getBuyerTermQuotesdata->id !!}">
										<div class="col-md-5 form-control-fld padding-left-none  padding-top-7">{{$getBuyerTermQuotesdata->service_type}}</div>
										<div class="col-md-5 padding-left-none gm_measurement">
											@if($getBuyerTermQuotesdata->lkp_gm_service_id!=7)
												{{$getBuyerTermQuotesdata->measurement}} {{$getBuyerTermQuotesdata->measurement_units}}
											@endif
										</div>
										<div class="col-md-2 form-control-fld padding-left-none  padding-top-7">
											@if($getBuyerTermQuotesdata->lkp_gm_service_id!=7)
												<a href="javascript:void(0)" onclick="updaterelcoationGlobaltermpostlineitem({{$getBuyerTermQuotesdata->id}})"  class="edit-service" row_id="1" data-pop="{{$getBuyerTermQuotesdata->lkp_gm_service_id}}|{{$getBuyerTermQuotesdata->measurement}}|{{$getBuyerTermQuotesdata->measurement_units}}"><i class="fa fa-edit red" title="Edit"></i></a>
											@endif
										</div>
										<input type="hidden" name="from_location[]" class="from_location_hidden" value="{{$getBuyerTermQuotesdata->from_location_id}}">
										<input type="hidden" name="service_ids[]" class="service_ids" value="{{$getBuyerTermQuotesdata->lkp_gm_service_id}}">
										<input type="hidden" name="measurements[]" value="{{$getBuyerTermQuotesdata->measurement}}">
										<input type="hidden" name="measurement_units[]" value="{{$getBuyerTermQuotesdata->measurement_units}}">
										<input type="hidden" value="{!! $getBuyerTermQuotesdata->id !!}" name="post_id[]">
									</div>
								@endforeach
							@endif
						</div>
						<!-- Table Row Ends Here -->
					</div>
					<!-- Table Ends Here -->
				</div>
			</div>
		@endif


        <!-- bid type section starts-->

        <div class="col-md-12 inner-block-bg inner-block-bg1">
        
        
        
			@if(Session::get('service_id') == COURIER)
							
							<div class="col-md-3 form-control-fld">
							
							<div class="col-md-8 padding-none">
								{!! Form::text('max_weight_accepted_text',$termQuotes->max_weight_accepted,['class'=>'form-control form-control1 numberVal','id'=>'max_weight_accepted','placeholder'=>'Maximum Weight Accepted*',($readonly == true) ? 'readonly' : '']) !!}
							</div>
							<div class="col-md-4 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days manage">
											{!! Form::select('units_max_weight',($volumeWeightTypes), $termQuotes->lkp_ict_weight_uom_id, ['id' => 'units_max_weight','class' => 'selectpicker bs-select-hidden',($readonly == true) ? 'disabled' : '']) !!}
											@if($readonly == true)
                        					<input type="hidden" value="{!! $termQuotes->lkp_ict_weight_uom_id !!}" name="units_max_weight">
                         					@endif
										</span>
									</div>	
								</div>
							</div>
							
							
							
							<div class="clearfix"></div>
							<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min</div>
										<div class="col-md-3 padding-left-none">Max</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none slabtable">

										<!-- Table Row Starts Here -->
            						<input type="hidden" name ='price_slap_hidden_value' id='price_slap_hidden_value' value='{{ $pricelabs_count }}'>			
										<div class="add-price-slap">
										 <div class="price-slap price-slap-update">
										 <?php $i = 1 ?>
										 @foreach($pricelabs as $key=>$pricelab)
										 <div class="add-price-slap table-row inner-block-bg" id="remove_item_{{$i}}" style="">
											{{--*/ $slab_min_rate =  "slab_min_rate" /*--}}
											{{--*/ $slab_max_rate = "slab_max_rate" /*--}}
											{{--*/ $price = "price" /*--}}
											<div class="col-md-3 padding-left-none">
											{!! Form::text("low_weight_salb_$i",$pricelab->slab_min_rate,['placeholder' => '0.00','class'=>'form-control form-control1',($readonly == true) ? 'readonly' : '','id'=>"low_weight_salb_$i"]) !!}
											</div>
											<div class="col-md-3 padding-left-none">
											{!! Form::text("high_weight_slab_$i",$pricelab->slab_max_rate,['placeholder' => '0.00','class'=>'form-control dynamic_high_weight form-control1 numberVal',($readonly == true) ? 'readonly' : '','id'=>"high_weight_slab_$i",'onblur'=>'checkPriceForInerment(this.value,this.id)']) !!}
											</div>
											@if($i == 1)
												<div class="col-md-3 padding-left-none">
												@if($readonly == true)
													<input type="button" class="btn add-box-buyer add-btn" disabled value="Add">
													@else
													<input type="button" class="btn add-box-buyer add-btn" value="Add">
													@endif
												</div>											
											@endif											
											
											@if($i == $pricelabs_count)
												@if($readonly == false)
													<div class="col-md-3 form-control-fld padding-left-none padding-top-7">
														<a class="remove-box-prices" href="#">
															<i class="fa fa-trash red" title="Delete"></i>
														</a>
		
													</div>
												@endif
											@endif
											<?php $i++ ?>
										  </div>
										@endforeach

										</div>
										
										</div>

										<!-- Table Row Ends Here -->

									</div>									
									@if($termQuotes->is_incremental == '1') 
										{{--*/ $is_incremental = true /*--}}
									@else
										{{--*/ $is_incremental = false /*--}}
									@endif
									
									<div class="col-md-5 form-control-fld padding-none">
										<div class="col-md-1 form-control-fld padding-top-7">
										<div class="checkbox_inline">
										<input type="hidden" value="{{ $termQuotes->is_incremental }}" id ='check_max_weight_assign' name="check_max_weight_assign">
										{!! Form::checkbox('check_max_weight', $termQuotes->is_incremental, $is_incremental,array('id'=>'check_max_weight','disabled'=>'disabled')) !!}
										<span class="lbl padding-8"></span></div>
										</div>

										<div class="col-md-5 form-control-fld padding-none">
											<div class="input-prepend">
												{!! Form::text('incremental_weight_text',$termQuotes->increment_weight,['class'=>'form-control form-control1 numberVal',($is_incremental == false) ? 'readonly' : '','id'=>'incremental_weight','placeholder'=>'Incremental Weight*']) !!}
											</div>
										</div>	

									</div>	
								</div>		

								<!-- Table Starts Here -->
							</div>
       				 @endif
        <div class="col-md-12 padding-left-none padding-right-none pad-top-20">

                <!--div class="col-md-3 form-control-fld">
                        <div class="normal-select">

                            <label class="col-md-4 pull-left padding-none" style="margin-top:9px;">Bid Type * :</label>
                            <div class="normal-select col-md-8 padding-none">
                                {!!	Form::select('bid_type',($bid_type), $termQuotes->lkp_bid_type_id ,['class' =>'selectpicker form_control','id'=>'bid_type']) !!}
                            </div>
                        </div>
                 </div-->
                
                <div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('last_bid_date', date("d/m/Y", strtotime($bidEndDates->bid_end_date)), ['id' => 'last_bid_date','class' => 'form-control calendar', 'placeholder' => 'Last Bid Date *', ($readonly == true) ? 'disabled' : '']) !!}		
								</div>
						   </div>							
						    <div class="col-md-3 form-control-fld">
								<div id="bid_time_icon_add" class="input-prepend date clsbid_close_time">
									<span class="add-on"><i class="fa fa-clock-o"></i></span>
									{!! Form::text('bid_close_time', $bidEndDates->bid_end_time, ['id' => 'bid_close_time','class' => 'form-control form-control1 clock timepicker disable-bg-white', 'placeholder' => 'Bid Closure Time *', ($readonly == true) ? 'disabled' : '']) !!}
								</div>
								<label for="bid_close_time" id="err_bid_close_time" class="error"></label>
							</div>
                
                
                
                <div class="clearfix"></div>
                <div class="form-control-fld">
                    {{--*/ $filescount = count($termConditionFiles) /*--}}
               @foreach($termConditionFiles as $termConditionFile)
                   @if($termConditionFile->file_name)
                        <span class="update_txt_test_buyer" id="file_{!! $termConditionFile->id !!}">{!! $termConditionFile->file_name !!}&nbsp;&nbsp;&nbsp;<a  onclick="javascript:deleteTermFile({{$termConditionFile->id}})"><i class="fa fa-trash red" title="Delete"></i></a></span>
                            <div class="clearfix"></div>
                    @endif
                @endforeach
                </div>
                <div class="clearfix"></div>
                <div class="form-control-fld"><span class="text-underline">Bid Terms & Conditions</span></div>

        </div>


        <!-- 	bid type section ends-->

        <!--file upload div starts-->
        <input type="hidden" name ='term_next_terms_count_search' id='term_next_terms_count_search' value='0'>

        <div class="documents-terms" style="display: <?php echo ($filescount < 5) ? "block" : "none"; ?>;">
        <div class="col-md-12 padding-none text-box">
        <span style='display:none;' class="box-number-delete">2</span>
            <div class="col-md-4 form-control-fld">

<!-- 	                			<input type="text" class="form-control form-control1 upload-control" readonly>  -->
                
                            <div class="upload-fld">
                                <button class="btn add-btn upload-browse-btn pull-right">Browse...</button>
                                <input type="file" name="terms_condtion_types_term_defualt" class="form-control form-control1 update_txt" value="" id="terms_condtion_types_term_defualt" <?php if($readonly==true) { ?> disabled='true' <?php } ?>/>
                            </div>
                        </div>
                        <div class="col-md-3 form-control-fld">


                    <input type="button" class="documents-add btn add-btn" value="Add +" <?php if($readonly==true) { ?> disabled='true' <?php } ?>>
                </div>
                <div class="clearfix"></div>
        </div>
        </div>

        <div class="col-md-9 form-control-fld">
              <textarea  class="form-control form-control1 clsFTLComments" maxlength="500" name="buyer_notes" id="buyer_notes" <?php if($readonly==true) { ?> disabled='true' <?php } ?>>{{$termQuotes->buyer_notes}}</textarea>
        </div>

        </div>

            <!--file upload div ends-->


        <div class="col-md-12 inner-block-bg inner-block-bg1">
            <div class="col-md-12 form-control-fld move-bottom-10 padding-left-none">
                <div class="radio-block">
                    <div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="term_post_public_update" <?php if($readonly==true) { ?> disabled='true' <?php } ?> @if($termQuotes->lkp_quote_access_id == 1) checked @endif> <label for="term_post_public_update"><span></span>Post Public</label></div>
                    <div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="term_post_private_update" class="create-posttype-service-ftl-term" @if($termQuotes->lkp_quote_access_id == 2) checked @endif> <label for="term_post_private_update"><span></span>Post Private</label></div>
                </div>
            </div>

            {{--*/ $private =  ($termQuotes->lkp_quote_access_id == 2) ? true : false; /*--}}

            <div class="col-md-4 padding-none private_post_term" style="display: <?php echo ($private == true) ? "block" : "none"; ?>;">

                <input type="text" id="term_seller_list_update" name="term_seller_list" class="form-control" placeholder="Seller Name (Auto Search)" />
                <script type="text/javascript">
                    $(document).ready(function() {
                        @if ($private === true)
                            $("#term_seller_list_update").tokenInput("/getTermSellerList", {
                                    prePopulate: [
                                            <?php if(isset($privateSellerNames) && !empty($privateSellerNames)){
                                                           foreach($privateSellerNames as $key=>$privateSellerName){ ?>
                                            {id: "<?php echo $privateSellerName->id ?>", name: "<?php echo $privateSellerName->username; ?>"},
                                        <?php }
                                         } ?>
                                    ]
                                });
                        @else
                            $("#term_seller_list_update").tokenInput("/getTermSellerList");
                        @endif



                         //update
                        $('#term_post_private_update').click(function() {
                            $(".private_post_term").show();
                            var seller_id_list = new Array();
                            var id = $('.term_request_rows').children().size();
                            if (id != 0) {
                                $("#showhidepost").css("display", "block");
                                $.ajax({
                                    url: '/getTermSellerList',
                                    type: "post",
                                    data: {
                                        'seller_list': seller_id_list,
                                        '_token': $('input[name=_token]').val()
                                    },
                                    success: function(data) {
                                        if (data != "") {
                                            $(".token-input-list").remove();
                                            $("#term_seller_list_update").tokenInput(data,{prePopulate: [
                                                    <?php if(isset($privateSellerNames) && !empty($privateSellerNames)){
                                                                   foreach($privateSellerNames as $key=>$privateSellerName){ ?>
                                                    {id: "<?php echo $privateSellerName->id ?>", name: "<?php echo $privateSellerName->username; ?>"},
                                                <?php }
                                                 } ?>
                                            ]});
                                        } else {
                                            $("#erroralertmodal .modal-body").html("No Sellers Available.");
                                            $("#erroralertmodal").modal({
                                                show: true
                                            });
                                            $('#term_post_private').prop('checked', false);
                                            $("#showhidepost").css("display", "none");
                                            return false;
                                        }
                                    },
                                    error: function(request, status, error) {
                                        $('#term_post_private').val(null);
                                        alert(error);
                                    },
                                });
                            } else {
                                $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
                                $("#erroralertmodal").modal({
                                    show: true
                                });
                                $('#term_post_private').prop('checked', false);
                                return false;
                            }
                        });
                        $('#term_post_public_update').click(function() {
                            $(".private_post_term").hide();
                            var id = $('.term_request_rows').children().size();
                            if (id == 0) {
                                $("#showhidepost").css("display", "none");
                                $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
                                $("#erroralertmodal").modal({
                                    show: true
                                });
                                $('#term_post_public').prop('checked', false);
                                return false;
                            } else {
                                $('#term_seller_list').val("");
                                $("#showhidepost").css("display", "none");
                            }
                        });


                        });
                </script>
                </div>



            <div class="clearfix"></div>
            <div class="check-box form-control-fld">
                <div class="normal-checkbox">
                  {!! Form::checkbox('agree', 1, false, ['class' => 'field','id'=>'agree', ($readonly == true) ? 'disabled' : '']) !!} <span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span></div>
                </div>
            </div>

    </div>

<div class="clearfix"></div>


<div class="col-md-12 padding-none">
<!-- 				{!! Form::submit('Save as Draft', ['name' => 'draft','class'=>'btn black-btn margin-top','id' => 'term_ftl_draft']) !!}	 -->

    <input type="hidden" name="confirm_but" id="confirm_but" value="">
    {!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote']) !!}
     @if($readonly == false)
    {!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote_draft']) !!}
    @endif
</div>

</div>	<!-- End custom div for FTl term srinu -->
<!-- End Term get quote form -->

</div> <!-- End container div here -->

</div> <!-- End Main braces -->
<script>
function val()
{
var selerId = document.getElementById("demo-input-local").value;
if (selerId == null || selerId == "") {
alert("Please enter seller name");
return false;
}
}
$(document).ready(function() {
var seller_id_list = new Array();
$.each( $( ".from_location" ), function() {
    var from_location_value =$(this).val();
    seller_id_list.unshift(from_location_value);

});
//getting buyer quote id check dupliactes form selected sseller table.
 var buyer_quote_id = $('#ftl_buyer_quoteid').val();

$('.token-input-delete-token').click(function(){
        $(this).parent().remove();
    });
$.ajax({
    url: '/getTermSellerList',
    type: "post",
    data: {
        'seller_list': seller_id_list,
        '_token': $('input[name=_token]').val()
    },
    success: function(data){
    //alert(data);
    if(data!="")
        {
            $("#term_seller_list").tokenInput(data);
        }
    else
        {
            alert("No Sellers Available");
            $('#post_private').prop('checked', false);
            $("#hideseller").css("display","none");
            return false;
        }
    },
    error : function(request, status, error) {
    $('#post_private').val(null);
    alert(error);
    },
});
});


</script>
{!! Form::close() !!}

@include('partials.footer')
@endsection