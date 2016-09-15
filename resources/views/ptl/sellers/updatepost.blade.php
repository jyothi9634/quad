@extends('app')
@section('content')
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif  


<div class="main">
	<div class="container">
		@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
	     	<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
				{{ Session::get('message_update_post') }}
			</p>
			</div>
		@endif
			@if(Session::has('transId_updated') && Session::get('transId_updated')!='')
	     	{{--*/ $transactionId = Session::get('transId_updated') /*--}}
			<script>
			$(document).ready(function(){
			$("#erroralertmodal .modal-body").html("Your post has been saved successfully. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the buyers online.");
     	   $("#erroralertmodal").modal({
               show: true
           }).one('click','.ok-btn',function (e){
        	   window.location="/sellerlist";
        	 
           });
		 });
			


</script>
		@endif
		
		
		@if(Session::get('service_id') == AIR_INTERNATIONAL)
		{{--*/ $str_from=  'From Airport*' /*--}}
        {{--*/ $str_to=  'To Airport*' /*--}}
        @elseif(Session::get('service_id') == OCEAN)
        {{--*/ $str_from=  'From Seaport*' /*--}}
        {{--*/ $str_to=  'To Seaport*' /*--}}
        @else
        {{--*/ $str_from=  "From Location with pin code / Zone*" /*--}}
        {{--*/ $str_to=  "To Location with pin code / Zone*" /*--}}
        @endif

		<span class="pull-left"><h1 class="page-title">Post @if(Session::get('service_id') == ROAD_PTL)
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
				@endif</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		<span class="pull-right"><a href="{{ $backToPostsUrl }}" class="back-link">Back to Posts</a></span>
		<div class="clearfix"></div>

		

		<div class="col-md-12 inner-block-bg single-layout padding-none">
		
		<div class="col-md-12 inner-block-bg inner-block-bg1 border-bottom-none margin-bottom-none padding-bottom-none">
			<div class="col-md-12 padding-none inner-form">
				{!! Form::open(['url' => '/ptl/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form-lines_ptl']) !!}
				<div class="col-md-12 padding-none">
					<div class="col-md-12 form-control-fld">
						<div class="padding-top1">
							<span class="data-head">Post Type : Spot</span>
						</div>

						{{--*/ $zone = ($seller_post_edit->lkp_ptl_post_type_id == 1) ? true : false /*--}}
						{{--*/ $location = ($seller_post_edit->lkp_ptl_post_type_id == 2) ? true : false /*--}}
						
						
					</div>

					@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != COURIER && Session::get('service_id') != OCEAN)
						<div class="col-md-12 form-control-fld">
	                        <div class="radio-block">
								<div class="radio_inline">{!! Form::radio('option_wise_ptl', 'Zone wise', $zone, ['id' => 'zone_wise_ptl','disabled'=>'disabled']) !!}<label for="zone_wise_ptl"><span></span>Zone wise</label></div>
								<div class="radio_inline">	
									 {!! Form::radio('option_wise_ptl', 'Location wise', $location, ['id' => 'location_wise_ptl','disabled'=>'disabled']) !!}<label for="location_wise_ptl"><span></span>Location wise</label>
								</div>	 
							</div>
						</div>
					@endif
					@if(Session::get('service_id') == COURIER)
					
					{{--*/ $domestic = ($seller_post_edit->lkp_courier_delivery_type_id == 1) ? true : false /*--}}
					{{--*/ $international = ($seller_post_edit->lkp_courier_delivery_type_id == 2) ? true : false /*--}}
						
					{{--*/ $documents = ($seller_post_edit->lkp_courier_type_id == 1) ? true : false /*--}}
					{{--*/ $parcel = ($seller_post_edit->lkp_courier_type_id == 2) ? true : false /*--}}
					
					
					<div class="col-md-3 form-control-fld">
	                        <div class="radio-block">
								<div class="radio_inline">{!! Form::radio('option_wise_ptl', 'Zone wise', $zone, ['id' => 'zone_wise_ptl','disabled'=>'disabled']) !!}<label for="zone_wise_ptl"><span></span>Zone wise</label></div>
								<div class="radio_inline">	
									 {!! Form::radio('option_wise_ptl', 'Location wise', $location, ['id' => 'location_wise_ptl','disabled'=>'disabled']) !!}<label for="location_wise_ptl"><span></span>Location wise</label>
								</div>	 
							</div>
						</div>
					<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'Domestic', $domestic, ['id' => 'domestic','disabled'=>'disabled']) !!} <label for="domestic"><span></span>Domestic</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('post_delivery_type', 'International', $international, ['id' => 'international','disabled'=>'disabled']) !!} <label for="international"><span></span>International</label>
											</div>
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Documents', $documents, ['id' => 'documents','disabled'=>'disabled']) !!} <label for="documents"><span></span>Documents</label>
											</div>
											<div class="radio_inline">
											{!! Form::radio('courier_types', 'Parcel', $parcel, ['id' => 'parcel','disabled'=>'disabled']) !!} <label for="parcel"><span></span>Parcel</label>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
					@endif
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							 {!! Form::text('valid_from', \App\Components\CommonComponent::convertDateDisplay($seller_post_edit->from_date), ['id' => 'datepicker','class' => 'calendar form-control from-date-control', 'placeholder' => 'Valid From*','disabled'=>'disabled']) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('valid_to', \App\Components\CommonComponent::convertDateDisplay($seller_post_edit->to_date), ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly'=>true,'placeholder' => 'Valid To*']) !!}
						</div>
					</div>

					<div class="clearfix"></div>

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', '', ['id' => 'from_location_ptl','class' => 'form-control', 'placeholder' => $str_from,'disabled'=>'disabled']) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', '', ['id' => 'to_location_ptl','class' => 'form-control', 'placeholder' => $str_to,'disabled'=>'disabled']) !!}
						</div>
					</div>

				@if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == COURIER)
					<div class="col-md-3 form-control-fld">
						<div class="col-md-7 padding-none">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
								{!! Form::text('transitdays',null,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays_ptl','placeholder'=>'Transit Days*']) !!}
								
							</div>
						</div>
						<div class="col-md-5 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_ptl', 'data-serviceId' => $serviceId]) !!}	
											</div>
								</span>
							</div>
						</div>
					
						
					</div>
					@else
					<div class="col-md-3 form-control-fld">
						<div class="col-md-7 padding-none">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
								{!! Form::text('transitdays',null,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays_ptl','placeholder'=>'Transit Days*']) !!}
								
							</div>
						</div>
						<div class="col-md-5 padding-none">
							<div class="input-prepend">
								<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_ptl', 'data-serviceId' => $serviceId]) !!}	
											</div>
								</span>
							</div>
						</div>
					
						
					</div>
					
					@endif
					@if(Session::get('service_id') != COURIER)
					<div class="col-md-3 padding-none">

						<div class="col-md-12 form-control-fld padding-left-none">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-rupee "></i></span>
							{!! Form::text('price',null,['class'=>'form-control numberVal fourdigitstwodecimals_deciVal','id'=>'price_ptl','placeholder'=>'Rate per kg (Rs)*']) !!}
						</div>	
						</div>
						
					</div>
					@endif

					{!! Form::hidden('current_row_id', '0', array('id' => 'current_row_id')) !!}
					
					<div class="col-md-1 form-control-fld padding-left-none text-right">
						 <input type="button" id="add_more_update_ptl" value="Update" class="btn add-btn" style="display: none;">
					</div>

			</div>
			{!! Form::close() !!}
</div>

</div>



			{!! Form::open(['url' => '/ptl/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form_ptl']) !!}
            {!! Form::hidden('labeltext[]', 'Cancellation Charges', array('id' => '')) !!}
            {!! Form::hidden('labeltext[]', 'Docket Charges', array('id' => '')) !!}
            @if(Session::get('service_id') == COURIER)
            	@if($domestic ==1)
            	{!! Form::hidden('post_or_delivery_type_id', '1', array('id' => 'post_or_delivery_type_id')) !!}
            	@else
            	{!! Form::hidden('post_or_delivery_type_id', '2', array('id' => 'post_or_delivery_type_id')) !!}
            	@endif
                {!! Form::hidden('courier_or_types_id', '', array('id' => 'courier_or_types_id')) !!}
            @endif
           
            {!! Form::hidden('valid_from_val', $seller_post_edit->from_date, array('id' => 'valid_from_val')) !!}
            {!! Form::hidden('valid_to_val', $seller_post_edit->to_date, array('id' => 'valid_to_val')) !!}
			{!! Form::hidden('post_type_id', $seller_post_edit->lkp_ptl_post_type_id, array()) !!}
			{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
			{!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
			{!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
			{!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
				<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>

				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
					<div class="col-md-12 padding-none">
						<div class="main-inner"> 
					

					<!-- Right Section Starts Here -->

					<div class="main-right">

						<!-- Table Starts Here -->

						<div class="table-div table-style1 padding-none">
							
							<!-- Table Head Starts Here -->

							<div class="table-heading inner-block-bg">
								<div class="col-md-3 padding-left-none">From Location</div>
								<div class="col-md-3 padding-left-none">To Location</div>
								@if(Session::get('service_id') != COURIER)
								<div class="col-md-3 padding-left-none">Rate Per KG</div>
								@endif
								<div class="col-md-2 padding-left-none">Transit Days</div>
								<div class="col-md-1 padding-left-none"></div>
							</div>
							<input type="hidden" id='next_add_more_id_ptl' value='0'>
							<!-- Table Head Ends Here -->

							<div class="table-data" id ="ptl_multi-line-itemes">
								

								<!-- Table Row Starts Here -->
								<div class="table-data request_rows_ptl" id="testDiv4_ptl">
									@foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
									<div class="table-row inner-block-bg request_row_ptl_{!! $seller_post_edit_action_line->id !!} "id="single_post_item_{!! $seller_post_edit_action_line->id !!}">
										<div class="col-md-3 padding-left-none from_location_text">{!! $seller_post_edit_action_line->from_locationcity !!}</div>
										<div class="col-md-3 padding-left-none to_location_text">{!! $seller_post_edit_action_line->to_locationcity !!}</div>
										@if(Session::get('service_id') != COURIER)
										<div class="col-md-3 padding-left-none price_text">{!! $seller_post_edit_action_line->price !!}</div>
										@endif
										<div class="col-md-2 padding-left-none transitdays_text">{!! $seller_post_edit_action_line->transitdays !!} {!! $seller_post_edit_action_line->units !!}</div>
										<div class="col-md-1 padding-none text-center">
										@if($seller_post_edit_action_line->lkp_post_status_id != 5 )
											<!-- a row_id="1" data-string="{!! $seller_post_edit_action_line->from_location_id !!}{!! $seller_post_edit_action_line->to_location_id !!}" class="remove_this_line remove"><i class="fa fa-trash red" title="Delete"></i></a -->
											<a href='javascript:void(0)' onclick="updatePtlpostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"><i class="fa fa-edit red" title="Edit"></i></a>
											@else
											Deleted
											@endif
										</div>
										<input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="post_id[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->from_location_id !!}" name="from_location[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->to_location_id !!}" name="to_location[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->transitdays !!}" name="transitdays[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->units !!}" name="units[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->price !!}" name="price[]">
									</div>
									@endforeach
								</div>
								<!-- Table Row Ends Here -->
							</div>



						</div>	

						<!-- Table Starts Here -->

						

					</div>

					<!-- Right Section Ends Here -->

				</div>
			</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-12 padding-none">
				<div class="col-md-3 form-control-fld">
					@if(Session::get('service_id') != COURIER)
					<h5 class="caption-head">&nbsp;</h5>
					@endif
					@if(Session::get('service_id') != COURIER)
					<div class="input-prepend">
						@if($seller_post_edit->kg_per_cft !=0)	
							@if(Session::get('service_id') == RAIL)	
	                    	{!! Form::text('kgpercft',$seller_post_edit->kg_per_cft,['class'=>'form-control form-control1 numberVal threedigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@elseif(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == AIR_DOMESTIC)
	                    	{!! Form::text('kgpercft',$seller_post_edit->kg_per_cft,['class'=>'form-control form-control1 numberVal fourdigitsfourdecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@else
	                    	{!! Form::text('kgpercft',$seller_post_edit->kg_per_cft,['class'=>'form-control form-control1 numberVal fourdigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@endif
                    	@else
	                    	@if(Session::get('service_id') == RAIL)	
	                    	{!! Form::text('kgpercft','',['class'=>'form-control form-control1 numberVal threedigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@elseif(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == AIR_DOMESTIC)
	                    	{!! Form::text('kgpercft',$seller_post_edit->kg_per_cft,['class'=>'form-control form-control1 numberVal fourdigitsfourdecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@else
	                    	{!! Form::text('kgpercft','',['class'=>'form-control form-control1 numberVal fourdigitsthreedecimals_deciVal','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
	                    	@endif
                    	@endif
					</div>	
					@endif
				</div>
				@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN && Session::get('service_id') != COURIER)
					<div class="col-md-9 form-control-fld">
						<h5 class="caption-head">Additional Charges</h5>
						<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							  {!! Form::text('pickup',$seller_post_edit->pickup_charges,['class'=>'form-control  form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'pickup_ptl','placeholder'=>'Pick Up*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
						</div>	
						</div>
						<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							{!! Form::text('delivery',$seller_post_edit->delivery_charges,['class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'delivery_ptl','placeholder'=>'Delivery*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
						</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							  {!! Form::text('oda',$seller_post_edit->oda_charges,['class'=>'form-control form-control1 numberVal fourdigitstwodecimals_deciVal','id'=>'oda_ptl','placeholder'=>'ODA*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
						</div>
						</div>
					</div>	
				@endif
			</div>

			<div class="clearfix"></div>
			@if(Session::get('service_id') == COURIER)
			
			
			@if($seller_post_edit->lkp_ict_weight_uom_id==1)
			{{--*/ $class='form-control form-control1 twodigitstwodecimals_deciVal numberVal' /*--}}
			@else
			{{--*/ $class='form-control form-control1 fourdigitstwodecimals_deciVal numberVal' /*--}}
			@endif
			
			<h2 class="filter-head1">Courier</h2>
			<div class="col-md-3 form-control-fld">	
				<div class="input-prepend">
								{!! Form::text('conversion_factor_text',$seller_post_edit->conversion_factor,['class'=>'form-control twodigitstwodecimals_deciVal form-control1 numberVal','id'=>'conversion_factor','placeholder'=>'Conversion Factor (Kg/CCM)*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
							</div>
						</div>
							<div class="col-md-3 form-control-fld">
							
							<div class="col-md-8 padding-none">
								{!! Form::text('max_weight_accepted_text',$seller_post_edit->max_weight_accepted,['class'=>$class,'id'=>'max_weight_accepted','placeholder'=>'Maximum Weight Accepted*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
							</div>
							<div class="col-md-4 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days manage">
											{!! Form::select('units_max_weight',($volumeWeightTypes), $seller_post_edit->lkp_ict_weight_uom_id, ['id' => 'units_max_weight','class' => 'selectpicker bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '','onChange'=>'return GetWeightType()']) !!}
											@if($seller_post_edit->lkp_post_status_id == 2)
                        					<input type="hidden" value="{!! $seller_post_edit->lkp_ict_weight_uom_id !!}" name="units_max_weight">
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
										<div class="col-md-3 padding-left-none">Price</div>
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
											{!! Form::text("low_weight_salb_$i",$pricelab->slab_min_rate,['placeholder' => '0.00','class'=>'form-control form-control1','id'=>"low_weight_salb_$i"]) !!}
											</div>
											<div class="col-md-3 padding-left-none">
											{!! Form::text("high_weight_slab_$i",$pricelab->slab_max_rate,['placeholder' => '0.00','class'=>'form-control form-control1 dynamic_high_weight numberVal','id'=>"high_weight_slab_$i",'onblur'=>'checkPriceForInerment(this.value,this.id)']) !!}
											</div>
											<div class="col-md-3 padding-left-none">
											{!! Form::text("price_slab_$i",$pricelab->price,['placeholder' => '0.00','class'=>'form-control form-control1 fivedigitstwodecimals_deciVal dynamic_prices numberVal','id'=>"price_slab_$i"]) !!}
											</div>
											@if($i == 1)
												<div class="col-md-3 padding-left-none">
													<input type="button" class="btn add-box add-btn" value="Add">
												</div>											
											@endif											
											
											@if($i == $pricelabs_count)
											<div class="col-md-3 form-control-fld padding-left-none padding-top-7">
												<a class="remove-box-prices" href="#">
													<i class="fa fa-trash red" title="Delete"></i>
												</a>

											</div>
											@endif
											<?php $i++ ?>
										  </div>
										@endforeach

										</div>
										
										</div>

										<!-- Table Row Ends Here -->

									</div>									
									@if($seller_post_edit->is_incremental == '1') 
										{{--*/ $is_incremental = true /*--}}
									@else
										{{--*/ $is_incremental = false /*--}}
									@endif
									
									<div class="col-md-5 form-control-fld padding-none">
										<div class="col-md-1 form-control-fld padding-top-7">
										<div class="checkbox_inline">
										<input type="hidden" value="{{ $seller_post_edit->is_incremental }}" id ='check_max_weight_assign' name="check_max_weight_assign">
										{!! Form::checkbox('check_max_weight', $seller_post_edit->is_incremental, $is_incremental,array('id'=>'check_max_weight','disabled'=>'disabled')) !!}
										<span class="lbl padding-8"></span></div>
										</div>

										<div class="col-md-5 form-control-fld padding-none">
											<div class="input-prepend">
												{!! Form::text('incremental_weight_text',$seller_post_edit->increment_weight,['class'=>'form-control form-control1 numberVal','id'=>'incremental_weight','placeholder'=>'Incremental Weight*',($seller_post_edit->is_incremental != 1) ? 'readonly' : '']) !!}
											</div>
										</div>	
										<div class="col-md-5 form-control-fld">
											<div class="input-prepend">
												{!! Form::text('rate_per_increment_text',$seller_post_edit->rate_per_increment,['class'=>'form-control fourdigitstwodecimals_deciVal form-control1 numberVal','id'=>'rate_per_increment','placeholder'=>'Rate Per Increment*',($seller_post_edit->is_incremental != 1) ? 'readonly' : '']) !!}
											</div>
										</div>
									</div>	
								</div>	

								<!-- Table Starts Here -->
							</div>
							<h5 class="caption-head">Additional Charges</h5>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('fuel_surcharge_text',$seller_post_edit->fuel_surcharge,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'fuel_surcharge','placeholder'=>'Fuel Surcharge*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>

							</div>	
						
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('check_on_delivery_text',$seller_post_edit->cod_charge,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'check_on_delivery','placeholder'=>'Check on Delivery*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>
							</div>	
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('freight_collect_text',$seller_post_edit->freight_collect_charge,['class'=>'form-control form-control1 fivedigitstwodecimals_deciVal numberVal','id'=>'freight_collect','placeholder'=>'Freight Collect*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('arc_text',$seller_post_edit->arc_charge,['class'=>'form-control form-control1 twodigitstwodecimals_deciVal numberVal','id'=>'arc','placeholder'=>'ARC*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('maximum_value_text',$seller_post_edit->maximum_value,['class'=>'form-control form-control1 fivedigitstwodecimals_deciVal numberVal','id'=>'maximum_value','placeholder'=>'Maximum Value*',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>
							</div>
							<div class="clearfix"></div>
							@endif
			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
                          {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), $seller_post_edit->tracking, ['id' => 'tracking_ptl','class' => 'selectpicker form-control', ($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                        @if($seller_post_edit->lkp_post_status_id == 2)
                        <input type="hidden" value="{!! $seller_post_edit->tracking !!}" name="tracking">
                         @endif
				</div>
			</div>
		
			<div class="clearfix"></div>
			
			<h2 class="filter-head1">Payment Terms</h2>

			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
					 {!! Form::select('paymentterms', ($paymentterms), $seller_post_edit->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'payment_options',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                    @if($seller_post_edit->lkp_post_status_id == 2)
                    <input type="hidden" value="{!! $seller_post_edit->lkp_payment_mode_id !!}" name="paymentterms">
                    @endif

				</div>
			</div>


			<div class="col-md-12 form-control-fld">
				<div class="check-block" id = 'show_advanced_period' style="display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 1) ? "block" : "none"; ?>">
	                
	                @if($seller_post_edit->lkp_post_status_id == 2)
	                <input type="hidden" value="{!! $seller_post_edit->accept_payment_netbanking !!}" name="accept_payment[]">
	                    		@if($seller_post_edit->accept_payment_netbanking == 1)
	                           <div class="checkbox_inline"> {!! Form::checkbox('accept_payment_ptl[]', 1, true, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">NEFT/RTGS</span></div>
	                            @else
	                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 1, false, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">NEFT/RTGS</span></div>
	                            @endif
	                 @else
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 1, $seller_post_edit->accept_payment_netbanking, false, ['class' => 'accept_payment_ptl']) !!}&nbsp;<span class="lbl padding-8">NEFT/RTGS</span></div>
	                 @endif
	                

	                
	                @if($seller_post_edit->lkp_post_status_id == 2)
	                <input type="hidden" value="{!! $seller_post_edit->accept_payment_credit !!}" name="accept_payment[]">
	                		@if($seller_post_edit->accept_payment_credit == 1)
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 2, true, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Credit Card</span></div>
	                        @else
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 2, false, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Credit Card</span></div>
	                        @endif
	                 @else
	                       <div class="checkbox_inline"> {!! Form::checkbox('accept_payment_ptl[]', 2, $seller_post_edit->accept_payment_credit, false, ['class' => 'accept_payment_ptl']) !!}&nbsp;<span class="lbl padding-8">Credit Card</span></div>
	                 @endif
	               


	                
	                @if($seller_post_edit->lkp_post_status_id == 2)
	                <input type="hidden" value="{!! $seller_post_edit->accept_payment_debit !!}" name="accept_payment[]">
	                		@if($seller_post_edit->accept_payment_debit == 1)
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 3,true, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Debit Card</span></div>
	                        @else
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 3, false, ['class' => 'accept_payment_ptl','disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Debit Card</span></div>
	                         @endif
	                 @else
	                        <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ptl[]', 3, $seller_post_edit->accept_payment_debit, false, ['class' => 'accept_payment_ptl']) !!}&nbsp;<span class="lbl padding-8">Debit Card</span></div>
	                 @endif
	                

	            </div>
        	</div>

                  @if($seller_post_edit->credit_period_units == 'Days')
                      {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                  @elseif($seller_post_edit->credit_period_units == 'Weeks')
                      {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriodWeeks' /*--}}
                  @else
                      {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                  @endif


			<div class="col-md-12 form-control-fld" style ='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 4) ? "block" : "none"; ?>;' id = 'show_credit_period'>
				<div class="col-md-3 form-control-fld padding-left-none">
				<div class="col-md-7 padding-none">
					<div class="input-prepend">
					{!! Form::text('credit_period_ptl',$seller_post_edit->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
					
				</div>
				</div>
				<div class="col-md-5 padding-none">
					<div class="input-prepend">
						<span class="add-on unit-days">
						<div class="normal-select">
							{!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
	                        @if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->credit_period_units !!}" name="credit_period_units">
							@endif
						</div>
					</span>
					</div>
				</div>
				
				</div>
				<div class="col-md-12 padding-none">
					@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->accept_credit_netbanking !!}" name="accept_credit_netbanking">
						@if($seller_post_edit->accept_credit_netbanking == 1)
                       <div class="checkbox_inline">{!! Form::checkbox('accept_credit_netbanking[]', 1,true, ['disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Net Banking</span></div>
                       @else
                      <div class="checkbox_inline"> {!! Form::checkbox('accept_credit_netbanking[]', 1, false, ['disabled'=>'disabled']) !!}&nbsp;<span class="lbl padding-8">Net Banking</span></div>
                   		@endif
                    @else   
                    <div class="checkbox_inline">{!! Form::checkbox('accept_credit_netbanking[]', 1,$seller_post_edit->accept_credit_netbanking, false) !!}&nbsp;<span class="lbl padding-8">Net Banking </span></div>         
                    @endif

                    @if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_credit_cheque !!}" name="accept_credit_cheque">
							@if($seller_post_edit->accept_credit_cheque == 1)
                            <div class="checkbox_inline">{!! Form::checkbox('accept_credit_netbanking[]', 2, true, ['disabled'=>'disabled']) !!}&nbsp; <span class="lbl padding-8">Cheque / DD</span></div>
                            @else
                            <div class="checkbox_inline">{!! Form::checkbox('accept_credit_netbanking[]', 2, false, ['disabled'=>'disabled']) !!}&nbsp; <span class="lbl padding-8">Cheque / DD</span></div>
                            @endif
							@else
                                     <div class="checkbox_inline">{!! Form::checkbox('accept_credit_netbanking[]', 2,$seller_post_edit->accept_credit_cheque, false) !!}&nbsp; <span class="lbl padding-8">Cheque / DD</span></div>
                            @endif

				</div>
			</div>

</div>
<div class="clearfix"></div>
				<div class="col-md-12 inner-block-bg inner-block-bg1">

					<h2 class="filter-head1">Additional Charges</h2>
					<div class="my-form">
					<div class="text-box form-control-fld terms-and-conditions-block">
						<div class="col-md-3 padding-none tc-block-fld">
							<div class="input-prepend">
							{!! Form::text('terms_condtion_types1',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 update_txt numberVal fourdigitstwodecimals_deciVal']) !!}
							<span class="add-on unit">Rs</span>
							</div>
						</div>
						<div class="col-md-3 tc-block-btn"></div>
					</div>
					</div>
				<div class="my-form">
					<div class="text-box form-control-fld terms-and-conditions-block"><span style='display:none;' class="box-number">2</span>
						<div class="col-md-3 padding-none tc-block-fld">
							<div class="input-prepend">
							 {!! Form::text('terms_condtion_types2',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 update_txt numberVal fourdigitstwodecimals_deciVal']) !!}
							 <span class="add-on unit">Rs</span>
							</div>
							</div>
						<div class="col-md-3 tc-block-btn">

							<input type="button" class="add-box btn add-btn" value="Add">

							</div>
					</div>
				
					@for ($i = 1; $i <= 3; $i++)
						{{--*/ $text =  "other_charge{$i}_text" /*--}}
						{{--*/ $price = "other_charge{$i}_price" /*--}}
						@if(($seller_post_edit->$text != "" || $seller_post_edit->$price != "") && ($seller_post_edit->$text != "" || $seller_post_edit->$price != "0.00"))
							<div class="text-box form-control-fld terms-and-conditions-block" style="">
								<div class="col-md-4 col-sm-4 col-xs-5 padding-left-none labelcharges">
								<div class="input-prepend">
									{!! Form::text("labeltext_$i",$seller_post_edit->$text,['placeholder' => 'Other Charges','class'=>'form-control labelcharges form-control1 dynamic_labelcharges']) !!}
								</div>
								</div>
								<div class="col-md-3 col-sm-3 col-xs-4  padding-left-none mobile-padding-none">
									<div class="input-prepend">
									{!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control update_txt_test  form-control1 update_txt dynamic_validations numberVal fourdigitstwodecimals_deciVal']) !!}
									<span class="add-on unit">Rs</span>
								</div>
								</div>
								@if($seller_post_edit->lkp_post_status_id == 1)
								<a href="#" class="remove-box col-md-2 margin-top-6" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a>
								@endif
							</div>
						@endif
					@endfor
					<input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='{{$i-1}}'>
					</div>
					<div class="col-md-6 form-control-fld">
						{!! Form::textarea('terms_conditions',$seller_post_edit->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','placeholder'=>'Notes to Terms &amp; Conditions (optional)','id'=>'terms_conditions_ptl', 'rows' => 2, 'cols' => 57,($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
					</div>
				</div>



				<div class="col-md-12 inner-block-bg inner-block-bg1">
					<div class="col-md-12 form-control-fld">
				@if($seller_post_edit->lkp_post_status_id == 1)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
						@else
						{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

				 @if($seller_post_edit->lkp_post_status_id == 2)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
					@else
						{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

			</div>

            @if($seller_post_edit->lkp_post_status_id == 2)
				<input type="hidden" value="{!! $seller_post_edit->lkp_access_id !!}" name="optradio">
			@endif
					<div class="col-md-3 form-control-fld">
					<div class="demo-input_buyers" style='display:<?php echo ($private == true) ? "block" : "none"; ?>'>
						<?php
						$selected  = "";
						foreach($selectedbuyers as $selectedbuyer){ 
							$selected .= ",".$selectedbuyer->buyer_id;
						} ?>
						<input type="hidden" id="demo_input_select_hidden" name="buyer_list_for_sellers_hidden" value="<?php echo $selected; ?>" />
						<select id="demo_input_select" class="tokenize-sample" name="buyer_list_for_sellers" multiple="multiple">
							<?php foreach($selectedbuyers as $selectedbuyer){ ?>
							 @if($selectedbuyer->principal_place != '')
								<option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->principal_place.' '.$selectedbuyer->buyer_id; ?></option>
								@else
								<option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->buyer_id; ?></option>
							@endif
							<?php } ?>
						</select>
					</div>
					</div>
					<div class="clearfix"></div>
					<div class="check-box form-control-fld margin-none">

						@if($seller_post_edit->lkp_post_status_id == 1)
                             {!! Form::checkbox('agree', 1, false,array('id'=>'agree_ptl')) !!}&nbsp;<span class="lbl padding-8">Accept Terms &amp; Conditions ( Digital Contract )
                         </span>
                             @else
                             {!! Form::checkbox('agree', 1, true,array('id'=>'agree_ptl',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '')) !!}&nbsp;<span class="lbl padding-8">Accept Terms &amp; Conditions ( Digital Contract )</span>
                             @endif
					</div>
				</div>
				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					{!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_ptl_edit','onclick'=>"updatepoststatus(1)"]) !!}
					@if($seller_post_edit->lkp_post_status_id == 1)
					{!! Form::submit('Save as Draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_ptl_edit','onclick'=>"updatepoststatus(0)"]) !!}
					 @endif
					
				</div>


			


		</div>






		{!! Form::close() !!}


	</div> <!-- Container -->
</div> <!-- main -->		
@include('partials.footer')
@endsection
